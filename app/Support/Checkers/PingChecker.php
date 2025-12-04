<?php

declare(strict_types=1);

namespace App\Support\Checkers;

use App\Models\Monitor;
use App\Support\CheckResult;

class PingChecker implements CheckerInterface
{
    public function check(Monitor $monitor): CheckResult
    {
        $host = $monitor->target;
        $timeout = $monitor->timeout;

        // Determine OS and use appropriate ping command
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            $command = sprintf('ping -n 1 -w %d %s', $timeout * 1000, escapeshellarg($host));
        } else {
            $command = sprintf('ping -c 1 -W %d %s 2>&1', $timeout, escapeshellarg($host));
        }

        $startTime = microtime(true);

        exec($command, $output, $returnCode);

        $responseTime = (int) round((microtime(true) - $startTime) * 1000);
        $outputStr = implode("\n", $output);

        if ($returnCode !== 0) {
            return CheckResult::failure(
                errorMessage: 'Ping failed: Host unreachable',
                responseTime: $responseTime,
            );
        }

        // Try to extract actual ping time from output
        $pingTime = $this->extractPingTime($outputStr);

        // Get DNS info
        $dnsInfo = $this->getDnsInfo($host);

        return CheckResult::success(
            responseTime: $pingTime ?? $responseTime,
            dnsInfo: $dnsInfo,
        );
    }

    public function supports(string $type): bool
    {
        return $type === Monitor::TYPE_PING;
    }

    private function extractPingTime(string $output): ?int
    {
        // Match patterns like "time=12.3 ms" or "time=12.3ms"
        if (preg_match('/time[=<](\d+(?:\.\d+)?)\s*ms/i', $output, $matches)) {
            return (int) round((float) $matches[1]);
        }

        return null;
    }

    private function getDnsInfo(string $host): ?array
    {
        try {
            $ip = gethostbyname($host);

            if ($ip === $host && ! filter_var($host, FILTER_VALIDATE_IP)) {
                // DNS resolution failed and host is not an IP
                return null;
            }

            return [
                'resolved_ip' => $ip,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
