<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AlertChannel;
use App\Models\Monitor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordNotificationService
{
    public function sendAlert(AlertChannel $channel, Monitor $monitor, string $previousStatus, string $newStatus): bool
    {
        if ($channel->type !== 'discord' || empty($channel->webhook_url)) {
            return false;
        }

        $color = $this->getColorForStatus($newStatus);
        $emoji = $this->getEmojiForStatus($newStatus);

        $payload = [
            'username' => $channel->discord_username ?? 'Beacon Monitor',
            'avatar_url' => 'https://beacon.app/images/logo.png',
            'embeds' => [
                [
                    'title' => "{$emoji} Monitor Status Changed",
                    'color' => $color,
                    'fields' => [
                        [
                            'name' => 'Monitor',
                            'value' => $monitor->name,
                            'inline' => true,
                        ],
                        [
                            'name' => 'Type',
                            'value' => strtoupper($monitor->type),
                            'inline' => true,
                        ],
                        [
                            'name' => 'Target',
                            'value' => "`{$monitor->target}`",
                            'inline' => false,
                        ],
                        [
                            'name' => 'Previous Status',
                            'value' => ucfirst($previousStatus),
                            'inline' => true,
                        ],
                        [
                            'name' => 'New Status',
                            'value' => ucfirst($newStatus),
                            'inline' => true,
                        ],
                    ],
                    'timestamp' => now()->toIso8601String(),
                    'footer' => [
                        'text' => 'Beacon Monitoring',
                    ],
                ],
            ],
        ];

        try {
            $response = Http::post($channel->webhook_url, $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Discord notification failed', [
                'channel_id' => $channel->id,
                'monitor_id' => $monitor->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendTestMessage(AlertChannel $channel): bool
    {
        if ($channel->type !== 'discord' || empty($channel->webhook_url)) {
            return false;
        }

        $payload = [
            'username' => $channel->discord_username ?? 'Beacon Monitor',
            'avatar_url' => 'https://beacon.app/images/logo.png',
            'embeds' => [
                [
                    'title' => '✅ Test Notification',
                    'description' => 'Your Discord integration is working correctly!',
                    'color' => 0x22C55E,
                    'timestamp' => now()->toIso8601String(),
                    'footer' => [
                        'text' => 'Beacon Monitoring',
                    ],
                ],
            ],
        ];

        try {
            $response = Http::post($channel->webhook_url, $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Discord test notification failed', [
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function getColorForStatus(string $status): int
    {
        return match ($status) {
            'up' => 0x22C55E,      // Green
            'degraded' => 0xF59E0B, // Amber
            'down' => 0xEF4444,     // Red
            default => 0x6B7280,    // Gray
        };
    }

    private function getEmojiForStatus(string $status): string
    {
        return match ($status) {
            'up' => '✅',
            'degraded' => '⚠️',
            'down' => '❌',
            default => '❓',
        };
    }
}
