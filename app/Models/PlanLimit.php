<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'feature',
        'limit_value',
    ];

    protected function casts(): array
    {
        return [
            'limit_value' => 'integer',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Check if this limit represents unlimited access.
     */
    public function isUnlimited(): bool
    {
        return $this->limit_value === -1;
    }

    /**
     * Check if this feature is enabled (has any value > 0 or is unlimited).
     */
    public function isEnabled(): bool
    {
        return $this->limit_value === -1 || $this->limit_value > 0;
    }
}
