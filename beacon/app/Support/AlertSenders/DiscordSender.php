<?php

declare(strict_types=1);

namespace App\Support\AlertSenders;

use App\Models\AlertChannel;
use App\Models\Monitor;
use Illuminate\Support\Facades\Http;

class DiscordSender implements AlertSenderInterface
{
    public function send(AlertChannel $channel, Monitor $monitor, string $trigger, string $message): bool
    {
        $webhookUrl = $channel->getConfigValue('webhook_url');

        if (! $webhookUrl) {
            return false;
        }

        $response = Http::post($webhookUrl, [
            'embeds' => [$this->buildEmbed($monitor, $trigger, $message)],
        ]);

        return $response->successful();
    }

    public function supports(string $type): bool
    {
        return $type === AlertChannel::TYPE_DISCORD;
    }

    private function buildEmbed(Monitor $monitor, string $trigger, string $message): array
    {
        $color = match ($trigger) {
            'monitor_down' => 0xDC3545,
            'monitor_up' => 0x28A745,
            'monitor_degraded' => 0xFFC107,
            default => 0x6C757D,
        };

        $statusEmoji = match ($trigger) {
            'monitor_down' => '🔴',
            'monitor_up' => '🟢',
            'monitor_degraded' => '🟡',
            'ssl_expiring' => '⚠️',
            default => '📢',
        };

        return [
            'title' => "{$statusEmoji} Monitor Alert: {$monitor->name}",
            'description' => $message,
            'color' => $color,
            'fields' => [
                [
                    'name' => 'Target',
                    'value' => $monitor->target,
                    'inline' => true,
                ],
                [
                    'name' => 'Type',
                    'value' => ucfirst($monitor->type),
                    'inline' => true,
                ],
                [
                    'name' => 'Trigger',
                    'value' => ucfirst(str_replace('_', ' ', $trigger)),
                    'inline' => true,
                ],
            ],
            'timestamp' => now()->toIso8601String(),
            'footer' => [
                'text' => 'Beacon Monitoring',
            ],
        ];
    }
}
