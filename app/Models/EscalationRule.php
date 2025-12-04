<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscalationRule extends Model
{
    use HasFactory;

    public const TARGET_USER = 'user';

    public const TARGET_ON_CALL_SCHEDULE = 'on_call_schedule';

    public const TARGET_ALERT_CHANNEL = 'alert_channel';

    protected $fillable = [
        'escalation_policy_id',
        'level',
        'delay_minutes',
        'target_type',
        'target_id',
    ];

    protected $casts = [
        'level' => 'integer',
        'delay_minutes' => 'integer',
        'target_id' => 'integer',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(EscalationPolicy::class, 'escalation_policy_id');
    }

    public function getTarget(): ?Model
    {
        return match ($this->target_type) {
            self::TARGET_USER => User::find($this->target_id),
            self::TARGET_ON_CALL_SCHEDULE => OnCallSchedule::find($this->target_id),
            self::TARGET_ALERT_CHANNEL => AlertChannel::find($this->target_id),
            default => null,
        };
    }

    public function getTargets(): array
    {
        $target = $this->getTarget();

        if (! $target) {
            return [];
        }

        if ($this->target_type === self::TARGET_ON_CALL_SCHEDULE) {
            $user = $target->getCurrentOnCallUser();

            return $user ? [$user] : [];
        }

        if ($this->target_type === self::TARGET_ALERT_CHANNEL) {
            return [$target];
        }

        return [$target];
    }
}
