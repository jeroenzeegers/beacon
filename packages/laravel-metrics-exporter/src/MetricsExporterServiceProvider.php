<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter;

use Beacon\MetricsExporter\Http\Middleware\RequestMetricsMiddleware;
use Beacon\MetricsExporter\Storage\ArrayStorage;
use Beacon\MetricsExporter\Storage\FileStorage;
use Beacon\MetricsExporter\Storage\RedisStorage;
use Beacon\MetricsExporter\Storage\StorageInterface;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class MetricsExporterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/metrics-exporter.php',
            'metrics-exporter'
        );

        // Register storage singleton
        $this->app->singleton(StorageInterface::class, function ($app) {
            $driver = config('metrics-exporter.storage.driver', 'redis');
            $prefix = config('metrics-exporter.storage.prefix', 'beacon_metrics:');
            $retention = config('metrics-exporter.retention_minutes', 60);

            return match ($driver) {
                'redis' => new RedisStorage(
                    config('metrics-exporter.storage.redis.connection', 'default'),
                    $prefix,
                    $retention
                ),
                'file' => new FileStorage(
                    config('metrics-exporter.storage.file.path', storage_path('framework/metrics')),
                    $prefix,
                    $retention
                ),
                'array' => new ArrayStorage($prefix),
                default => new ArrayStorage($prefix),
            };
        });

        // Register metrics manager singleton
        $this->app->singleton(MetricsManager::class, function ($app) {
            return new MetricsManager(
                $app->make(StorageInterface::class)
            );
        });
    }

    public function boot(): void
    {
        if (!config('metrics-exporter.enabled', true)) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/metrics-exporter.php' => config_path('metrics-exporter.php'),
        ], 'metrics-exporter-config');

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // Register request tracking middleware
        if (config('metrics-exporter.collectors.requests', true)) {
            $this->registerRequestMiddleware();
        }

        // Register database query listener
        if (config('metrics-exporter.collectors.database', true)) {
            $this->registerDatabaseListener();
        }

        // Register cache event listeners
        if (config('metrics-exporter.collectors.cache', true)) {
            $this->registerCacheListeners();
        }

        // Register error listener
        if (config('metrics-exporter.collectors.errors', true)) {
            $this->registerErrorListener();
        }
    }

    private function registerRequestMiddleware(): void
    {
        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);

        $kernel->pushMiddleware(RequestMetricsMiddleware::class);
    }

    private function registerDatabaseListener(): void
    {
        DB::listen(function ($query) {
            $storage = $this->app->make(StorageInterface::class);
            $slowThreshold = config('metrics-exporter.database.slow_query_threshold_ms', 100);
            $isSlow = $query->time > $slowThreshold;

            $storage->recordQuery($query->time, $isSlow);
        });
    }

    private function registerCacheListeners(): void
    {
        Event::listen('cache.hit', function () {
            $storage = $this->app->make(StorageInterface::class);
            $storage->recordCacheHit(true);
        });

        Event::listen('cache.missed', function () {
            $storage = $this->app->make(StorageInterface::class);
            $storage->recordCacheHit(false);
        });

        // Laravel 10+ uses different cache events
        Event::listen(\Illuminate\Cache\Events\CacheHit::class, function () {
            $storage = $this->app->make(StorageInterface::class);
            $storage->recordCacheHit(true);
        });

        Event::listen(\Illuminate\Cache\Events\CacheMissed::class, function () {
            $storage = $this->app->make(StorageInterface::class);
            $storage->recordCacheHit(false);
        });
    }

    private function registerErrorListener(): void
    {
        Event::listen(MessageLogged::class, function (MessageLogged $event) {
            $errorLevels = ['error', 'critical', 'alert', 'emergency'];

            if (in_array($event->level, $errorLevels, true)) {
                $storage = $this->app->make(StorageInterface::class);
                $storage->recordError($event->level);
            }
        });
    }
}
