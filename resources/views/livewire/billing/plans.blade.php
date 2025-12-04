<x-slot name="header">
    <h2 class="font-semibold text-xl text-white leading-tight">
        {{ __('Plans & Pricing') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if (session()->has('error'))
            <div class="mb-6 glass-liquid rounded-xl p-4 border border-red-500/30 scroll-reveal" style="--delay: 0">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="text-center mb-12 scroll-reveal" style="--delay: 0.1s">
            <h1 class="text-3xl font-extrabold sm:text-4xl bg-gradient-to-r from-white via-violet-200 to-cyan-200 bg-clip-text text-transparent">
                Choose the right plan for your team
            </h1>
            <p class="mt-4 text-xl text-slate-400">
                All plans include a 14-day free trial. No credit card required.
            </p>
        </div>

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4 stagger-list">
            @foreach($plans as $index => $plan)
                @php
                    $isCurrentPlan = $currentPlan && $currentPlan->id === $plan->id;
                    $limits = $plan->limits->keyBy('feature');
                @endphp
                <div class="glass-liquid rounded-2xl overflow-hidden tilt-3d stagger-item {{ $plan->is_popular ? 'ring-2 ring-violet-500/50 shadow-lg shadow-violet-500/20' : '' }}" style="--stagger: {{ $index }}">
                    @if($plan->is_popular)
                        <div class="bg-gradient-to-r from-violet-600 to-indigo-600 text-center py-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-white flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Most Popular
                            </span>
                        </div>
                    @endif

                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-white">{{ $plan->name }}</h2>
                        <p class="mt-2 text-sm text-slate-400">{{ $plan->description }}</p>

                        <div class="mt-4">
                            @if($plan->price > 0)
                                <span class="text-4xl font-extrabold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">&euro;{{ number_format($plan->price / 100, 0) }}</span>
                                <span class="text-base font-medium text-slate-400">/month</span>
                            @else
                                <span class="text-4xl font-extrabold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">Free</span>
                            @endif
                        </div>

                        <ul class="mt-6 space-y-3">
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-slate-300">
                                    <span class="font-semibold text-white">{{ $limits->get('monitors')?->value ?? 'Unlimited' }}</span> monitors
                                </span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-slate-300">
                                    <span class="font-semibold text-white">{{ $limits->get('projects')?->value ?? 'Unlimited' }}</span> projects
                                </span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-slate-300">
                                    <span class="font-semibold text-white">{{ $limits->get('team_members')?->value ?? 'Unlimited' }}</span> team members
                                </span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-slate-300">
                                    <span class="font-semibold text-white">{{ $limits->get('check_interval')?->value ?? 60 }}s</span> check interval
                                </span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-slate-300">
                                    <span class="font-semibold text-white">{{ $limits->get('data_retention_days')?->value ?? 30 }}</span> days data retention
                                </span>
                            </li>
                            @if($limits->get('status_pages'))
                                <li class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center mt-0.5">
                                        <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-slate-300">
                                        <span class="font-semibold text-white">{{ $limits->get('status_pages')?->value ?? 0 }}</span> status pages
                                    </span>
                                </li>
                            @endif
                            @if($limits->get('api_access')?->value)
                                <li class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-violet-500/20 flex items-center justify-center mt-0.5">
                                        <svg class="w-3 h-3 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-violet-300 font-medium">API access</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="px-6 pb-6">
                        @if($isCurrentPlan)
                            <button disabled class="w-full inline-flex justify-center items-center px-4 py-3 border border-slate-600 text-sm font-medium rounded-xl text-slate-500 bg-slate-800/50 cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Current Plan
                            </button>
                        @elseif($plan->price === 0)
                            <button wire:click="downgradeToFree" wire:confirm="Are you sure you want to cancel your subscription?" class="w-full inline-flex justify-center items-center px-4 py-3 border border-slate-600 text-sm font-medium rounded-xl text-slate-300 bg-slate-800/50 hover:bg-slate-700/50 hover:border-slate-500 transition-all duration-300 focus-ring">
                                Downgrade to Free
                            </button>
                        @else
                            <button wire:click="checkout({{ $plan->id }})" class="btn-liquid btn-magnetic ripple-effect w-full inline-flex justify-center items-center px-4 py-3 text-sm font-semibold rounded-xl text-white {{ $plan->is_popular ? '' : 'opacity-90 hover:opacity-100' }}">
                                <span class="btn-magnetic-inner inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    {{ $subscribed ? 'Switch Plan' : 'Start Free Trial' }}
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- FAQ Section -->
        <div class="mt-16 scroll-reveal" style="--delay: 0.4s">
            <h2 class="text-2xl font-bold text-white text-center mb-8">Frequently Asked Questions</h2>
            <div class="max-w-3xl mx-auto glass-liquid rounded-2xl divide-y divide-slate-700/50 overflow-hidden">
                <div class="p-6 hover:bg-slate-800/30 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Can I change plans at any time?
                    </h3>
                    <p class="mt-2 text-slate-400">Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately.</p>
                </div>
                <div class="p-6 hover:bg-slate-800/30 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        What happens when my trial ends?
                    </h3>
                    <p class="mt-2 text-slate-400">If you don't subscribe, your account will be downgraded to the free plan with limited features.</p>
                </div>
                <div class="p-6 hover:bg-slate-800/30 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Is there a refund policy?
                    </h3>
                    <p class="mt-2 text-slate-400">Yes, we offer a 30-day money-back guarantee. Contact support if you're not satisfied.</p>
                </div>
            </div>
        </div>
    </div>
</div>
