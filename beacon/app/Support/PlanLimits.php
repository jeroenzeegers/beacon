<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Plan;

class PlanLimits
{
    public function __construct(
        public readonly ?Plan $plan
    ) {}

    /**
     * Get a limit value dynamically.
     */
    public function __get(string $name): int
    {
        return $this->plan?->getLimit($name) ?? $this->getDefaultLimit($name);
    }

    /**
     * Check if a feature is available.
     */
    public function hasFeature(string $feature): bool
    {
        $value = $this->$feature;

        return $value === -1 || $value > 0;
    }

    /**
     * Check if the limit is unlimited.
     */
    public function isUnlimited(string $feature): bool
    {
        return $this->$feature === -1;
    }

    /**
     * Get the plan name.
     */
    public function getPlanName(): string
    {
        return $this->plan?->name ?? 'Free';
    }

    /**
     * Get the plan slug.
     */
    public function getPlanSlug(): string
    {
        return $this->plan?->slug ?? 'free';
    }

    /**
     * Check if this is the free plan.
     */
    public function isFree(): bool
    {
        return $this->plan === null || $this->plan->isFree();
    }

    /**
     * Get all limits as an array.
     */
    public function toArray(): array
    {
        $features = [
            'monitors',
            'projects',
            'team_members',
            'check_interval_min',
            'retention_days',
            'status_pages',
            'alert_channels',
            'api_access',
            'sms_alerts',
            'custom_domains',
            'sla_reports',
        ];

        $limits = [];
        foreach ($features as $feature) {
            $limits[$feature] = $this->$feature;
        }

        return $limits;
    }

    /**
     * Get the default limit for a feature (free tier).
     */
    private function getDefaultLimit(string $feature): int
    {
        return match ($feature) {
            'monitors' => 3,
            'projects' => 1,
            'team_members' => 1,
            'check_interval_min' => 300, // 5 minutes
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
