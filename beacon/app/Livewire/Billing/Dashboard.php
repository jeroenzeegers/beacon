<?php

declare(strict_types=1);

namespace App\Livewire\Billing;

use App\Services\UsageLimiter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function redirectToPortal()
    {
        $team = Auth::user()->currentTeam;

        if (! $team->hasStripeId()) {
            session()->flash('error', 'No billing account found. Please subscribe to a plan first.');

            return;
        }

        return redirect($team->billingPortalUrl(route('billing.dashboard')));
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        $usageLimiter = app(UsageLimiter::class);
        $remainingLimits = $usageLimiter->getRemainingLimits($team);

        $subscription = $team->subscription('default');
        $currentPlan = $team->getCurrentPlan();

        return view('livewire.billing.dashboard', [
            'team' => $team,
            'currentPlan' => $currentPlan,
            'subscription' => $subscription,
            'remainingLimits' => $remainingLimits,
            'onTrial' => $team->onTrial(),
            'trialEndsAt' => $subscription?->trial_ends_at,
            'subscribed' => $team->subscribed(),
            'hasStripeId' => $team->hasStripeId(),
        ])->layout('layouts.app');
    }
}
