<?php

declare(strict_types=1);

namespace App\Support\Checkers;

use App\Models\Monitor;
use App\Support\CheckResult;

class TcpChecker implements CheckerInterface
{
    public function check(Monitor $monitor): CheckResult
    {
        $host = $monitor->target;
        $port = $monitor->port ?? 80;
        $timeout = $monitor->timeout;

        $startTime = microtime(true);

        try {
            $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

            $responseTime = (int) round((microtime(true) - $startTime) * 1000);

            if (! $socket) {
                return CheckResult::failure(
                    errorMessage: "TCP connection failed: {$errstr} (#{$errno})",
                    responseTime: $responseTime,
                );
            }

            fclose($socket);

            // Resolve DNS for additional info
            $dnsInfo = $this->getDnsInfo($host);

            return CheckResult::success(
                responseTime: $responseTime,
                dnsInfo: $dnsInfo,
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
        return $type === Monitor::TYPE_TCP;
    }

    private function getDnsInfo(string $host): ?array
    {
        try {
            $ip = gethostbyname($host);

            if ($ip === $host) {
                // DNS resolution failed
                return null;
            }

            $records = dns_get_record($host, DNS_A | DNS_AAAA);

            return [
                'resolved_ip' => $ip,
                'records' => array_map(fn ($r) => [
                    'type' => $r['type'] ?? null,
                    'ip' => $r['ip'] ?? $r['ipv6'] ?? null,
                    'ttl' => $r['ttl'] ?? null,
                ], $records ?: []),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
