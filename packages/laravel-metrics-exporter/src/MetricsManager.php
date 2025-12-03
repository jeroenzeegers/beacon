<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter;

use Beacon\MetricsExporter\Collectors\CacheCollector;
use Beacon\MetricsExporter\Collectors\CollectorInterface;
use Beacon\MetricsExporter\Collectors\DatabaseCollector;
use Beacon\MetricsExporter\Collectors\ErrorCollector;
use Beacon\MetricsExporter\Collectors\QueueCollector;
use Beacon\MetricsExporter\Collectors\RequestCollector;
use Beacon\MetricsExporter\Collectors\SystemCollector;
use Beacon\MetricsExporter\Storage\StorageInterface;
use Carbon\Carbon;

class MetricsManager
{
    /** @var array<string, float|int> */
    private array $customGauges = [];

    /** @var CollectorInterface[] */
    private array $collectors = [];

    public function __construct(
        private StorageInterface $storage
    ) {
        $this->registerDefaultCollectors();
    }

    /**
     * Collect all metrics from enabled collectors.
     */
    public function collect(): array
    {
        $metrics = [
            'collected_at' => Carbon::now()->toIso8601String(),
            'app' => $this->getAppInfo(),
        ];

        foreach ($this->collectors as $collector) {
            if ($collector->isEnabled()) {
                $metrics[$collector->name()] = $collector->collect();
            }
        }

        // Add custom metrics if any exist
        $customMetrics = $this->getCustomMetrics();
        if (!empty($customMetrics['gauges']) || !empty($customMetrics['counters'])) {
            $metrics['custom'] = $customMetrics;
        }

        return $metrics;
    }

    /**
     * Set a custom gauge value.
     */
    public function gauge(string $name, float|int $value): void
    {
        $this->customGauges[$name] = $value;
        $this->storage->gauge('custom:' . $name, $value);
    }

    /**
     * Increment a custom counter.
     */
    public function increment(string $name, int $amount = 1): int
    {
        return $this->storage->increment('custom:counter:' . $name, $amount);
    }

    /**
     * Record a timing value.
     */
    public function timing(string $name, float $milliseconds): void
    {
        $this->storage->timing('custom:timing:' . $name, $milliseconds);
    }

    /**
     * Measure the execution time of a callback.
     */
    public function measure(string $name, callable $callback): mixed
    {
        $start = microtime(true);
        $result = $callback();
        $elapsed = (microtime(true) - $start) * 1000;

        $this->timing($name, $elapsed);

        return $result;
    }

    /**
     * Get the storage instance.
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * Register a custom collector.
     */
    public function registerCollector(CollectorInterface $collector): void
    {
        $this->collectors[$collector->name()] = $collector;
    }

    /**
     * Flush all metrics data.
     */
    public function flush(): void
    {
        $this->storage->flush();
        $this->customGauges = [];
    }

    private function registerDefaultCollectors(): void
    {
        $this->collectors = [
            'requests' => new RequestCollector($this->storage),
            'system' => new SystemCollector(),
            'database' => new DatabaseCollector($this->storage),
            'cache' => new CacheCollector($this->storage),
            'queue' => new QueueCollector(),
            'errors' => new ErrorCollector($this->storage),
        ];
    }

    private function getAppInfo(): array
    {
        return [
            'name' => config('app.name', 'Laravel'),
            'environment' => config('app.env', 'production'),
            'debug' => config('app.debug', false),
        ];
    }

    private function getCustomMetrics(): array
    {
        $gauges = $this->storage->getAll('gauge:custom:');
        $counters = $this->storage->getAll('custom:counter:');

        // Clean up keys
        $cleanGauges = [];
        foreach ($gauges as $key => $value) {
            $cleanKey = str_replace('gauge:custom:', '', $key);
            $cleanGauges[$cleanKey] = $value;
        }

        $cleanCounters = [];
        foreach ($counters as $key => $value) {
            $cleanKey = str_replace('custom:counter:', '', $key);
            $cleanCounters[$cleanKey] = $value;
        }

        // Include in-memory gauges
        foreach ($this->customGauges as $name => $value) {
            $cleanGauges[$name] = $value;
        }

        return [
            'gauges' => $cleanGauges,
            'counters' => $cleanCounters,
        ];
    }
}
