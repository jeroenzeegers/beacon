<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Team $team
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Beacon trial has expired')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your free trial for **{$this->team->name}** has expired.")
            ->line('Your account has been downgraded to the free plan with limited features.')
            ->line('Subscribe now to restore full access to:')
            ->line('- More monitors and projects')
            ->line('- Advanced alerting channels')
            ->line('- Team collaboration')
            ->line('- API access')
            ->line('- Longer data retention')
            ->action('View Plans', route('billing.plans'))
            ->line('We hope you enjoyed your trial. If you have any feedback, we\'d love to hear from you!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'team_id' => $this->team->id,
            'team_name' => $this->team->name,
        ];
    }
}
