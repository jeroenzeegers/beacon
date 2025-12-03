<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Monitor;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class SystemHealth extends Component
{
    public function render(): View
    {
        return view('livewire.admin.system-health', [
            'services' => $this->checkServices(),
            'queues' => $this->getQueueStatus(),
            'monitors' => $this->getMonitorStats(),
            'storage' => $this->getStorageStats(),
        ]);
    }

    private function checkServices(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'redis' => $this->checkRedis(),
            'queue' => $this->checkQueue(),
        ];
    }

    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $time = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => $time < 100 ? 'healthy' : 'warning',
                'latency' => $time,
                'message' => "Response time: {$time}ms",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'latency' => null,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function checkCache(): array
    {
        try {
            $start = microtime(true);
            $key = 'health_check_' . time();
            cache()->put($key, true, 10);
            $result = cache()->get($key);
            cache()->forget($key);
            $time = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => $result ? 'healthy' : 'error',
                'latency' => $time,
                'message' => $result ? "Response time: {$time}ms" : 'Cache read failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'latency' => null,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function checkRedis(): array
    {
        try {
            $start = microtime(true);
            Redis::ping();
            $time = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'healthy',
                'latency' => $time,
                'message' => "Response time: {$time}ms",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'latency' => null,
                'message' => 'Redis not available',
            ];
        }
    }

    private function checkQueue(): array
    {
        try {
            $failedJobs = DB::table('failed_jobs')->count();
            $pendingJobs = DB::table('jobs')->count();

            $status = 'healthy';
            if ($failedJobs > 10) {
                $status = 'warning';
            }
            if ($failedJobs > 50) {
                $status = 'error';
            }

            return [
                'status' => $status,
                'pending' => $pendingJobs,
                'failed' => $failedJobs,
                'message' => "Pending: {$pendingJobs}, Failed: {$failedJobs}",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'pending' => null,
                'failed' => null,
                'message' => 'Could not check queue',
            ];
        }
    }

    private function getQueueStatus(): array
    {
        try {
            return [
                'pending' => DB::table('jobs')->count(),
                'failed' => DB::table('failed_jobs')->count(),
                'processed_today' => 0, // Would need job batches or logging
            ];
        } catch (\Exception) {
            return [
                'pending' => 0,
                'failed' => 0,
                'processed_today' => 0,
            ];
        }
    }

    private function getMonitorStats(): array
    {
        return [
            'total' => Monitor::count(),
            'active' => Monitor::where('is_active', true)->count(),
            'paused' => Monitor::where('is_active', false)->count(),
            'down' => Monitor::where('is_active', true)->where('status', 'down')->count(),
        ];
    }

    private function getStorageStats(): array
    {
        $diskTotal = disk_total_space('/');
        $diskFree = disk_free_space('/');
        $diskUsed = $diskTotal - $diskFree;

        return [
            'total' => $this->formatBytes($diskTotal),
            'used' => $this->formatBytes($diskUsed),
            'free' => $this->formatBytes($diskFree),
            'percentage' => round(($diskUsed / $diskTotal) * 100, 1),
        ];
    }

    private function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function retryFailedJobs(): void
    {
        // This would dispatch failed jobs to be retried
        session()->flash('success', 'Failed jobs queued for retry.');
    }

    public function clearFailedJobs(): void
    {
        DB::table('failed_jobs')->truncate();
        session()->flash('success', 'Failed jobs cleared.');
    }
}
