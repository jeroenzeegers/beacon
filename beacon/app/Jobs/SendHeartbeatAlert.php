<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AlertChannel;
use App\Models\Heartbeat;
use App\Notifications\HeartbeatStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendHeartbeatAlert implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Heartbeat $heartbeat,
        public string $previousStatus,
        public string $newStatus
    ) {
        $this->onQueue('alerts');
    }

    public function handle(): void
    {
        $team = $this->heartbeat->team;

        // Get alert channels for this team
        $channels = AlertChannel::where('team_id', $team->id)
            ->where('is_active', true)
            ->get();

        foreach ($channels as $channel) {
            try {
                Notification::route($channel->type, $channel->config)
                    ->notify(new HeartbeatStatusChanged(
                        $this->heartbeat,
                        $this->previousStatus,
                        $this->newStatus
                    ));

                Log::info('Heartbeat alert sent', [
                    'heartbeat_id' => $this->heartbeat->id,
                    'channel_id' => $channel->id,
                    'channel_type' => $channel->type,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send heartbeat alert', [
                    'heartbeat_id' => $this->heartbeat->id,
                    'channel_id' => $channel->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
