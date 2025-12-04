<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MaintenanceWindow extends Model
{
    use BelongsToTeam, HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const RECURRENCE_DAILY = 'daily';

    public const RECURRENCE_WEEKLY = 'weekly';

    public const RECURRENCE_MONTHLY = 'monthly';

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'starts_at',
        'ends_at',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_config',
        'suppress_alerts',
        'show_on_status_page',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_recurring' => 'boolean',
        'recurrence_config' => 'array',
        'suppress_alerts' => 'boolean',
        'show_on_status_page' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class, 'maintenance_window_monitors')
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE ||
            (now()->gte($this->starts_at) && now()->lte($this->ends_at) && $this->status === self::STATUS_SCHEDULED);
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED && now()->lt($this->starts_at);
    }

    public function getDurationInMinutes(): int
    {
        return (int) $this->starts_at->diffInMinutes($this->ends_at);
    }

    public function updateStatus(): void
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return;
        }

        if (now()->gte($this->ends_at)) {
            $this->update(['status' => self::STATUS_COMPLETED]);
        } elseif (now()->gte($this->starts_at)) {
            $this->update(['status' => self::STATUS_ACTIVE]);
        }
    }

    public static function isMonitorInMaintenance(int $monitorId): bool
    {
        return self::whereHas('monitors', function ($query) use ($monitorId) {
            $query->where('monitor_id', $monitorId);
        })
            ->where('status', self::STATUS_ACTIVE)
            ->orWhere(function ($query) {
                $query->where('status', self::STATUS_SCHEDULED)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now());
            })
            ->where('suppress_alerts', true)
            ->exists();
    }
}
