<x-slot name="header">
    <h2 class="font-semibold text-xl text-white leading-tight">
        {{ __('Billing') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="glass-liquid rounded-xl p-4 border border-emerald-500/30 scroll-reveal" style="--delay: 0">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-emerald-300">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="glass-liquid rounded-xl p-4 border border-red-500/30 scroll-reveal" style="--delay: 0">
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

        <!-- Trial Banner -->
        @if($onTrial)
            <div class="glass-liquid rounded-xl p-5 border border-cyan-500/30 scroll-reveal" style="--delay: 0.1s">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/30 to-blue-500/30 flex items-center justify-center">
                        <svg class="h-6 w-6 text-cyan-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-cyan-300">Trial Period</h3>
                        <p class="mt-1 text-sm text-slate-400">Your trial ends {{ $trialEndsAt?->diffForHumans() }}. Subscribe to a plan to continue using all features.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Current Plan -->
        <div class="glass-liquid rounded-2xl overflow-hidden scroll-reveal" style="--delay: 0.2s">
            <div class="p-6 sm:p-8">
                <div class="sm:flex sm:items-center sm:justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Current Plan
                        </h3>
                        <div class="mt-2 text-sm text-slate-400">
                            @if($currentPlan)
                                <p>You are currently on the <span class="text-violet-300 font-medium">{{ $currentPlan->name }}</span> plan.</p>
                            @else
                                <p>You don't have an active subscription.</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-0 flex flex-wrap items-center gap-3">
                        @if($hasStripeId)
                            <button wire:click="redirectToPortal" class="inline-flex items-center px-4 py-2.5 border border-slate-600 text-sm font-medium rounded-xl text-slate-300 bg-slate-800/50 hover:bg-slate-700/50 hover:border-slate-500 transition-all duration-300 focus-ring">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Manage Billing
                            </button>
                        @endif
                        <a href="{{ route('billing.plans') }}" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center px-5 py-2.5 text-sm font-semibold rounded-xl text-white" wire:navigate>
                            <span class="btn-magnetic-inner inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                {{ $subscribed ? 'Change Plan' : 'Subscribe' }}
                            </span>
                        </a>
                    </div>
                </div>

                @if($currentPlan)
                    <div class="mt-6 pt-6 border-t border-slate-700/50">
                        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="glass-liquid rounded-xl p-4 tilt-3d">
                                <dt class="text-sm font-medium text-slate-400 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Price
                                </dt>
                                <dd class="mt-2 text-2xl font-bold text-white">
                                    @if($currentPlan->price > 0)
                                        <span class="bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">&euro;{{ number_format($currentPlan->price / 100, 2) }}</span>
                                        <span class="text-sm font-normal text-slate-400">/month</span>
                                    @else
                                        <span class="bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">Free</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="glass-liquid rounded-xl p-4 tilt-3d">
                                <dt class="text-sm font-medium text-slate-400 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Status
                                </dt>
                                <dd class="mt-2">
                                    @if($onTrial)
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                            Trial
                                        </span>
                                    @elseif($subscribed)
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-500/20 text-slate-400 border border-slate-500/30">
                                            <span class="w-2 h-2 rounded-full bg-slate-500"></span>
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
        <div class="glass-liquid rounded-2xl overflow-hidden scroll-reveal" style="--delay: 0.3s">
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500/30 to-indigo-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Usage</h3>
                        <p class="text-sm text-slate-400">Current usage compared to your plan limits.</p>
                    </div>
                </div>

                <div class="space-y-5 stagger-list">
                    <!-- Monitors -->
                    <div class="glass-liquid rounded-xl p-4 stagger-item" style="--stagger: 0">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Monitors
                            </span>
                            <span class="text-sm font-semibold text-slate-300">
                                {{ $remainingLimits['monitors']['used'] ?? 0 }} / {{ $remainingLimits['monitors']['limit'] ?? 'Unlimited' }}
                            </span>
                        </div>
                        @if(isset($remainingLimits['monitors']['limit']) && $remainingLimits['monitors']['limit'] > 0)
                            <div class="progress-liquid h-2">
                                <div class="progress-liquid-fill" style="--progress: {{ min(100, (($remainingLimits['monitors']['used'] ?? 0) / $remainingLimits['monitors']['limit']) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Projects -->
                    <div class="glass-liquid rounded-xl p-4 stagger-item" style="--stagger: 1">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                Projects
                            </span>
                            <span class="text-sm font-semibold text-slate-300">
                                {{ $remainingLimits['projects']['used'] ?? 0 }} / {{ $remainingLimits['projects']['limit'] ?? 'Unlimited' }}
                            </span>
                        </div>
                        @if(isset($remainingLimits['projects']['limit']) && $remainingLimits['projects']['limit'] > 0)
                            <div class="progress-liquid h-2">
                                <div class="progress-liquid-fill" style="--progress: {{ min(100, (($remainingLimits['projects']['used'] ?? 0) / $remainingLimits['projects']['limit']) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Team Members -->
                    <div class="glass-liquid rounded-xl p-4 stagger-item" style="--stagger: 2">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Team Members
                            </span>
                            <span class="text-sm font-semibold text-slate-300">
                                {{ $remainingLimits['team_members']['used'] ?? 0 }} / {{ $remainingLimits['team_members']['limit'] ?? 'Unlimited' }}
                            </span>
                        </div>
                        @if(isset($remainingLimits['team_members']['limit']) && $remainingLimits['team_members']['limit'] > 0)
                            <div class="progress-liquid h-2">
                                <div class="progress-liquid-fill" style="--progress: {{ min(100, (($remainingLimits['team_members']['used'] ?? 0) / $remainingLimits['team_members']['limit']) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
