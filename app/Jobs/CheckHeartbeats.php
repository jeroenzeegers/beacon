<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Heartbeat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckHeartbeats implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue('heartbeats');
    }

    public function handle(): void
    {
        $heartbeats = Heartbeat::where('is_active', true)
            ->whereNotNull('last_ping_at')
            ->get();

        foreach ($heartbeats as $heartbeat) {
            $newStatus = $heartbeat->checkStatus();

            if ($heartbeat->status !== $newStatus) {
                $previousStatus = $heartbeat->status;
                $heartbeat->update(['status' => $newStatus]);

                Log::info('Heartbeat status changed', [
                    'heartbeat_id' => $heartbeat->id,
                    'name' => $heartbeat->name,
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus,
                ]);

                // Dispatch alert if status changed to late or missing
                if (in_array($newStatus, [Heartbeat::STATUS_LATE, Heartbeat::STATUS_MISSING])) {
                    SendHeartbeatAlert::dispatch($heartbeat, $previousStatus, $newStatus);
                }
            }
        }
    }
}
