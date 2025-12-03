<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitor_id',
        'status',
        'response_time',
        'status_code',
        'error_message',
        'response_headers',
        'response_size',
        'ssl_info',
        'dns_info',
        'checked_from',
        'checked_at',
    ];

    protected $casts = [
        'response_time' => 'integer',
        'status_code' => 'integer',
        'response_headers' => 'array',
        'response_size' => 'integer',
        'ssl_info' => 'array',
        'dns_info' => 'array',
        'checked_at' => 'datetime',
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', Monitor::STATUS_UP);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', Monitor::STATUS_DOWN);
    }

    public function scopeSince(Builder $query, $date): Builder
    {
        return $query->where('checked_at', '>=', $date);
    }

    public function scopeForMonitor(Builder $query, int $monitorId): Builder
    {
        return $query->where('monitor_id', $monitorId);
    }

    public function isSuccessful(): bool
    {
        return $this->status === Monitor::STATUS_UP;
    }

    public function isFailed(): bool
    {
        return $this->status === Monitor::STATUS_DOWN;
    }

    public function getResponseTimeInSeconds(): ?float
    {
        if ($this->response_time === null) {
            return null;
        }

        return $this->response_time / 1000;
    }

    public function getResponseSizeFormatted(): ?string
    {
        if ($this->response_size === null) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->response_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2).' '.$units[$unit];
    }

    public function getSslExpiryDays(): ?int
    {
        if (empty($this->ssl_info['valid_to'])) {
            return null;
        }

        $expiryDate = \Carbon\Carbon::parse($this->ssl_info['valid_to']);

        return (int) now()->diffInDays($expiryDate, false);
    }
}
