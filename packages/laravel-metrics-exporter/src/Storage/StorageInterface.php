<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Storage;

interface StorageInterface
{
    /**
     * Increment a counter by a given amount.
     */
    public function increment(string $key, int $amount = 1): int;

    /**
     * Set a gauge value.
     */
    public function gauge(string $key, float|int $value): void;

    /**
     * Get a single value.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Get all values with a given prefix.
     */
    public function getAll(string $prefix = ''): array;

    /**
     * Record a timing value (for averages).
     */
    public function timing(string $key, float $milliseconds): void;

    /**
     * Get timing statistics (count, total, avg).
     */
    public function getTimingStats(string $key): array;

    /**
     * Record an HTTP request.
     */
    public function recordRequest(int $statusCode, float $responseTimeMs, ?string $route = null): void;

    /**
     * Get request statistics.
     */
    public function getRequestStats(): array;

    /**
     * Record a database query.
     */
    public function recordQuery(float $timeMs, bool $isSlow = false): void;

    /**
     * Get database query statistics.
     */
    public function getQueryStats(): array;

    /**
     * Record a cache hit or miss.
     */
    public function recordCacheHit(bool $hit): void;

    /**
     * Get cache statistics.
     */
    public function getCacheStats(): array;

    /**
     * Record an error by level.
     */
    public function recordError(string $level): void;

    /**
     * Get error statistics.
     */
    public function getErrorStats(): array;

    /**
     * Flush all metrics data.
     */
    public function flush(): void;
}
