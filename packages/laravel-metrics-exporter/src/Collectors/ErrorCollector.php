<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Collectors;

use Beacon\MetricsExporter\Storage\StorageInterface;

class ErrorCollector implements CollectorInterface
{
    public function __construct(
        private StorageInterface $storage
    ) {}

    public function name(): string
    {
        return 'errors';
    }

    public function isEnabled(): bool
    {
        return config('metrics-exporter.collectors.errors', true);
    }

    public function collect(): array
    {
        return $this->storage->getErrorStats();
    }
}
