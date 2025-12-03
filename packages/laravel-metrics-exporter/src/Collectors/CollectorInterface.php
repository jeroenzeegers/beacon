<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Collectors;

interface CollectorInterface
{
    /**
     * Get the collector name/identifier.
     */
    public function name(): string;

    /**
     * Check if the collector is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Collect metrics and return as an array.
     */
    public function collect(): array;
}
