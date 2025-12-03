<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Team $team,
        private readonly Carbon $expiresAt
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = now()->diffInDays($this->expiresAt);

        return (new MailMessage())
            ->subject("Your Beacon trial expires in {$daysLeft} days")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your free trial for **{$this->team->name}** will expire in {$daysLeft} days.")
            ->line('Subscribe now to keep access to all features including:')
            ->line('- Unlimited monitor checks')
            ->line('- Advanced alerting')
            ->line('- Team collaboration')
            ->line('- API access')
            ->action('Subscribe Now', route('billing.plans'))
            ->line('If you have any questions, feel free to reach out to our support team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'team_id' => $this->team->id,
            'team_name' => $this->team->name,
            'expires_at' => $this->expiresAt->toIso8601String(),
        ];
    }
}
