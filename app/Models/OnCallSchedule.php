<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnCallSchedule extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function rotations(): HasMany
    {
        return $this->hasMany(OnCallRotation::class);
    }

    public function getCurrentOnCallUser(): ?User
    {
        $now = now($this->timezone);

        $rotation = $this->rotations()
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->orderByDesc('is_override')
            ->orderByDesc('starts_at')
            ->first();

        return $rotation?->user;
    }

    public function getNextOnCallUser(): ?User
    {
        $now = now($this->timezone);

        $rotation = $this->rotations()
            ->where('starts_at', '>', $now)
            ->where('is_override', false)
            ->orderBy('starts_at')
            ->first();

        return $rotation?->user;
    }
}
