<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\PlanLimits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;

class Team extends Model
{
    use Billable, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'owner_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Team $team) {
            if (empty($team->slug)) {
                $team->slug = Str::slug($team->name);

                // Ensure unique slug
                $originalSlug = $team->slug;
                $counter = 1;
                while (static::where('slug', $team->slug)->exists()) {
                    $team->slug = $originalSlug . '-' . $counter++;
                }
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function members(): BelongsToMany
    {
        return $this->users();
    }

    public function hasUser(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function userRole(User $user): ?string
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot
            ?->role;
    }

    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    public function isAdmin(User $user): bool
    {
        $role = $this->userRole($user);

        return in_array($role, ['owner', 'admin'], true);
    }

    public function addUser(User $user, string $role = 'member'): void
    {
        if (!$this->hasUser($user)) {
            $this->users()->attach($user->id, ['role' => $role]);
        }
    }

    public function removeUser(User $user): void
    {
        if (!$this->isOwner($user)) {
            $this->users()->detach($user->id);
        }
    }

    public function updateUserRole(User $user, string $role): void
    {
        $this->users()->updateExistingPivot($user->id, ['role' => $role]);
    }

    // Resource relationships (for usage tracking - models created in later phases)

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function monitors(): HasMany
    {
        return $this->hasMany(Monitor::class);
    }

    public function statusPages(): HasMany
    {
        return $this->hasMany(StatusPage::class);
    }

    public function alertChannels(): HasMany
    {
        return $this->hasMany(AlertChannel::class);
    }

    public function usageRecords(): HasMany
    {
        return $this->hasMany(UsageRecord::class);
    }

    // Billing & Plan methods

    /**
     * Get the team's plan limits.
     */
    public function getPlanLimits(): PlanLimits
    {
        $plan = $this->getCurrentPlan();

        return new PlanLimits($plan);
    }

    /**
     * Get the current plan based on subscription.
     */
    public function getCurrentPlan(): ?Plan
    {
        $subscription = $this->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return Plan::where('slug', 'free')->first();
        }

        return Plan::where('stripe_price_id_monthly', $subscription->stripe_price)
            ->orWhere('stripe_price_id_yearly', $subscription->stripe_price)
            ->first() ?? Plan::where('slug', 'free')->first();
    }

    /**
     * Check if team has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        return $this->getPlanLimits()->hasFeature($feature);
    }

    /**
     * Check if team is on trial.
     */
    public function onTrial(): bool
    {
        return $this->subscription('default')?->onTrial() ?? false;
    }

    /**
     * Check if team has an active subscription.
     */
    public function subscribed(): bool
    {
        return $this->subscription('default')?->active() ?? false;
    }

    /**
     * Check if team is on the free plan.
     */
    public function onFreePlan(): bool
    {
        return !$this->subscribed() && !$this->onTrial();
    }

    /**
     * Get current plan name for display.
     */
    public function currentPlanName(): string
    {
        if ($this->onTrial()) {
            return 'Pro (Trial)';
        }

        return $this->getCurrentPlan()?->name ?? 'Free';
    }

    /**
     * Check if team has ever had a subscription.
     */
    public function hasEverSubscribed(): bool
    {
        return $this->subscriptions()->exists();
    }
}
