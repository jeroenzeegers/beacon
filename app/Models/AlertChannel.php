<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlertChannel extends Model
{
    use BelongsToTeam, HasFactory;

    public const TYPE_EMAIL = 'email';

    public const TYPE_SLACK = 'slack';

    public const TYPE_WEBHOOK = 'webhook';

    public const TYPE_SMS = 'sms';

    public const TYPE_PAGERDUTY = 'pagerduty';

    public const TYPE_DISCORD = 'discord';

    public const TYPE_TELEGRAM = 'telegram';

    protected $fillable = [
        'team_id',
        'name',
        'type',
        'config',
        'is_active',
        'is_default',
        'verified_at',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function alertRules(): BelongsToMany
    {
        return $this->belongsToMany(AlertRule::class, 'alert_rule_channel');
    }

    public function alertLogs(): HasMany
    {
        return $this->hasMany(AlertLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function markAsVerified(): void
    {
        $this->update(['verified_at' => now()]);
    }

    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_EMAIL => 'Email',
            self::TYPE_SLACK => 'Slack',
            self::TYPE_WEBHOOK => 'Webhook',
            self::TYPE_SMS => 'SMS',
            self::TYPE_PAGERDUTY => 'PagerDuty',
            self::TYPE_DISCORD => 'Discord',
            self::TYPE_TELEGRAM => 'Telegram',
        ];
    }

    public static function getRequiredConfigFields(string $type): array
    {
        return match ($type) {
            self::TYPE_EMAIL => ['email'],
            self::TYPE_SLACK => ['webhook_url'],
            self::TYPE_WEBHOOK => ['url', 'method'],
            self::TYPE_SMS => ['phone_number'],
            self::TYPE_PAGERDUTY => ['routing_key'],
            self::TYPE_DISCORD => ['webhook_url'],
            self::TYPE_TELEGRAM => ['bot_token', 'chat_id'],
            default => [],
        };
    }
}
