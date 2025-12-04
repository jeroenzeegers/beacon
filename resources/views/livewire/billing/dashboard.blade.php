<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Billing') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-4 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Trial Banner -->
        @if($onTrial)
            <div class="mb-6 rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Trial Period</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Your trial ends {{ $trialEndsAt?->diffForHumans() }}. Subscribe to a plan to continue using all features.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Current Plan -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Current Plan</h3>
                        <div class="mt-2 max-w-xl text-sm text-gray-500">
                            @if($currentPlan)
                                <p>You are currently on the <strong>{{ $currentPlan->name }}</strong> plan.</p>
                            @else
                                <p>You don't have an active subscription.</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-0 sm:flex sm:items-center sm:space-x-3">
                        @if($hasStripeId)
                            <button wire:click="redirectToPortal" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Manage Billing
                            </button>
                        @endif
                        <a href="{{ route('billing.plans') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:navigate>
                            {{ $subscribed ? 'Change Plan' : 'Subscribe' }}
                        </a>
                    </div>
                </div>

                @if($currentPlan)
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Price</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($currentPlan->price > 0)
                                        &euro;{{ number_format($currentPlan->price / 100, 2) }}/month
                                    @else
                                        Free
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($onTrial)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Trial
                                        </span>
                                    @elseif($subscribed)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                @endif
            </div>
        </div>

        <!-- Usage -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Usage</h3>
                <p class="mt-1 text-sm text-gray-500">Current usage compared to your plan limits.</p>

                <div class="mt-6 space-y-6">
                    <!-- Monitors -->
                    <div>
                        <div class="flex justify-between text-sm font-medium">
                            <span class="text-gray-700">Monitors</span>
                            <span class="text-gray-500">
                                {{ $remainingLimits['monitors']['used'] ?? 0 }} / {{ $remainingLimits['monitors']['limit'] ?? 'Unlimited' }}
                            </span>
                        </div>
                        @if(isset($remainingLimits['monitors']['limit']) && $remainingLimits['monitors']['limit'] > 0)
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, (($remainingLimits['monitors']['used'] ?? 0) / $remainingLimits['monitors']['limit']) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Projects -->
                    <div>
                        <div class="flex justify-between text-sm font-medium">
                            <span class="text-gray-700">Projects</span>
                            <span class="text-gray-500">
                                {{ $remainingLimits['projects']['used'] ?? 0 }} / {{ $remainingLimits['projects']['limit'] ?? 'Unlimited' }}
                            </span>
                        </div>
                        @if(isset($remainingLimits['projects']['limit']) && $remainingLimits['projects']['limit'] > 0)
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, (($remainingLimits['projects']['used'] ?? 0) / $remainingLimits['projects']['limit']) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Team Members -->
                    <div>
                        <div class="flex justify-between text-sm font-medium">
                            <span class="text-gray-700">Team Members</span>
                            <span class="text-gray-500">
                                {{ $remainingLimits['team_members']['used'] ?? 0 }} / {{ $remainingLimits['team_members']['limit'] ?? 'Unlimited' }}
                            </span>
                        </div>
                        @if(isset($remainingLimits['team_members']['limit']) && $remainingLimits['team_members']['limit'] > 0)
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, (($remainingLimits['team_members']['used'] ?? 0) / $remainingLimits['team_members']['limit']) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
