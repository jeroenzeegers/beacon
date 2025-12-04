<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnCallRotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'on_call_schedule_id',
        'user_id',
        'position',
        'starts_at',
        'ends_at',
        'is_override',
    ];

    protected $casts = [
        'position' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_override' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(OnCallSchedule::class, 'on_call_schedule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        $now = now($this->schedule->timezone ?? 'UTC');

        return $now->gte($this->starts_at) && $now->lte($this->ends_at);
    }
}
