<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MonitorCheckCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Monitor $monitor,
        public MonitorCheck $check
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
                'status' => $this->monitor->status,
                'uptime_percentage' => $this->monitor->uptime_percentage,
            ],
            'check' => [
                'id' => $this->check->id,
                'is_successful' => $this->check->is_successful,
                'response_time' => $this->check->response_time,
                'status_code' => $this->check->status_code,
                'checked_at' => $this->check->created_at->toIso8601String(),
            ],
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'monitor.check.completed';
    }
}
