<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Collectors;

use Beacon\MetricsExporter\Storage\StorageInterface;

class RequestCollector implements CollectorInterface
{
    public function __construct(
        private StorageInterface $storage
    ) {}

    public function name(): string
    {
        return 'requests';
    }

    public function isEnabled(): bool
    {
        return config('metrics-exporter.collectors.requests', true);
    }

    public function collect(): array
    {
        return $this->storage->getRequestStats();
    }
}
