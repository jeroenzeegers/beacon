<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Plans & Pricing') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Flash Messages -->
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

        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Choose the right plan for your team
            </h1>
            <p class="mt-4 text-xl text-gray-600">
                All plans include a 14-day free trial. No credit card required.
            </p>
        </div>

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            @foreach($plans as $plan)
                @php
                    $isCurrentPlan = $currentPlan && $currentPlan->id === $plan->id;
                    $limits = $plan->limits->keyBy('feature');
                @endphp
                <div class="bg-white rounded-lg shadow-lg overflow-hidden {{ $plan->is_popular ? 'ring-2 ring-indigo-500' : '' }}">
                    @if($plan->is_popular)
                        <div class="bg-indigo-500 text-center py-1">
                            <span class="text-xs font-semibold uppercase tracking-wide text-white">Most Popular</span>
                        </div>
                    @endif

                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $plan->name }}</h2>
                        <p class="mt-2 text-sm text-gray-500">{{ $plan->description }}</p>

                        <div class="mt-4">
                            @if($plan->price > 0)
                                <span class="text-4xl font-extrabold text-gray-900">&euro;{{ number_format($plan->price / 100, 0) }}</span>
                                <span class="text-base font-medium text-gray-500">/month</span>
                            @else
                                <span class="text-4xl font-extrabold text-gray-900">Free</span>
                            @endif
                        </div>

                        <ul class="mt-6 space-y-3">
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="ml-3 text-sm text-gray-700">
                                    {{ $limits->get('monitors')?->value ?? 'Unlimited' }} monitors
                                </span>
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="ml-3 text-sm text-gray-700">
                                    {{ $limits->get('projects')?->value ?? 'Unlimited' }} projects
                                </span>
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="ml-3 text-sm text-gray-700">
                                    {{ $limits->get('team_members')?->value ?? 'Unlimited' }} team members
                                </span>
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="ml-3 text-sm text-gray-700">
                                    {{ $limits->get('check_interval')?->value ?? 60 }}s check interval
                                </span>
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="ml-3 text-sm text-gray-700">
                                    {{ $limits->get('data_retention_days')?->value ?? 30 }} days data retention
                                </span>
                            </li>
                            @if($limits->get('status_pages'))
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="ml-3 text-sm text-gray-700">
                                        {{ $limits->get('status_pages')?->value ?? 0 }} status pages
                                    </span>
                                </li>
                            @endif
                            @if($limits->get('api_access')?->value)
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="ml-3 text-sm text-gray-700">API access</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="px-6 pb-6">
                        @if($isCurrentPlan)
                            <button disabled class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-500 bg-gray-100 cursor-not-allowed">
                                Current Plan
                            </button>
                        @elseif($plan->price === 0)
                            <button wire:click="downgradeToFree" wire:confirm="Are you sure you want to cancel your subscription?" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Downgrade to Free
                            </button>
                        @else
                            <button wire:click="checkout({{ $plan->id }})" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white {{ $plan->is_popular ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-800 hover:bg-gray-900' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ $subscribed ? 'Switch Plan' : 'Start Free Trial' }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Frequently Asked Questions</h2>
            <div class="max-w-3xl mx-auto divide-y divide-gray-200">
                <div class="py-6">
                    <h3 class="text-lg font-medium text-gray-900">Can I change plans at any time?</h3>
                    <p class="mt-2 text-gray-500">Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately.</p>
                </div>
                <div class="py-6">
                    <h3 class="text-lg font-medium text-gray-900">What happens when my trial ends?</h3>
                    <p class="mt-2 text-gray-500">If you don't subscribe, your account will be downgraded to the free plan with limited features.</p>
                </div>
                <div class="py-6">
                    <h3 class="text-lg font-medium text-gray-900">Is there a refund policy?</h3>
                    <p class="mt-2 text-gray-500">Yes, we offer a 30-day money-back guarantee. Contact support if you're not satisfied.</p>
                </div>
            </div>
        </div>
    </div>
</div>
