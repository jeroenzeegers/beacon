<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Heartbeat extends Model
{
    use BelongsToTeam, HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_HEALTHY = 'healthy';

    public const STATUS_LATE = 'late';

    public const STATUS_MISSING = 'missing';

    protected $fillable = [
        'team_id',
        'project_id',
        'name',
        'slug',
        'description',
        'expected_interval',
        'grace_period',
        'status',
        'last_ping_at',
        'next_expected_at',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'expected_interval' => 'integer',
        'grace_period' => 'integer',
        'is_active' => 'boolean',
        'last_ping_at' => 'datetime',
        'next_expected_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Heartbeat $heartbeat) {
            if (empty($heartbeat->slug)) {
                $heartbeat->slug = Str::random(32);
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function pings(): HasMany
    {
        return $this->hasMany(HeartbeatPing::class);
    }

    public function recordPing(array $data = []): HeartbeatPing
    {
        $ping = $this->pings()->create([
            'status' => $data['status'] ?? 'success',
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'payload' => $data['payload'] ?? null,
            'pinged_at' => now(),
        ]);

        $this->update([
            'status' => self::STATUS_HEALTHY,
            'last_ping_at' => now(),
            'next_expected_at' => now()->addMinutes($this->expected_interval),
        ]);

        return $ping;
    }

    public function checkStatus(): string
    {
        if (! $this->last_ping_at) {
            return self::STATUS_PENDING;
        }

        $expectedAt = $this->last_ping_at->addMinutes($this->expected_interval);
        $graceEndsAt = $expectedAt->addMinutes($this->grace_period);

        if (now()->lt($expectedAt)) {
            return self::STATUS_HEALTHY;
        }

        if (now()->lt($graceEndsAt)) {
            return self::STATUS_LATE;
        }

        return self::STATUS_MISSING;
    }

    public function getPingUrl(): string
    {
        return route('heartbeat.ping', $this->slug);
    }

    public function getUptimePercentage(int $days = 30): float
    {
        $since = now()->subDays($days);
        $totalPings = $this->pings()->where('pinged_at', '>=', $since)->count();

        if ($totalPings === 0) {
            return 0.0;
        }

        $successfulPings = $this->pings()
            ->where('pinged_at', '>=', $since)
            ->where('status', 'success')
            ->count();

        return round(($successfulPings / $totalPings) * 100, 2);
    }

    public static function getStatusColors(): array
    {
        return [
            self::STATUS_PENDING => 'gray',
            self::STATUS_HEALTHY => 'emerald',
            self::STATUS_LATE => 'amber',
            self::STATUS_MISSING => 'red',
        ];
    }
}
