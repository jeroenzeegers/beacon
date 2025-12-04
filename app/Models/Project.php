<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'slug',
        'description',
        'environment',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function monitors(): HasMany
    {
        return $this->hasMany(Monitor::class);
    }

    public function metricSnapshots(): HasMany
    {
        return $this->hasMany(MetricSnapshot::class);
    }

    public function activeMonitors(): HasMany
    {
        return $this->monitors()->where('is_active', true);
    }

    public function getMonitorCountAttribute(): int
    {
        return $this->monitors()->count();
    }

    public function getActiveMonitorCountAttribute(): int
    {
        return $this->activeMonitors()->count();
    }

    public function getOverallStatusAttribute(): string
    {
        $monitors = $this->monitors()->get();

        if ($monitors->isEmpty()) {
            return 'unknown';
        }

        if ($monitors->contains('status', 'down')) {
            return 'down';
        }

        if ($monitors->contains('status', 'degraded')) {
            return 'degraded';
        }

        if ($monitors->every(fn ($m) => $m->status === 'up')) {
            return 'up';
        }

        return 'unknown';
    }
}
