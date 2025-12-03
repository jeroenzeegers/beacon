<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Facades;

use Beacon\MetricsExporter\MetricsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array collect()
 * @method static void gauge(string $name, float|int $value)
 * @method static int increment(string $name, int $amount = 1)
 * @method static void timing(string $name, float $milliseconds)
 * @method static mixed measure(string $name, callable $callback)
 * @method static void flush()
 * @method static \Beacon\MetricsExporter\Storage\StorageInterface getStorage()
 * @method static void registerCollector(\Beacon\MetricsExporter\Collectors\CollectorInterface $collector)
 *
 * @see \Beacon\MetricsExporter\MetricsManager
 */
class Metrics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MetricsManager::class;
    }
}
