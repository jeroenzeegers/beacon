<?php

declare(strict_types=1);

namespace App\Support\Checkers;

use App\Models\Monitor;
use App\Support\CheckResult;

class SslExpiryChecker implements CheckerInterface
{
    public function check(Monitor $monitor): CheckResult
    {
        $host = $monitor->target;
        $port = $monitor->port ?? 443;
        $timeout = $monitor->timeout;

        $options = $monitor->ssl_options ?? [];
        $warningDays = $options['warning_days'] ?? 30;
        $criticalDays = $options['critical_days'] ?? 7;

        $startTime = microtime(true);

        try {
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ]);

            $stream = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );

            $responseTime = (int) round((microtime(true) - $startTime) * 1000);

            if (!$stream) {
                return CheckResult::failure(
                    errorMessage: "SSL connection failed: {$errstr} (#{$errno})",
                    responseTime: $responseTime,
                );
            }

            $params = stream_context_get_params($stream);
            $cert = $params['options']['ssl']['peer_certificate'] ?? null;

            if (!$cert) {
                fclose($stream);

                return CheckResult::failure(
                    errorMessage: "Could not retrieve SSL certificate",
                    responseTime: $responseTime,
                );
            }

            $certInfo = openssl_x509_parse($cert);
            fclose($stream);

            if (!$certInfo) {
                return CheckResult::failure(
                    errorMessage: "Could not parse SSL certificate",
                    responseTime: $responseTime,
                );
            }

            $expiryTimestamp = $certInfo['validTo_time_t'];
            $daysRemaining = (int) round(($expiryTimestamp - time()) / 86400);

            $sslInfo = [
                'issuer' => $certInfo['issuer']['O'] ?? $certInfo['issuer']['CN'] ?? 'Unknown',
                'subject' => $certInfo['subject']['CN'] ?? 'Unknown',
                'valid_from' => date('Y-m-d H:i:s', $certInfo['validFrom_time_t']),
                'valid_to' => date('Y-m-d H:i:s', $expiryTimestamp),
                'days_remaining' => $daysRemaining,
                'serial_number' => $certInfo['serialNumberHex'] ?? null,
            ];

            // Check if certificate is already expired
            if ($daysRemaining < 0) {
                return CheckResult::failure(
                    errorMessage: "SSL certificate expired " . abs($daysRemaining) . " days ago",
                    responseTime: $responseTime,
                    sslInfo: $sslInfo,
                );
            }

            // Check critical threshold
            if ($daysRemaining <= $criticalDays) {
                return CheckResult::failure(
                    errorMessage: "SSL certificate expires in {$daysRemaining} days (critical threshold: {$criticalDays})",
                    responseTime: $responseTime,
                    sslInfo: $sslInfo,
                );
            }

            // Check warning threshold
            if ($daysRemaining <= $warningDays) {
                return CheckResult::degraded(
                    reason: "SSL certificate expires in {$daysRemaining} days (warning threshold: {$warningDays})",
                    responseTime: $responseTime,
                    sslInfo: $sslInfo,
                );
            }

            return CheckResult::success(
                responseTime: $responseTime,
                sslInfo: $sslInfo,
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
        return $type === Monitor::TYPE_SSL_EXPIRY;
    }
}
