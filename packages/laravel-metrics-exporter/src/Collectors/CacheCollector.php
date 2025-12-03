<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Collectors;

use Beacon\MetricsExporter\Storage\StorageInterface;

class CacheCollector implements CollectorInterface
{
    public function __construct(
        private StorageInterface $storage
    ) {}

    public function name(): string
    {
        return 'cache';
    }

    public function isEnabled(): bool
    {
        return config('metrics-exporter.collectors.cache', true);
    }

    public function collect(): array
    {
        $stats = $this->storage->getCacheStats();

        return array_merge($stats, [
            'driver' => config('cache.default'),
        ]);
    }
}
