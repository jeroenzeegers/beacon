<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class StatusPage extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'slug',
        'custom_domain',
        'description',
        'logo_url',
        'favicon_url',
        'primary_color',
        'is_public',
        'show_uptime',
        'show_response_time',
        'uptime_days_shown',
        'settings',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'show_uptime' => 'boolean',
        'show_response_time' => 'boolean',
        'uptime_days_shown' => 'integer',
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (StatusPage $statusPage) {
            if (empty($statusPage->slug)) {
                $statusPage->slug = Str::slug($statusPage->name);

                // Ensure unique slug
                $originalSlug = $statusPage->slug;
                $counter = 1;
                while (static::where('slug', $statusPage->slug)->exists()) {
                    $statusPage->slug = $originalSlug . '-' . $counter++;
                }
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class, 'status_page_monitor')
            ->withPivot(['sort_order', 'display_name'])
            ->orderByPivot('sort_order');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function getUrlAttribute(): string
    {
        if ($this->custom_domain) {
            return "https://{$this->custom_domain}";
        }

        return route('status-page.show', $this->slug);
    }

    public function getOverallStatusAttribute(): string
    {
        $monitors = $this->monitors;

        if ($monitors->isEmpty()) {
            return 'unknown';
        }

        if ($monitors->contains('status', Monitor::STATUS_DOWN)) {
            return 'down';
        }

        if ($monitors->contains('status', Monitor::STATUS_DEGRADED)) {
            return 'degraded';
        }

        if ($monitors->every(fn ($m) => $m->status === Monitor::STATUS_UP)) {
            return 'up';
        }

        return 'unknown';
    }

    public function getActiveIncidentsAttribute()
    {
        $monitorIds = $this->monitors->pluck('id');

        return Incident::where('team_id', $this->team_id)
            ->active()
            ->public()
            ->whereHas('monitors', function ($query) use ($monitorIds) {
                $query->whereIn('monitors.id', $monitorIds);
            })
            ->orWhere(function ($query) use ($monitorIds) {
                $query->where('team_id', $this->team_id)
                    ->active()
                    ->public()
                    ->whereIn('monitor_id', $monitorIds);
            })
            ->orderBy('started_at', 'desc')
            ->get();
    }

    public function getRecentIncidentsAttribute()
    {
        $monitorIds = $this->monitors->pluck('id');

        return Incident::where('team_id', $this->team_id)
            ->public()
            ->recent(30)
            ->where(function ($query) use ($monitorIds) {
                $query->whereHas('monitors', function ($q) use ($monitorIds) {
                    $q->whereIn('monitors.id', $monitorIds);
                })->orWhereIn('monitor_id', $monitorIds);
            })
            ->orderBy('started_at', 'desc')
            ->get();
    }

    public function getUptimePercentage(int $days = null): float
    {
        $days = $days ?? $this->uptime_days_shown;
        $monitors = $this->monitors;

        if ($monitors->isEmpty()) {
            return 100.0;
        }

        $totalUptime = 0;
        foreach ($monitors as $monitor) {
            $totalUptime += $monitor->getUptimePercentage($days);
        }

        return round($totalUptime / $monitors->count(), 2);
    }

    public function getSettingValue(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }
}
