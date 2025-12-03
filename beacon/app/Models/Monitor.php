<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Monitor extends Model
{
    use BelongsToTeam, HasFactory;

    public const TYPE_HTTP = 'http';
    public const TYPE_HTTPS = 'https';
    public const TYPE_TCP = 'tcp';
    public const TYPE_PING = 'ping';
    public const TYPE_SSL_EXPIRY = 'ssl_expiry';

    public const STATUS_UP = 'up';
    public const STATUS_DOWN = 'down';
    public const STATUS_DEGRADED = 'degraded';
    public const STATUS_UNKNOWN = 'unknown';

    protected $fillable = [
        'team_id',
        'project_id',
        'name',
        'type',
        'target',
        'port',
        'check_interval',
        'timeout',
        'is_active',
        'status',
        'last_check_at',
        'last_status_change_at',
        'consecutive_failures',
        'failure_threshold',
        'http_options',
        'ssl_options',
        'metadata',
    ];

    protected $casts = [
        'port' => 'integer',
        'check_interval' => 'integer',
        'timeout' => 'integer',
        'is_active' => 'boolean',
        'last_check_at' => 'datetime',
        'last_status_change_at' => 'datetime',
        'consecutive_failures' => 'integer',
        'failure_threshold' => 'integer',
        'http_options' => 'array',
        'ssl_options' => 'array',
        'metadata' => 'array',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(MonitorCheck::class);
    }

    public function latestCheck(): HasOne
    {
        return $this->hasOne(MonitorCheck::class)->latestOfMany('checked_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForCheck(Builder $query): Builder
    {
        return $query->active()
            ->where(function (Builder $q) {
                $q->whereNull('last_check_at')
                    ->orWhereRaw('last_check_at <= NOW() - INTERVAL check_interval SECOND');
            });
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeUp(Builder $query): Builder
    {
        return $this->scopeByStatus($query, self::STATUS_UP);
    }

    public function scopeDown(Builder $query): Builder
    {
        return $this->scopeByStatus($query, self::STATUS_DOWN);
    }

    public function isDueForCheck(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->last_check_at === null) {
            return true;
        }

        return $this->last_check_at->addSeconds($this->check_interval)->isPast();
    }

    public function isUp(): bool
    {
        return $this->status === self::STATUS_UP;
    }

    public function isDown(): bool
    {
        return $this->status === self::STATUS_DOWN;
    }

    public function recordCheck(string $status, array $data = []): MonitorCheck
    {
        $previousStatus = $this->status;
        $now = now();

        $check = $this->checks()->create([
            'status' => $status,
            'response_time' => $data['response_time'] ?? null,
            'status_code' => $data['status_code'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'response_headers' => $data['response_headers'] ?? null,
            'response_size' => $data['response_size'] ?? null,
            'ssl_info' => $data['ssl_info'] ?? null,
            'dns_info' => $data['dns_info'] ?? null,
            'checked_from' => $data['checked_from'] ?? config('app.region', 'default'),
            'checked_at' => $now,
        ]);

        // Update monitor status based on check result
        if ($status === self::STATUS_UP) {
            $this->consecutive_failures = 0;
            $this->status = self::STATUS_UP;
        } else {
            $this->consecutive_failures++;
            if ($this->consecutive_failures >= $this->failure_threshold) {
                $this->status = self::STATUS_DOWN;
            } elseif ($this->consecutive_failures > 0) {
                $this->status = self::STATUS_DEGRADED;
            }
        }

        // Track status change time
        if ($previousStatus !== $this->status) {
            $this->last_status_change_at = $now;
        }

        $this->last_check_at = $now;
        $this->save();

        return $check;
    }

    public function getUptimePercentage(int $days = 30): float
    {
        $since = now()->subDays($days);

        $checks = $this->checks()
            ->where('checked_at', '>=', $since)
            ->get();

        if ($checks->isEmpty()) {
            return 0;
        }

        $upChecks = $checks->where('status', self::STATUS_UP)->count();

        return round(($upChecks / $checks->count()) * 100, 2);
    }

    public function getAverageResponseTime(int $days = 30): ?float
    {
        $since = now()->subDays($days);

        $avg = $this->checks()
            ->where('checked_at', '>=', $since)
            ->whereNotNull('response_time')
            ->avg('response_time');

        return $avg ? round($avg, 2) : null;
    }

    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_HTTP => 'HTTP',
            self::TYPE_HTTPS => 'HTTPS',
            self::TYPE_TCP => 'TCP Port',
            self::TYPE_PING => 'Ping',
            self::TYPE_SSL_EXPIRY => 'SSL Certificate',
        ];
    }
}
