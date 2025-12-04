<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'stripe_product_id',
        'stripe_price_id_monthly',
        'stripe_price_id_yearly',
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'integer',
            'price_yearly' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function limits(): HasMany
    {
        return $this->hasMany(PlanLimit::class);
    }

    public function getLimit(string $feature): int
    {
        $limit = $this->limits()->where('feature', $feature)->first();

        return $limit?->limit_value ?? $this->getDefaultLimit($feature);
    }

    public function hasFeature(string $feature): bool
    {
        $value = $this->getLimit($feature);

        return $value === -1 || $value > 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the formatted monthly price.
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return '€'.number_format($this->price_monthly / 100, 2);
    }

    /**
     * Get the formatted yearly price.
     */
    public function getFormattedYearlyPriceAttribute(): string
    {
        return '€'.number_format($this->price_yearly / 100, 2);
    }

    /**
     * Check if this is the free plan.
     */
    public function isFree(): bool
    {
        return $this->slug === 'free' || $this->price_monthly === 0;
    }

    /**
     * Get the default limit for a feature if not explicitly set.
     */
    private function getDefaultLimit(string $feature): int
    {
        return match ($feature) {
            'monitors' => 3,
            'projects' => 1,
            'team_members' => 1,
            'check_interval_min' => 300,
            'retention_days' => 7,
            'status_pages' => 1,
            'alert_channels' => 2,
            'api_access' => 0,
            'sms_alerts' => 0,
            'custom_domains' => 0,
            'sla_reports' => 0,
            default => 0,
        };
    }
}
