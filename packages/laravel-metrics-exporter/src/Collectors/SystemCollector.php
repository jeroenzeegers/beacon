<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Collectors;

class SystemCollector implements CollectorInterface
{
    public function name(): string
    {
        return 'system';
    }

    public function isEnabled(): bool
    {
        return config('metrics-exporter.collectors.system', true);
    }

    public function collect(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'memory_peak_mb' => round($memoryPeak / 1024 / 1024, 2),
            'memory_limit_mb' => $memoryLimit,
            'cpu_load' => $this->getCpuLoad(),
            'disk_free_gb' => $this->getDiskFree(),
            'disk_total_gb' => $this->getDiskTotal(),
            'opcache_enabled' => $this->isOpcacheEnabled(),
            'opcache_stats' => $this->getOpcacheStats(),
        ];
    }

    private function parseMemoryLimit(string $limit): float
    {
        if ($limit === '-1') {
            return -1;
        }

        $unit = strtolower(substr($limit, -1));
        $value = (int) substr($limit, 0, -1);

        return match ($unit) {
            'g' => $value * 1024,
            'm' => $value,
            'k' => $value / 1024,
            default => (int) $limit / 1024 / 1024,
        };
    }

    private function getCpuLoad(): ?array
    {
        if (!function_exists('sys_getloadavg')) {
            return null;
        }

        $load = sys_getloadavg();

        return $load !== false ? array_map(fn ($v) => round($v, 2), $load) : null;
    }

    private function getDiskFree(): ?float
    {
        $path = base_path();
        $free = @disk_free_space($path);

        return $free !== false ? round($free / 1024 / 1024 / 1024, 2) : null;
    }

    private function getDiskTotal(): ?float
    {
        $path = base_path();
        $total = @disk_total_space($path);

        return $total !== false ? round($total / 1024 / 1024 / 1024, 2) : null;
    }

    private function isOpcacheEnabled(): bool
    {
        if (!function_exists('opcache_get_status')) {
            return false;
        }

        $status = @opcache_get_status(false);

        return $status !== false && ($status['opcache_enabled'] ?? false);
    }

    private function getOpcacheStats(): ?array
    {
        if (!function_exists('opcache_get_status')) {
            return null;
        }

        $status = @opcache_get_status(false);

        if ($status === false) {
            return null;
        }

        $memory = $status['memory_usage'] ?? [];
        $stats = $status['opcache_statistics'] ?? [];

        return [
            'memory_used_mb' => isset($memory['used_memory']) ? round($memory['used_memory'] / 1024 / 1024, 2) : null,
            'memory_free_mb' => isset($memory['free_memory']) ? round($memory['free_memory'] / 1024 / 1024, 2) : null,
            'hit_rate' => isset($stats['opcache_hit_rate']) ? round($stats['opcache_hit_rate'], 2) : null,
            'scripts_cached' => $stats['num_cached_scripts'] ?? null,
        ];
    }
}
