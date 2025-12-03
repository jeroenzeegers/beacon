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

class Incident extends Model
{
    use BelongsToTeam, HasFactory;

    public const STATUS_INVESTIGATING = 'investigating';
    public const STATUS_IDENTIFIED = 'identified';
    public const STATUS_MONITORING = 'monitoring';
    public const STATUS_RESOLVED = 'resolved';

    public const SEVERITY_MINOR = 'minor';
    public const SEVERITY_MAJOR = 'major';
    public const SEVERITY_CRITICAL = 'critical';

    protected $fillable = [
        'team_id',
        'monitor_id',
        'title',
        'status',
        'severity',
        'description',
        'started_at',
        'resolved_at',
        'is_public',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class, 'incident_monitor');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(IncidentUpdate::class)->orderBy('created_at', 'desc');
    }

    public function latestUpdate(): HasMany
    {
        return $this->hasMany(IncidentUpdate::class)->latestOfMany();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_INVESTIGATING,
            self::STATUS_IDENTIFIED,
            self::STATUS_MONITORING,
        ]);
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('started_at', '>=', now()->subDays($days));
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            self::STATUS_INVESTIGATING,
            self::STATUS_IDENTIFIED,
            self::STATUS_MONITORING,
        ], true);
    }

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function resolve(): void
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    public function updateStatus(string $status, string $message, ?User $user = null): IncidentUpdate
    {
        $this->update(['status' => $status]);

        if ($status === self::STATUS_RESOLVED) {
            $this->update(['resolved_at' => now()]);
        }

        return $this->updates()->create([
            'user_id' => $user?->id,
            'status' => $status,
            'message' => $message,
            'is_public' => $this->is_public,
        ]);
    }

    public function getDurationAttribute(): ?int
    {
        if (!$this->started_at) {
            return null;
        }

        $endTime = $this->resolved_at ?? now();

        return $this->started_at->diffInMinutes($endTime);
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_INVESTIGATING => 'Investigating',
            self::STATUS_IDENTIFIED => 'Identified',
            self::STATUS_MONITORING => 'Monitoring',
            self::STATUS_RESOLVED => 'Resolved',
        ];
    }

    public static function getSeverities(): array
    {
        return [
            self::SEVERITY_MINOR => 'Minor',
            self::SEVERITY_MAJOR => 'Major',
            self::SEVERITY_CRITICAL => 'Critical',
        ];
    }
}
