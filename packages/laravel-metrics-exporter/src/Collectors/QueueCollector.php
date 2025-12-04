<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Collectors;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class QueueCollector implements CollectorInterface
{
    public function name(): string
    {
        return 'queue';
    }

    public function isEnabled(): bool
    {
        return config('metrics-exporter.collectors.queue', true);
    }

    public function collect(): array
    {
        $driver = config('queue.default');
        $queues = config('metrics-exporter.queue.queues', ['default']);

        return [
            'driver' => $driver,
            'pending' => $this->getPendingCount($driver, $queues),
            'failed' => $this->getFailedCount(),
            'queues' => $this->getQueueDetails($driver, $queues),
        ];
    }

    private function getPendingCount(string $driver, array $queues): int
    {
        $total = 0;

        foreach ($queues as $queue) {
            $total += $this->getQueueSize($driver, $queue);
        }

        return $total;
    }

    private function getQueueSize(string $driver, string $queue): int
    {
        try {
            return match ($driver) {
                'redis' => $this->getRedisQueueSize($queue),
                'database' => $this->getDatabaseQueueSize($queue),
                'sync' => 0,
                default => 0,
            };
        } catch (\Exception) {
            return 0;
        }
    }

    private function getRedisQueueSize(string $queue): int
    {
        $connection = config('queue.connections.redis.connection', 'default');
        $prefix = config('queue.connections.redis.queue', 'default');

        $queueName = $prefix === 'default' ? "queues:{$queue}" : "{$prefix}:{$queue}";

        try {
            return Redis::connection($connection)->llen($queueName);
        } catch (\Exception) {
            return 0;
        }
    }

    private function getDatabaseQueueSize(string $queue): int
    {
        $table = config('queue.connections.database.table', 'jobs');

        try {
            return DB::table($table)
                ->where('queue', $queue)
                ->whereNull('reserved_at')
                ->count();
        } catch (\Exception) {
            return 0;
        }
    }

    private function getFailedCount(): int
    {
        try {
            $table = config('queue.failed.table', 'failed_jobs');

            return DB::table($table)->count();
        } catch (\Exception) {
            return 0;
        }
    }

    private function getQueueDetails(string $driver, array $queues): array
    {
        $details = [];

        foreach ($queues as $queue) {
            $details[$queue] = [
                'pending' => $this->getQueueSize($driver, $queue),
            ];
        }

        return $details;
    }
}
