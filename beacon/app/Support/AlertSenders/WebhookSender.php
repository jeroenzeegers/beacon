<?php

declare(strict_types=1);

namespace App\Support\AlertSenders;

use App\Models\AlertChannel;
use App\Models\Monitor;
use Illuminate\Support\Facades\Http;

class WebhookSender implements AlertSenderInterface
{
    public function send(AlertChannel $channel, Monitor $monitor, string $trigger, string $message): bool
    {
        $url = $channel->getConfigValue('url');
        $method = strtoupper($channel->getConfigValue('method', 'POST'));
        $headers = $channel->getConfigValue('headers', []);
        $secret = $channel->getConfigValue('secret');

        if (!$url) {
            return false;
        }

        $payload = [
            'monitor' => [
                'id' => $monitor->id,
                'name' => $monitor->name,
                'type' => $monitor->type,
                'target' => $monitor->target,
                'status' => $monitor->status,
            ],
            'trigger' => $trigger,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ];

        // Add signature if secret is configured
        if ($secret) {
            $signature = hash_hmac('sha256', json_encode($payload), $secret);
            $headers['X-Beacon-Signature'] = $signature;
        }

        $request = Http::withHeaders($headers)->timeout(30);

        $response = match ($method) {
            'GET' => $request->get($url, $payload),
            'PUT' => $request->put($url, $payload),
            default => $request->post($url, $payload),
        };

        return $response->successful();
    }

    public function supports(string $type): bool
    {
        return $type === AlertChannel::TYPE_WEBHOOK;
    }
}
