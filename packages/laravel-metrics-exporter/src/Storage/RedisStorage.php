<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Storage;

use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

class RedisStorage implements StorageInterface
{
    private Connection $redis;

    private string $prefix;

    private int $ttl;

    public function __construct(string $connection = 'default', string $prefix = 'beacon_metrics:', int $retentionMinutes = 60)
    {
        $this->redis = Redis::connection($connection);
        $this->prefix = $prefix;
        $this->ttl = $retentionMinutes * 60;
    }

    public function increment(string $key, int $amount = 1): int
    {
        $fullKey = $this->prefix.$key;
        $value = $this->redis->incrby($fullKey, $amount);
        $this->redis->expire($fullKey, $this->ttl);

        return $value;
    }

    public function gauge(string $key, float|int $value): void
    {
        $fullKey = $this->prefix.'gauge:'.$key;
        $this->redis->set($fullKey, $value);
        $this->redis->expire($fullKey, $this->ttl);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->redis->get($this->prefix.$key);

        return $value !== null ? $value : $default;
    }

    public function getAll(string $prefix = ''): array
    {
        $pattern = $this->prefix.$prefix.'*';
        $keys = $this->redis->keys($pattern);
        $result = [];

        foreach ($keys as $key) {
            $shortKey = str_replace($this->prefix, '', $key);
            $result[$shortKey] = $this->redis->get($key);
        }

        return $result;
    }

    public function timing(string $key, float $milliseconds): void
    {
        $fullKey = $this->prefix.'timing:'.$key;

        // Store count and total for calculating average
        $this->redis->hincrby($fullKey, 'count', 1);
        $this->redis->hincrbyfloat($fullKey, 'total', $milliseconds);
        $this->redis->expire($fullKey, $this->ttl);
    }

    public function getTimingStats(string $key): array
    {
        $fullKey = $this->prefix.'timing:'.$key;
        $data = $this->redis->hgetall($fullKey);

        $count = (int) ($data['count'] ?? 0);
        $total = (float) ($data['total'] ?? 0);

        return [
            'count' => $count,
            'total' => $total,
            'avg' => $count > 0 ? round($total / $count, 2) : 0,
        ];
    }

    public function recordRequest(int $statusCode, float $responseTimeMs, ?string $route = null): void
    {
        $minute = date('Y-m-d-H-i');
        $statusGroup = (int) floor($statusCode / 100).'xx';

        // Increment total requests
        $this->increment('requests:total');
        $this->increment('requests:minute:'.$minute);

        // Increment status code group
        $this->increment('requests:status:'.$statusGroup);

        // Record response time
        $this->timing('requests:response_time', $responseTimeMs);

        // Track slow requests
        $slowThreshold = config('metrics-exporter.requests.slow_threshold_ms', 1000);
        if ($responseTimeMs > $slowThreshold) {
            $this->increment('requests:slow');
        }

        // Track per-route stats if enabled
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

        // Get status code breakdown
        $byStatus = [
            '2xx' => (int) $this->get('requests:status:2xx', 0),
            '3xx' => (int) $this->get('requests:status:3xx', 0),
            '4xx' => (int) $this->get('requests:status:4xx', 0),
            '5xx' => (int) $this->get('requests:status:5xx', 0),
        ];

        // Get per-route stats
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
        $keys = $this->redis->keys($this->prefix.'*');

        foreach ($keys as $key) {
            $this->redis->del($key);
        }
    }

    private function getRouteStats(): array
    {
        $maxRoutes = config('metrics-exporter.requests.max_routes', 100);
        $routes = [];

        $keys = $this->redis->keys($this->prefix.'requests:route:*');

        foreach (array_slice($keys, 0, $maxRoutes) as $key) {
            $route = str_replace($this->prefix.'requests:route:', '', $key);
            $routes[$route] = (int) $this->redis->get($key);
        }

        // Sort by count descending
        arsort($routes);

        return $routes;
    }

    private function sanitizeRouteName(string $route): string
    {
        // Replace special characters with underscores for Redis key safety
        return preg_replace('/[^a-zA-Z0-9_\-\/]/', '_', $route);
    }
}
