<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Plan;
use App\Models\PlanLimit;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Plans extends Component
{
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editingPlanId = null;

    public string $name = '';
    public string $stripe_price_id = '';
    public int $price = 0;
    public string $billing_period = 'monthly';
    public bool $is_active = true;

    // Plan limits
    public int $max_monitors = 10;
    public int $max_team_members = 5;
    public int $check_interval_minutes = 5;
    public int $retention_days = 30;
    public bool $api_access = false;
    public bool $custom_status_page = false;
    public bool $sms_alerts = false;
    public bool $slack_integration = false;
    public bool $webhook_integration = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'stripe_price_id' => 'nullable|string|max:255',
            'price' => 'required|integer|min:0',
            'billing_period' => 'required|in:monthly,yearly',
            'is_active' => 'boolean',
            'max_monitors' => 'required|integer|min:1',
            'max_team_members' => 'required|integer|min:1',
            'check_interval_minutes' => 'required|integer|min:1',
            'retention_days' => 'required|integer|min:1',
            'api_access' => 'boolean',
            'custom_status_page' => 'boolean',
            'sms_alerts' => 'boolean',
            'slack_integration' => 'boolean',
            'webhook_integration' => 'boolean',
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.plans', [
            'plans' => Plan::with('limits')->orderBy('price')->get(),
        ]);
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'stripe_price_id', 'price', 'billing_period', 'is_active', 'max_monitors', 'max_team_members', 'check_interval_minutes', 'retention_days', 'api_access', 'custom_status_page', 'sms_alerts', 'slack_integration', 'webhook_integration']);
        $this->showCreateModal = true;
    }

    public function openEditModal(int $planId): void
    {
        $plan = Plan::with('limits')->findOrFail($planId);

        $this->editingPlanId = $plan->id;
        $this->name = $plan->name;
        $this->stripe_price_id = $plan->stripe_price_id ?? '';
        $this->price = $plan->price;
        $this->billing_period = $plan->billing_period;
        $this->is_active = $plan->is_active;

        if ($plan->limits) {
            $this->max_monitors = $plan->limits->max_monitors;
            $this->max_team_members = $plan->limits->max_team_members;
            $this->check_interval_minutes = $plan->limits->check_interval_minutes;
            $this->retention_days = $plan->limits->retention_days;
            $this->api_access = $plan->limits->api_access;
            $this->custom_status_page = $plan->limits->custom_status_page;
            $this->sms_alerts = $plan->limits->sms_alerts;
            $this->slack_integration = $plan->limits->slack_integration;
            $this->webhook_integration = $plan->limits->webhook_integration;
        }

        $this->showEditModal = true;
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editingPlanId = null;
    }

    public function createPlan(): void
    {
        $this->validate();

        $plan = Plan::create([
            'name' => $this->name,
            'stripe_price_id' => $this->stripe_price_id ?: null,
            'price' => $this->price,
            'billing_period' => $this->billing_period,
            'is_active' => $this->is_active,
        ]);

        PlanLimit::create([
            'plan_id' => $plan->id,
            'max_monitors' => $this->max_monitors,
            'max_team_members' => $this->max_team_members,
            'check_interval_minutes' => $this->check_interval_minutes,
            'retention_days' => $this->retention_days,
            'api_access' => $this->api_access,
            'custom_status_page' => $this->custom_status_page,
            'sms_alerts' => $this->sms_alerts,
            'slack_integration' => $this->slack_integration,
            'webhook_integration' => $this->webhook_integration,
        ]);

        AuditLog::log('create', "Created plan: {$plan->name}", $plan);

        session()->flash('success', "Plan {$plan->name} has been created.");
        $this->closeModal();
    }

    public function updatePlan(): void
    {
        $this->validate();

        $plan = Plan::findOrFail($this->editingPlanId);

        $plan->update([
            'name' => $this->name,
            'stripe_price_id' => $this->stripe_price_id ?: null,
            'price' => $this->price,
            'billing_period' => $this->billing_period,
            'is_active' => $this->is_active,
        ]);

        $plan->limits()->updateOrCreate(
            ['plan_id' => $plan->id],
            [
                'max_monitors' => $this->max_monitors,
                'max_team_members' => $this->max_team_members,
                'check_interval_minutes' => $this->check_interval_minutes,
                'retention_days' => $this->retention_days,
                'api_access' => $this->api_access,
                'custom_status_page' => $this->custom_status_page,
                'sms_alerts' => $this->sms_alerts,
                'slack_integration' => $this->slack_integration,
                'webhook_integration' => $this->webhook_integration,
            ]
        );

        AuditLog::log('update', "Updated plan: {$plan->name}", $plan);

        session()->flash('success', "Plan {$plan->name} has been updated.");
        $this->closeModal();
    }

    public function togglePlanStatus(int $planId): void
    {
        $plan = Plan::findOrFail($planId);
        $plan->update(['is_active' => !$plan->is_active]);

        AuditLog::log(
            'update',
            $plan->is_active ? "Activated plan: {$plan->name}" : "Deactivated plan: {$plan->name}",
            $plan
        );

        session()->flash('success', $plan->is_active
            ? "Plan {$plan->name} has been activated."
            : "Plan {$plan->name} has been deactivated.");
    }
}
