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

class AlertRule extends Model
{
    use BelongsToTeam, HasFactory;

    public const TRIGGER_MONITOR_DOWN = 'monitor_down';

    public const TRIGGER_MONITOR_UP = 'monitor_up';

    public const TRIGGER_MONITOR_DEGRADED = 'monitor_degraded';

    public const TRIGGER_SSL_EXPIRING = 'ssl_expiring';

    public const TRIGGER_RESPONSE_SLOW = 'response_slow';

    public const TRIGGER_STATUS_CHANGE = 'status_change';

    protected $fillable = [
        'team_id',
        'monitor_id',
        'project_id',
        'name',
        'trigger',
        'conditions',
        'cooldown_minutes',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'cooldown_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(AlertChannel::class, 'alert_rule_channel');
    }

    public function alertLogs(): HasMany
    {
        return $this->hasMany(AlertLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForTrigger(Builder $query, string $trigger): Builder
    {
        return $query->where('trigger', $trigger);
    }

    public function scopeForMonitor(Builder $query, int $monitorId): Builder
    {
        return $query->where(function (Builder $q) use ($monitorId) {
            $q->where('monitor_id', $monitorId)
                ->orWhereNull('monitor_id');
        });
    }

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('monitor_id')->whereNull('project_id');
    }

    public function isGlobal(): bool
    {
        return $this->monitor_id === null && $this->project_id === null;
    }

    public function isInCooldown(): bool
    {
        $lastAlert = $this->alertLogs()
            ->where('status', 'sent')
            ->latest('sent_at')
            ->first();

        if (! $lastAlert) {
            return false;
        }

        return $lastAlert->sent_at->addMinutes($this->cooldown_minutes)->isFuture();
    }

    public function shouldTrigger(string $trigger, ?Monitor $monitor = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->trigger !== $trigger && $this->trigger !== self::TRIGGER_STATUS_CHANGE) {
            return false;
        }

        // Check if rule is specific to a monitor
        if ($this->monitor_id !== null && $monitor?->id !== $this->monitor_id) {
            return false;
        }

        // Check if rule is specific to a project
        if ($this->project_id !== null && $monitor?->project_id !== $this->project_id) {
            return false;
        }

        // Check cooldown
        if ($this->isInCooldown()) {
            return false;
        }

        // Check additional conditions
        if (! $this->checkConditions($monitor)) {
            return false;
        }

        return true;
    }

    private function checkConditions(?Monitor $monitor): bool
    {
        if (empty($this->conditions)) {
            return true;
        }

        // Check response time threshold
        if (isset($this->conditions['response_time_threshold'])) {
            $latestCheck = $monitor?->latestCheck;
            if ($latestCheck && $latestCheck->response_time < $this->conditions['response_time_threshold']) {
                return false;
            }
        }

        // Check SSL days threshold
        if (isset($this->conditions['ssl_days_threshold'])) {
            $latestCheck = $monitor?->latestCheck;
            $daysRemaining = $latestCheck?->getSslExpiryDays();
            if ($daysRemaining !== null && $daysRemaining > $this->conditions['ssl_days_threshold']) {
                return false;
            }
        }

        return true;
    }

    public static function getAvailableTriggers(): array
    {
        return [
            self::TRIGGER_MONITOR_DOWN => 'Monitor goes down',
            self::TRIGGER_MONITOR_UP => 'Monitor recovers',
            self::TRIGGER_MONITOR_DEGRADED => 'Monitor is degraded',
            self::TRIGGER_SSL_EXPIRING => 'SSL certificate expiring',
            self::TRIGGER_RESPONSE_SLOW => 'Response time too slow',
            self::TRIGGER_STATUS_CHANGE => 'Any status change',
        ];
    }
}
