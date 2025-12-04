<?php

declare(strict_types=1);

namespace App\Support\AlertSenders;

use App\Models\AlertChannel;
use App\Models\Monitor;
use Illuminate\Support\Facades\Http;

class SlackSender implements AlertSenderInterface
{
    public function send(AlertChannel $channel, Monitor $monitor, string $trigger, string $message): bool
    {
        $webhookUrl = $channel->getConfigValue('webhook_url');

        if (! $webhookUrl) {
            return false;
        }

        $response = Http::post($webhookUrl, [
            'blocks' => $this->buildBlocks($monitor, $trigger, $message),
        ]);

        return $response->successful();
    }

    public function supports(string $type): bool
    {
        return $type === AlertChannel::TYPE_SLACK;
    }

    private function buildBlocks(Monitor $monitor, string $trigger, string $message): array
    {
        $color = match ($trigger) {
            'monitor_down' => '#dc3545',
            'monitor_up' => '#28a745',
            'monitor_degraded' => '#ffc107',
            default => '#6c757d',
        };

        $statusEmoji = match ($trigger) {
            'monitor_down' => ':red_circle:',
            'monitor_up' => ':large_green_circle:',
            'monitor_degraded' => ':large_yellow_circle:',
            'ssl_expiring' => ':warning:',
            default => ':bell:',
        };

        return [
            [
                'type' => 'header',
                'text' => [
                    'type' => 'plain_text',
                    'text' => "{$statusEmoji} Monitor Alert: {$monitor->name}",
                    'emoji' => true,
                ],
            ],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => $message,
                ],
            ],
            [
                'type' => 'context',
                'elements' => [
                    [
                        'type' => 'mrkdwn',
                        'text' => "*Target:* {$monitor->target}",
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => "*Type:* {$monitor->type}",
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => '*Trigger:* '.str_replace('_', ' ', $trigger),
                    ],
                ],
            ],
        ];
    }
}
