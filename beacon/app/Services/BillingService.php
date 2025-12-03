<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plan;
use App\Models\Team;
use App\Models\UsageRecord;
use Laravel\Cashier\Subscription;

class BillingService
{
    public function __construct(
        private readonly UsageLimiter $usageLimiter
    ) {}

    /**
     * Create a new subscription for a team.
     */
    public function createSubscription(
        Team $team,
        string $priceId,
        ?string $paymentMethodId = null
    ): Subscription {
        // Create or get Stripe customer
        $team->createOrGetStripeCustomer([
            'name' => $team->name,
            'email' => $team->owner->email,
            'metadata' => [
                'team_id' => $team->id,
                'team_slug' => $team->slug,
            ],
        ]);

        $subscription = $team->newSubscription('default', $priceId);

        // Apply trial if team hasn't had one before
        if (!$team->hasEverSubscribed()) {
            $subscription->trialDays(config('billing.trial_days', 14));
        }

        if ($paymentMethodId) {
            return $subscription->create($paymentMethodId);
        }

        return $subscription->create();
    }

    /**
     * Change a team's subscription plan.
     */
    public function changePlan(Team $team, string $newPriceId): void
    {
        $subscription = $team->subscription('default');

        if (!$subscription) {
            throw new \RuntimeException('Team does not have an active subscription.');
        }

        $subscription->skipTrial()->swap($newPriceId);

        // Check if downgrade violates new limits
        $this->enforceNewLimits($team);
    }

    /**
     * Cancel a team's subscription.
     */
    public function cancelSubscription(Team $team): void
    {
        $subscription = $team->subscription('default');

        if ($subscription) {
            $subscription->cancel();
        }
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resumeSubscription(Team $team): void
    {
        $subscription = $team->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();
        }
    }

    /**
     * Report metered usage to Stripe.
     */
    public function reportUsage(Team $team, string $type, int $quantity = 1): void
    {
        // Record locally
        UsageRecord::create([
            'team_id' => $team->id,
            'type' => $type,
            'quantity' => $quantity,
            'recorded_at' => now()->toDateString(),
            'stripe_reported' => false,
        ]);

        // Report to Stripe if needed (for metered billing)
        $subscription = $team->subscription('default');
        if ($subscription) {
            $meteredPriceId = $this->getMeteredPriceId($type);
            if ($meteredPriceId) {
                $subscription->reportUsageFor($meteredPriceId, $quantity);

                // Mark as reported
                UsageRecord::where('team_id', $team->id)
                    ->where('type', $type)
                    ->whereDate('recorded_at', now()->toDateString())
                    ->update(['stripe_reported' => true]);
            }
        }
    }

    /**
     * Enforce plan limits after a downgrade.
     */
    public function enforceNewLimits(Team $team): void
    {
        $limits = $team->getPlanLimits();

        // Pause excess monitors (keep newest active)
        $monitorCount = $team->monitors()->count();
        if (!$limits->isUnlimited('monitors') && $monitorCount > $limits->monitors) {
            $team->monitors()
                ->where('is_active', true)
                ->orderByDesc('created_at')
                ->skip($limits->monitors)
                ->take($monitorCount - $limits->monitors)
                ->update(['is_active' => false, 'is_paused' => true]);
        }

        // Similar for other resources as needed...
    }

    /**
     * Downgrade team to free plan.
     */
    public function downgradeToFree(Team $team): void
    {
        // Cancel subscription if exists
        $this->cancelSubscription($team);

        // Enforce free tier limits
        $this->enforceNewLimits($team);

        // TODO: Notify owner
        // $team->owner->notify(new DowngradedToFreeNotification());
    }

    /**
     * Get team's current plan.
     */
    public function getCurrentPlan(Team $team): ?Plan
    {
        $subscription = $team->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return Plan::where('slug', 'free')->first();
        }

        return Plan::where('stripe_price_id_monthly', $subscription->stripe_price)
            ->orWhere('stripe_price_id_yearly', $subscription->stripe_price)
            ->first();
    }

    /**
     * Check if team has ever had a subscription (for trial eligibility).
     */
    public function hasEverSubscribed(Team $team): bool
    {
        return $team->subscriptions()->exists();
    }

    /**
     * Get metered price ID for a usage type.
     */
    private function getMeteredPriceId(string $type): ?string
    {
        return match ($type) {
            'sms_sent' => config('billing.metered_prices.sms'),
            'api_calls' => config('billing.metered_prices.api_calls'),
            default => null,
        };
    }
}
