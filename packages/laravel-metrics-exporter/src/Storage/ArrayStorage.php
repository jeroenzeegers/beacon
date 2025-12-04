<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Storage;

/**
 * In-memory storage for testing purposes.
 * Data is not persisted between requests.
 */
class ArrayStorage implements StorageInterface
{
    private array $counters = [];

    private array $gauges = [];

    private array $timings = [];

    private string $prefix;

    public function __construct(string $prefix = 'beacon_metrics:')
    {
        $this->prefix = $prefix;
    }

    public function increment(string $key, int $amount = 1): int
    {
        $fullKey = $this->prefix.$key;

        if (! isset($this->counters[$fullKey])) {
            $this->counters[$fullKey] = 0;
        }

        $this->counters[$fullKey] += $amount;

        return $this->counters[$fullKey];
    }

    public function gauge(string $key, float|int $value): void
    {
        $fullKey = $this->prefix.'gauge:'.$key;
        $this->gauges[$fullKey] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $fullKey = $this->prefix.$key;

        return $this->counters[$fullKey] ?? $this->gauges[$fullKey] ?? $default;
    }

    public function getAll(string $prefix = ''): array
    {
        $fullPrefix = $this->prefix.$prefix;
        $result = [];

        foreach ($this->counters as $key => $value) {
            if (str_starts_with($key, $fullPrefix)) {
                $shortKey = str_replace($this->prefix, '', $key);
                $result[$shortKey] = $value;
            }
        }

        foreach ($this->gauges as $key => $value) {
            if (str_starts_with($key, $fullPrefix)) {
                $shortKey = str_replace($this->prefix, '', $key);
                $result[$shortKey] = $value;
            }
        }

        return $result;
    }

    public function timing(string $key, float $milliseconds): void
    {
        $fullKey = $this->prefix.'timing:'.$key;

        if (! isset($this->timings[$fullKey])) {
            $this->timings[$fullKey] = ['count' => 0, 'total' => 0];
        }

        $this->timings[$fullKey]['count']++;
        $this->timings[$fullKey]['total'] += $milliseconds;
    }

    public function getTimingStats(string $key): array
    {
        $fullKey = $this->prefix.'timing:'.$key;
        $data = $this->timings[$fullKey] ?? ['count' => 0, 'total' => 0];

        return [
            'count' => $data['count'],
            'total' => $data['total'],
            'avg' => $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0,
        ];
    }

    public function recordRequest(int $statusCode, float $responseTimeMs, ?string $route = null): void
    {
        $minute = date('Y-m-d-H-i');
        $statusGroup = (int) floor($statusCode / 100).'xx';

        $this->increment('requests:total');
        $this->increment('requests:minute:'.$minute);
        $this->increment('requests:status:'.$statusGroup);
        $this->timing('requests:response_time', $responseTimeMs);

        $slowThreshold = config('metrics-exporter.requests.slow_threshold_ms', 1000);
        if ($responseTimeMs > $slowThreshold) {
            $this->increment('requests:slow');
        }

        if ($route && config('metrics-exporter.requests.track_routes', true)) {
            $this->increment('requests:route:'.$this->sanitizeRouteName($route));
        }
    }

    public function getRequestStats(): array
    {
        $total = (int) $this->get('requests:total', 0);
        $minute = date('Y-m-d-H-i');
        $perMinute = (int) $this->get('requests:minute:'.$minute, 0);
        $timing = $this->getTimingStats('requests:response_time');

        $byStatus = [
            '2xx' => (int) $this->get('requests:status:2xx', 0),
            '3xx' => (int) $this->get('requests:status:3xx', 0),
            '4xx' => (int) $this->get('requests:status:4xx', 0),
            '5xx' => (int) $this->get('requests:status:5xx', 0),
        ];

        $routes = $this->getRouteStats();

        return [
            'total' => $total,
            'per_minute' => $perMinute,
            'by_status' => $byStatus,
            'avg_response_time_ms' => $timing['avg'],
            'slow_requests' => (int) $this->get('requests:slow', 0),
            'by_route' => $routes,
        ];
    }

    public function recordQuery(float $timeMs, bool $isSlow = false): void
    {
        $minute = date('Y-m-d-H-i');

        $this->increment('database:total');
        $this->increment('database:minute:'.$minute);
        $this->timing('database:query_time', $timeMs);

        if ($isSlow) {
            $this->increment('database:slow');
        }
    }

    public function getQueryStats(): array
    {
        $total = (int) $this->get('database:total', 0);
        $minute = date('Y-m-d-H-i');
        $perMinute = (int) $this->get('database:minute:'.$minute, 0);
        $timing = $this->getTimingStats('database:query_time');

        return [
            'queries_total' => $total,
            'queries_per_minute' => $perMinute,
            'slow_queries' => (int) $this->get('database:slow', 0),
            'avg_query_time_ms' => $timing['avg'],
        ];
    }

    public function recordCacheHit(bool $hit): void
    {
        if ($hit) {
            $this->increment('cache:hits');
        } else {
            $this->increment('cache:misses');
        }
    }

    public function getCacheStats(): array
    {
        $hits = (int) $this->get('cache:hits', 0);
        $misses = (int) $this->get('cache:misses', 0);
        $total = $hits + $misses;

        return [
            'hits' => $hits,
            'misses' => $misses,
            'hit_ratio' => $total > 0 ? round($hits / $total, 4) : 0,
        ];
    }

    public function recordError(string $level): void
    {
        $this->increment('errors:total');
        $this->increment('errors:level:'.$level);

        $minute = date('Y-m-d-H-i');
        $this->increment('errors:minute:'.$minute);
    }

    public function getErrorStats(): array
    {
        $total = (int) $this->get('errors:total', 0);
        $minute = date('Y-m-d-H-i');
        $perMinute = (int) $this->get('errors:minute:'.$minute, 0);

        return [
            'total' => $total,
            'per_minute' => $perMinute,
            'by_level' => [
                'error' => (int) $this->get('errors:level:error', 0),
                'critical' => (int) $this->get('errors:level:critical', 0),
                'alert' => (int) $this->get('errors:level:alert', 0),
                'emergency' => (int) $this->get('errors:level:emergency', 0),
            ],
        ];
    }

    public function flush(): void
    {
        $this->counters = [];
        $this->gauges = [];
        $this->timings = [];
    }

    private function getRouteStats(): array
    {
        $maxRoutes = config('metrics-exporter.requests.max_routes', 100);
        $routes = [];
        $prefix = $this->prefix.'requests:route:';

        foreach ($this->counters as $key => $value) {
            if (str_starts_with($key, $prefix)) {
                $route = str_replace($prefix, '', $key);
                $routes[$route] = $value;
            }
        }

        arsort($routes);

        return array_slice($routes, 0, $maxRoutes, true);
    }

    private function sanitizeRouteName(string $route): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-\/]/', '_', $route);
    }
}
