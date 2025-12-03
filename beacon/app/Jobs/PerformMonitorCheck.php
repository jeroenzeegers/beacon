<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use App\Support\Checkers\CheckerFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;

class PerformMonitorCheck implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;
    public int $timeout = 120;

    public function __construct(
        public Monitor $monitor
    ) {
        $this->onQueue('monitors');
    }

    public function middleware(): array
    {
        // Prevent duplicate checks for the same monitor
        return [
            new WithoutOverlapping($this->monitor->id),
        ];
    }

    public function handle(CheckerFactory $checkerFactory): void
    {
        if (!$this->monitor->is_active) {
            Log::debug("Monitor {$this->monitor->id} is inactive, skipping check");
            return;
        }

        $previousStatus = $this->monitor->status;

        try {
            $result = $checkerFactory->check($this->monitor);

            // Record the check
            $this->monitor->recordCheck($result->status, $result->toArray());

            Log::info("Monitor check completed", [
                'monitor_id' => $this->monitor->id,
                'monitor_name' => $this->monitor->name,
                'status' => $result->status,
                'response_time' => $result->responseTime,
            ]);

            // Dispatch alert if status changed
            if ($previousStatus !== $this->monitor->fresh()->status) {
                $this->handleStatusChange($previousStatus, $this->monitor->fresh()->status);
            }
        } catch (\Exception $e) {
            Log::error("Monitor check failed", [
                'monitor_id' => $this->monitor->id,
                'error' => $e->getMessage(),
            ]);

            // Record as failure
            $this->monitor->recordCheck(Monitor::STATUS_DOWN, [
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    private function handleStatusChange(string $previousStatus, string $newStatus): void
    {
        Log::info("Monitor status changed", [
            'monitor_id' => $this->monitor->id,
            'monitor_name' => $this->monitor->name,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
        ]);

        SendAlert::dispatch($this->monitor, $previousStatus, $newStatus);
    }
}
