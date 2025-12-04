<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageRecord extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id',
        'type',
        'quantity',
        'recorded_at',
        'stripe_reported',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'recorded_at' => 'date',
            'stripe_reported' => 'boolean',
        ];
    }

    /**
     * Scope for a specific usage type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for the current month.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('recorded_at', now()->month)
            ->whereYear('recorded_at', now()->year);
    }

    /**
     * Scope for a specific date range.
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('recorded_at', [$start, $end]);
    }

    /**
     * Scope for unreported usage (for Stripe sync).
     */
    public function scopeUnreported($query)
    {
        return $query->where('stripe_reported', false);
    }
}
