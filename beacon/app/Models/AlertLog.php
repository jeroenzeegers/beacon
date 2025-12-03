<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertLog extends Model
{
    use BelongsToTeam, HasFactory;

    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        'team_id',
        'alert_rule_id',
        'alert_channel_id',
        'monitor_id',
        'trigger',
        'status',
        'message',
        'metadata',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function alertRule(): BelongsTo
    {
        return $this->belongsTo(AlertRule::class);
    }

    public function alertChannel(): BelongsTo
    {
        return $this->belongsTo(AlertChannel::class);
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeSkipped(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SKIPPED);
    }

    public function scopeForMonitor(Builder $query, int $monitorId): Builder
    {
        return $query->where('monitor_id', $monitorId);
    }

    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('sent_at', '>=', now()->subHours($hours));
    }

    public function wasSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function wasFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function wasSkipped(): bool
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    public static function log(
        int $teamId,
        string $trigger,
        string $status,
        string $message,
        ?int $alertRuleId = null,
        ?int $alertChannelId = null,
        ?int $monitorId = null,
        ?array $metadata = null,
        ?string $errorMessage = null,
    ): self {
        return self::create([
            'team_id' => $teamId,
            'alert_rule_id' => $alertRuleId,
            'alert_channel_id' => $alertChannelId,
            'monitor_id' => $monitorId,
            'trigger' => $trigger,
            'status' => $status,
            'message' => $message,
            'metadata' => $metadata,
            'error_message' => $errorMessage,
            'sent_at' => now(),
        ]);
    }
}
