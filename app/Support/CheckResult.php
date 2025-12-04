<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Monitor;

class CheckResult
{
    public function __construct(
        public readonly string $status,
        public readonly ?int $responseTime = null,
        public readonly ?int $statusCode = null,
        public readonly ?string $errorMessage = null,
        public readonly ?array $responseHeaders = null,
        public readonly ?int $responseSize = null,
        public readonly ?array $sslInfo = null,
        public readonly ?array $dnsInfo = null,
    ) {}

    public static function success(
        int $responseTime,
        ?int $statusCode = null,
        ?array $responseHeaders = null,
        ?int $responseSize = null,
        ?array $sslInfo = null,
        ?array $dnsInfo = null,
    ): self {
        return new self(
            status: Monitor::STATUS_UP,
            responseTime: $responseTime,
            statusCode: $statusCode,
            responseHeaders: $responseHeaders,
            responseSize: $responseSize,
            sslInfo: $sslInfo,
            dnsInfo: $dnsInfo,
        );
    }

    public static function failure(
        string $errorMessage,
        ?int $responseTime = null,
        ?int $statusCode = null,
        ?array $responseHeaders = null,
    ): self {
        return new self(
            status: Monitor::STATUS_DOWN,
            responseTime: $responseTime,
            statusCode: $statusCode,
            errorMessage: $errorMessage,
            responseHeaders: $responseHeaders,
        );
    }

    public static function degraded(
        string $reason,
        int $responseTime,
        ?int $statusCode = null,
        ?array $sslInfo = null,
    ): self {
        return new self(
            status: Monitor::STATUS_DEGRADED,
            responseTime: $responseTime,
            statusCode: $statusCode,
            errorMessage: $reason,
            sslInfo: $sslInfo,
        );
    }

    public function isSuccessful(): bool
    {
        return $this->status === Monitor::STATUS_UP;
    }

    public function isFailed(): bool
    {
        return $this->status === Monitor::STATUS_DOWN;
    }

    public function isDegraded(): bool
    {
        return $this->status === Monitor::STATUS_DEGRADED;
    }

    public function toArray(): array
    {
        return array_filter([
            'status' => $this->status,
            'response_time' => $this->responseTime,
            'status_code' => $this->statusCode,
            'error_message' => $this->errorMessage,
            'response_headers' => $this->responseHeaders,
            'response_size' => $this->responseSize,
            'ssl_info' => $this->sslInfo,
            'dns_info' => $this->dnsInfo,
        ], fn ($value) => $value !== null);
    }
}
