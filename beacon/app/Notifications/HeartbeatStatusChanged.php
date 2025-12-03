<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Heartbeat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class HeartbeatStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Heartbeat $heartbeat,
        public string $previousStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusEmoji = match ($this->newStatus) {
            Heartbeat::STATUS_HEALTHY => '✅',
            Heartbeat::STATUS_LATE => '⚠️',
            Heartbeat::STATUS_MISSING => '❌',
            default => '❓',
        };

        return (new MailMessage)
            ->subject("{$statusEmoji} Heartbeat Alert: {$this->heartbeat->name}")
            ->greeting("Heartbeat Status Changed")
            ->line("The heartbeat **{$this->heartbeat->name}** has changed status.")
            ->line("**Previous Status:** " . ucfirst($this->previousStatus))
            ->line("**New Status:** " . ucfirst($this->newStatus))
            ->when($this->heartbeat->last_ping_at, function ($message) {
                return $message->line("**Last Ping:** " . $this->heartbeat->last_ping_at->diffForHumans());
            })
            ->when($this->heartbeat->next_expected_at, function ($message) {
                return $message->line("**Expected At:** " . $this->heartbeat->next_expected_at->format('Y-m-d H:i:s'));
            })
            ->action('View Heartbeat', route('heartbeats.show', $this->heartbeat->id))
            ->line('Please check your cron job or scheduled task.');
    }
}
