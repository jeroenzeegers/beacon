<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Plans & Pricing</h1>
            <button
                wire:click="openCreateModal"
                class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all"
            >
                Create Plan
            </button>
        </div>
    </x-slot>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            <div class="glass rounded-2xl p-6 {{ !$plan->is_active ? 'opacity-60' : '' }}">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">{{ $plan->name }}</h3>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $plan->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-gray-500/20 text-gray-400' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="mb-4">
                    <span class="text-3xl font-bold text-white">&euro;{{ number_format($plan->price / 100, 2) }}</span>
                    <span class="text-gray-400">/{{ $plan->billing_period === 'monthly' ? 'mo' : 'yr' }}</span>
                </div>

                @if($plan->limits)
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Monitors</span>
                            <span class="text-white">{{ $plan->limits->max_monitors }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Team Members</span>
                            <span class="text-white">{{ $plan->limits->max_team_members }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Check Interval</span>
                            <span class="text-white">{{ $plan->limits->check_interval_minutes }} min</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Data Retention</span>
                            <span class="text-white">{{ $plan->limits->retention_days }} days</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-6">
                        @if($plan->limits->api_access)
                            <span class="px-2 py-1 text-xs bg-indigo-500/20 text-indigo-400 rounded-full">API Access</span>
                        @endif
                        @if($plan->limits->custom_status_page)
                            <span class="px-2 py-1 text-xs bg-purple-500/20 text-purple-400 rounded-full">Status Page</span>
                        @endif
                        @if($plan->limits->sms_alerts)
                            <span class="px-2 py-1 text-xs bg-emerald-500/20 text-emerald-400 rounded-full">SMS Alerts</span>
                        @endif
                        @if($plan->limits->slack_integration)
                            <span class="px-2 py-1 text-xs bg-amber-500/20 text-amber-400 rounded-full">Slack</span>
                        @endif
                        @if($plan->limits->webhook_integration)
                            <span class="px-2 py-1 text-xs bg-pink-500/20 text-pink-400 rounded-full">Webhooks</span>
                        @endif
                    </div>
                @endif

                <div class="flex items-center space-x-2">
                    <button
                        wire:click="openEditModal({{ $plan->id }})"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-white/10 hover:bg-white/20 rounded-lg transition-colors"
                    >
                        Edit
                    </button>
                    <button
                        wire:click="togglePlanStatus({{ $plan->id }})"
                        class="px-4 py-2 text-sm font-medium {{ $plan->is_active ? 'text-red-400 bg-red-500/10 hover:bg-red-500/20' : 'text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20' }} rounded-lg transition-colors"
                    >
                        {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full glass rounded-2xl p-12 text-center">
                <p class="text-gray-500">No plans created yet.</p>
                <button
                    wire:click="openCreateModal"
                    class="mt-4 px-4 py-2 text-sm font-medium text-indigo-400 bg-indigo-500/10 hover:bg-indigo-500/20 rounded-lg transition-colors"
                >
                    Create your first plan
                </button>
            </div>
        @endforelse
    </div>

    <!-- Create/Edit Modal -->
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-on:click.self="$wire.closeModal()">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                <div class="relative glass-darker rounded-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-white">{{ $showCreateModal ? 'Create Plan' : 'Edit Plan' }}</h2>
                        <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="{{ $showCreateModal ? 'createPlan' : 'updatePlan' }}" class="space-y-6">
                        <!-- Basic Info -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Basic Information</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Plan Name</label>
                                    <input
                                        wire:model="name"
                                        type="text"
                                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    >
                                    @error('name') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Stripe Price ID</label>
                                    <input
                                        wire:model="stripe_price_id"
                                        type="text"
                                        placeholder="price_..."
                                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    >
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Price (cents)</label>
                                    <input
                                        wire:model="price"
                                        type="number"
                                        min="0"
                                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Billing Period</label>
                                    <select
                                        wire:model="billing_period"
                                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    >
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                            </div>

                            <label class="flex items-center space-x-2">
                                <input wire:model="is_active" type="checkbox" class="w-4 h-4 rounded bg-white/5 border-white/10 text-indigo-500">
                                <span class="text-sm text-gray-400">Plan is active</span>
                            </label>
                        </div>

                        <!-- Limits -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Limits</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Max Monitors</label>
                                    <input
                                        wire:model="max_monitors"
                                        type="number"
                                        min="1"
                                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Max Team Members</label>
                                    <input
                                        wire:model="max_team_members"
                                        type="number"
                                        min="1"
                                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Check Interval (minutes)</label>
                                    <input
                                        wire:model="check_interval_minutes"
                                        type="number"
                                        min="1"
                                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Data Retention (days)</label>
                                    <input
                                        wire:model="retention_days"
                                        type="number"
                                        min="1"
                                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Features</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center space-x-2">
                                    <input wire:model="api_access" type="checkbox" class="w-4 h-4 rounded bg-white/5 border-white/10 text-indigo-500">
                                    <span class="text-sm text-gray-400">API Access</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input wire:model="custom_status_page" type="checkbox" class="w-4 h-4 rounded bg-white/5 border-white/10 text-indigo-500">
                                    <span class="text-sm text-gray-400">Custom Status Page</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input wire:model="sms_alerts" type="checkbox" class="w-4 h-4 rounded bg-white/5 border-white/10 text-indigo-500">
                                    <span class="text-sm text-gray-400">SMS Alerts</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input wire:model="slack_integration" type="checkbox" class="w-4 h-4 rounded bg-white/5 border-white/10 text-indigo-500">
                                    <span class="text-sm text-gray-400">Slack Integration</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input wire:model="webhook_integration" type="checkbox" class="w-4 h-4 rounded bg-white/5 border-white/10 text-indigo-500">
                                    <span class="text-sm text-gray-400">Webhook Integration</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-white/5">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all"
                            >
                                {{ $showCreateModal ? 'Create Plan' : 'Update Plan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
