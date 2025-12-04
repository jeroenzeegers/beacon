<?php

declare(strict_types=1);

namespace App\Support\Checkers;

use App\Models\Monitor;
use App\Support\CheckResult;
use Illuminate\Support\Facades\Http;

class HttpChecker implements CheckerInterface
{
    public function check(Monitor $monitor): CheckResult
    {
        $options = $monitor->http_options ?? [];
        $method = strtoupper($options['method'] ?? 'GET');
        $headers = $options['headers'] ?? [];
        $body = $options['body'] ?? null;
        $expectedStatus = $options['expected_status'] ?? [200, 201, 202, 203, 204, 301, 302];
        $expectedBody = $options['expected_body'] ?? null;
        $followRedirects = $options['follow_redirects'] ?? true;

        // Ensure expected_status is an array
        if (! is_array($expectedStatus)) {
            $expectedStatus = [$expectedStatus];
        }

        $startTime = microtime(true);

        try {
            $request = Http::timeout($monitor->timeout)
                ->withHeaders($headers);

            if (! $followRedirects) {
                $request->withOptions(['allow_redirects' => false]);
            }

            $response = match ($method) {
                'GET' => $request->get($monitor->target),
                'POST' => $request->post($monitor->target, $body),
                'PUT' => $request->put($monitor->target, $body),
                'PATCH' => $request->patch($monitor->target, $body),
                'DELETE' => $request->delete($monitor->target),
                'HEAD' => $request->head($monitor->target),
                default => $request->get($monitor->target),
            };

            $responseTime = (int) round((microtime(true) - $startTime) * 1000);
            $statusCode = $response->status();
            $responseHeaders = $response->headers();
            $responseBody = $response->body();
            $responseSize = strlen($responseBody);

            // Check status code
            if (! in_array($statusCode, $expectedStatus, true)) {
                return CheckResult::failure(
                    errorMessage: "Unexpected status code: {$statusCode}",
                    responseTime: $responseTime,
                    statusCode: $statusCode,
                    responseHeaders: $responseHeaders,
                );
            }

            // Check expected body content
            if ($expectedBody !== null && ! str_contains($responseBody, $expectedBody)) {
                return CheckResult::failure(
                    errorMessage: 'Expected body content not found',
                    responseTime: $responseTime,
                    statusCode: $statusCode,
                    responseHeaders: $responseHeaders,
                );
            }

            // Collect SSL info for HTTPS
            $sslInfo = null;
            if (str_starts_with($monitor->target, 'https://')) {
                $sslInfo = $this->getSslInfo($monitor->target);
            }

            return CheckResult::success(
                responseTime: $responseTime,
                statusCode: $statusCode,
                responseHeaders: $responseHeaders,
                responseSize: $responseSize,
                sslInfo: $sslInfo,
            );
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $responseTime = (int) round((microtime(true) - $startTime) * 1000);

            return CheckResult::failure(
                errorMessage: 'Connection failed: '.$e->getMessage(),
                responseTime: $responseTime,
            );
        } catch (\Exception $e) {
            $responseTime = (int) round((microtime(true) - $startTime) * 1000);

            return CheckResult::failure(
                errorMessage: $e->getMessage(),
                responseTime: $responseTime,
            );
        }
    }

    public function supports(string $type): bool
    {
        return in_array($type, [Monitor::TYPE_HTTP, Monitor::TYPE_HTTPS], true);
    }

    private function getSslInfo(string $url): ?array
    {
        try {
            $parsed = parse_url($url);
            $host = $parsed['host'] ?? null;
            $port = $parsed['port'] ?? 443;

            if (! $host) {
                return null;
            }

            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $stream = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno,
                $errstr,
                10,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (! $stream) {
                return null;
            }

            $params = stream_context_get_params($stream);
            $cert = $params['options']['ssl']['peer_certificate'] ?? null;

            if (! $cert) {
                fclose($stream);

                return null;
            }

            $certInfo = openssl_x509_parse($cert);
            fclose($stream);

            if (! $certInfo) {
                return null;
            }

            return [
                'issuer' => $certInfo['issuer']['O'] ?? $certInfo['issuer']['CN'] ?? 'Unknown',
                'subject' => $certInfo['subject']['CN'] ?? 'Unknown',
                'valid_from' => date('Y-m-d H:i:s', $certInfo['validFrom_time_t']),
                'valid_to' => date('Y-m-d H:i:s', $certInfo['validTo_time_t']),
                'days_remaining' => (int) round(($certInfo['validTo_time_t'] - time()) / 86400),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
