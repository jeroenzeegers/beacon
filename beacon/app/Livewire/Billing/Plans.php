<?php

declare(strict_types=1);

namespace App\Livewire\Billing;

use App\Models\Plan;
use App\Services\BillingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Plans extends Component
{
    public string $billingPeriod = 'monthly';

    public function checkout(int $planId): void
    {
        $team = Auth::user()->currentTeam;
        $plan = Plan::findOrFail($planId);

        if ($plan->isFree()) {
            session()->flash('error', 'Cannot checkout for free plan.');
            return;
        }

        $priceId = $this->billingPeriod === 'yearly'
            ? $plan->stripe_price_id_yearly
            : $plan->stripe_price_id_monthly;

        if (!$priceId) {
            session()->flash('error', 'This plan is not available for checkout yet.');
            return;
        }

        $billingService = app(BillingService::class);

        $checkoutUrl = $billingService->createCheckoutSession(
            $team,
            $priceId,
            route('billing.dashboard'),
            route('billing.plans')
        );

        if ($checkoutUrl) {
            $this->redirect($checkoutUrl);
        } else {
            session()->flash('error', 'Failed to create checkout session.');
        }
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->with('limits')
            ->get();

        $currentPlan = $team->getCurrentPlan();

        return view('livewire.billing.plans', [
            'plans' => $plans,
            'currentPlan' => $currentPlan,
            'team' => $team,
        ])->layout('layouts.app');
    }
}
