<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Monitor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MonitorStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Monitor $monitor,
        public string $previousStatus,
        public string $newStatus
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('team.' . $this->monitor->team_id),
            new PrivateChannel('monitor.' . $this->monitor->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'monitor' => [
                'id' => $this->monitor->id,
                'name' => $this->monitor->name,
                'url' => $this->monitor->url,
                'type' => $this->monitor->type,
                'status' => $this->newStatus,
                'previous_status' => $this->previousStatus,
                'last_checked_at' => $this->monitor->last_checked_at?->toIso8601String(),
            ],
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'monitor.status.changed';
    }
}
