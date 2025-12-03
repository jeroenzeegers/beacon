<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AlertChannel;
use App\Models\Monitor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationService
{
    public function sendAlert(AlertChannel $channel, Monitor $monitor, string $previousStatus, string $newStatus): bool
    {
        if ($channel->type !== 'slack' || empty($channel->webhook_url)) {
            return false;
        }

        $color = $this->getColorForStatus($newStatus);
        $emoji = $this->getEmojiForStatus($newStatus);

        $payload = [
            'username' => 'Beacon Monitor',
            'icon_emoji' => ':beacon:',
            'attachments' => [
                [
                    'color' => $color,
                    'blocks' => [
                        [
                            'type' => 'header',
                            'text' => [
                                'type' => 'plain_text',
                                'text' => "{$emoji} Monitor Status Changed",
                                'emoji' => true,
                            ],
                        ],
                        [
                            'type' => 'section',
                            'fields' => [
                                [
                                    'type' => 'mrkdwn',
                                    'text' => "*Monitor:*\n{$monitor->name}",
                                ],
                                [
                                    'type' => 'mrkdwn',
                                    'text' => "*Type:*\n".strtoupper($monitor->type),
                                ],
                                [
                                    'type' => 'mrkdwn',
                                    'text' => "*Previous Status:*\n".ucfirst($previousStatus),
                                ],
                                [
                                    'type' => 'mrkdwn',
                                    'text' => "*New Status:*\n".ucfirst($newStatus),
                                ],
                            ],
                        ],
                        [
                            'type' => 'section',
                            'text' => [
                                'type' => 'mrkdwn',
                                'text' => "*Target:*\n`{$monitor->target}`",
                            ],
                        ],
                        [
                            'type' => 'context',
                            'elements' => [
                                [
                                    'type' => 'mrkdwn',
                                    'text' => 'Detected at '.now()->format('Y-m-d H:i:s T'),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if (! empty($channel->slack_channel)) {
            $payload['channel'] = $channel->slack_channel;
        }

        try {
            $response = Http::post($channel->webhook_url, $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Slack notification failed', [
                'channel_id' => $channel->id,
                'monitor_id' => $monitor->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendTestMessage(AlertChannel $channel): bool
    {
        if ($channel->type !== 'slack' || empty($channel->webhook_url)) {
            return false;
        }

        $payload = [
            'username' => 'Beacon Monitor',
            'icon_emoji' => ':beacon:',
            'text' => ':white_check_mark: *Test notification from Beacon*\n\nYour Slack integration is working correctly!',
        ];

        if (! empty($channel->slack_channel)) {
            $payload['channel'] = $channel->slack_channel;
        }

        try {
            $response = Http::post($channel->webhook_url, $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Slack test notification failed', [
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function getColorForStatus(string $status): string
    {
        return match ($status) {
            'up' => '#22c55e',
            'degraded' => '#f59e0b',
            'down' => '#ef4444',
            default => '#6b7280',
        };
    }

    private function getEmojiForStatus(string $status): string
    {
        return match ($status) {
            'up' => ':white_check_mark:',
            'degraded' => ':warning:',
            'down' => ':x:',
            default => ':grey_question:',
        };
    }
}
