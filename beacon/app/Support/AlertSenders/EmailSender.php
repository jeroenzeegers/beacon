<?php

declare(strict_types=1);

namespace App\Support\AlertSenders;

use App\Models\AlertChannel;
use App\Models\Monitor;
use Illuminate\Support\Facades\Mail;

class EmailSender implements AlertSenderInterface
{
    public function send(AlertChannel $channel, Monitor $monitor, string $trigger, string $message): bool
    {
        $email = $channel->getConfigValue('email');

        if (!$email) {
            return false;
        }

        Mail::raw($message, function ($mail) use ($email, $monitor, $trigger) {
            $mail->to($email)
                ->subject($this->getSubject($monitor, $trigger));
        });

        return true;
    }

    public function supports(string $type): bool
    {
        return $type === AlertChannel::TYPE_EMAIL;
    }

    private function getSubject(Monitor $monitor, string $trigger): string
    {
        $statusEmoji = match ($trigger) {
            'monitor_down' => '🔴',
            'monitor_up' => '🟢',
            'monitor_degraded' => '🟡',
            'ssl_expiring' => '⚠️',
            default => '📢',
        };

        return "{$statusEmoji} [{$monitor->name}] " . ucfirst(str_replace('_', ' ', $trigger));
    }
}
