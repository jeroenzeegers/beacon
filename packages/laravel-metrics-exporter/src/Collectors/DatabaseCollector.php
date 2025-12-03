<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Collectors;

use Beacon\MetricsExporter\Storage\StorageInterface;

class DatabaseCollector implements CollectorInterface
{
    public function __construct(
        private StorageInterface $storage
    ) {}

    public function name(): string
    {
        return 'database';
    }

    public function isEnabled(): bool
    {
        return config('metrics-exporter.collectors.database', true);
    }

    public function collect(): array
    {
        $stats = $this->storage->getQueryStats();

        return array_merge($stats, [
            'connection_name' => config('database.default'),
        ]);
    }
}
