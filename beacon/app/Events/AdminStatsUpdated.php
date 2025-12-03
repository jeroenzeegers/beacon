<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Monitor;
use App\Models\Team;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminStatsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public array $stats = []
    ) {
        if (empty($this->stats)) {
            $this->stats = $this->calculateStats();
        }
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.stats'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'stats' => $this->stats,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'stats.updated';
    }

    /**
     * Calculate current stats.
     */
    private function calculateStats(): array
    {
        return [
            'total_users' => User::count(),
            'users_today' => User::whereDate('created_at', today())->count(),
            'total_teams' => Team::count(),
            'total_monitors' => Monitor::count(),
            'active_monitors' => Monitor::where('is_active', true)->count(),
            'monitors_down' => Monitor::where('is_active', true)->where('status', 'down')->count(),
        ];
    }
}
