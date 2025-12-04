<x-slot name="header">
    <h2 class="text-2xl font-bold text-gradient">
        {{ __('Dashboard') }}
    </h2>
</x-slot>

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-6 glass rounded-xl p-4 border-l-4 border-emerald-500 scroll-reveal">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-emerald-300">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats Cards - Bento Grid -->
        <div class="bento-grid mb-8 stagger-list">
            <!-- Total Monitors -->
            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500/20 to-indigo-500/20 flex items-center justify-center float-depth-1">
                        <svg class="h-6 w-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Monitors</p>
                        <p class="text-3xl font-bold text-white text-fluid">{{ $stats['total_monitors'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Monitors Up -->
            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-green-500/20 flex items-center justify-center status-online float-depth-2">
                        <svg class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Monitors Up</p>
                        <p class="text-3xl font-bold text-emerald-400 text-fluid">{{ $stats['monitors_up'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Monitors Down -->
            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500/20 to-rose-500/20 flex items-center justify-center {{ $stats['monitors_down'] > 0 ? 'status-offline animate-pulse-glow' : '' }} float-depth-1">
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Monitors Down</p>
                        <p class="text-3xl font-bold text-red-400 text-fluid">{{ $stats['monitors_down'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Incidents -->
            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-yellow-500/20 flex items-center justify-center {{ $stats['active_incidents'] > 0 ? 'status-degraded' : '' }} float-depth-3">
                        <svg class="h-6 w-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Active Incidents</p>
                        <p class="text-3xl font-bold text-amber-400 text-fluid">{{ $stats['active_incidents'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Monitors List -->
            <div class="lg:col-span-2 scroll-reveal">
                <div class="glass rounded-2xl overflow-hidden card-hover-shine">
                    <div class="px-6 py-5 border-b border-white/5 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white">Monitors</h3>
                        <a href="{{ route('monitors.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1 group" wire:navigate>
                            View all
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <ul role="list" class="divide-y divide-white/5">
                        @forelse($monitors->take(5) as $monitor)
                            <li wire:key="monitor-{{ $monitor->id }}">
                                <a href="{{ route('monitors.show', $monitor->id) }}" class="block hover:bg-white/[0.02] transition-colors" wire:navigate>
                                    <div class="px-6 py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                @if($monitor->status === 'up')
                                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-400 status-online animate-breathe"></span>
                                                @elseif($monitor->status === 'down')
                                                    <span class="h-2.5 w-2.5 rounded-full bg-red-400 status-offline animate-pulse"></span>
                                                @elseif($monitor->status === 'degraded')
                                                    <span class="h-2.5 w-2.5 rounded-full bg-amber-400 status-degraded"></span>
                                                @else
                                                    <span class="h-2.5 w-2.5 rounded-full bg-gray-400"></span>
                                                @endif
                                                <p class="text-sm font-medium text-white truncate text-fluid">{{ $monitor->name }}</p>
                                            </div>
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-white/5 text-gray-300 border border-white/10">
                                                {{ strtoupper($monitor->type) }}
                                            </span>
                                        </div>
                                        <div class="mt-2 flex justify-between items-center">
                                            <p class="text-sm text-gray-500 truncate">{{ $monitor->url ?? $monitor->host }}</p>
                                            <span class="text-sm text-gray-400">
                                                @if($monitor->latestCheck)
                                                    <span class="text-cyan-400">{{ $monitor->latestCheck->response_time }}ms</span>
                                                @else
                                                    <span class="text-gray-500">No checks yet</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="px-6 py-12 text-center">
                                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-violet-500/10 to-indigo-500/10 flex items-center justify-center mb-4 float-depth-1">
                                    <svg class="w-8 h-8 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <p class="text-gray-400">No monitors yet.</p>
                                <a href="{{ route('monitors.create') }}" class="mt-3 inline-flex items-center gap-2 text-sm text-indigo-400 hover:text-indigo-300 transition-colors" wire:navigate>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create your first monitor
                                </a>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6 stagger-reveal">
                <!-- Active Incidents -->
                <div class="glass rounded-2xl overflow-hidden scroll-reveal-right">
                    <div class="px-6 py-5 border-b border-white/5">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            Active Incidents
                            @if(count($activeIncidents) > 0)
                                <span class="notification-dot"></span>
                            @endif
                        </h3>
                    </div>
                    <ul role="list" class="divide-y divide-white/5">
                        @forelse($activeIncidents as $incident)
                            <li class="px-6 py-4 hover:bg-white/[0.02] transition-colors" wire:key="incident-{{ $incident->id }}">
                                <div class="flex items-center gap-3">
                                    <span class="h-2 w-2 rounded-full bg-red-400 status-offline animate-pulse flex-shrink-0"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-white truncate">{{ $incident->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $incident->monitor?->name ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-6 py-8 text-center">
                                <div class="w-12 h-12 mx-auto rounded-xl bg-gradient-to-br from-emerald-500/20 to-green-500/20 flex items-center justify-center mb-3 status-online">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-emerald-400 font-medium">All systems operational</p>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Usage Limits -->
                <div class="glass rounded-2xl overflow-hidden scroll-reveal-right">
                    <div class="px-6 py-5 border-b border-white/5">
                        <h3 class="text-lg font-semibold text-white">Usage</h3>
                    </div>
                    <div class="px-6 py-5 space-y-5">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-400">Monitors</span>
                                <span class="text-white font-medium">{{ $stats['total_monitors'] }} / {{ $remainingLimits['monitors']['limit'] ?? '∞' }}</span>
                            </div>
                            @if(isset($remainingLimits['monitors']['limit']))
                                <div class="progress-liquid">
                                    <div class="progress-liquid-bar" style="width: {{ min(100, ($stats['total_monitors'] / $remainingLimits['monitors']['limit']) * 100) }}%"></div>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-400">Projects</span>
                                <span class="text-white font-medium">{{ $stats['total_projects'] }} / {{ $remainingLimits['projects']['limit'] ?? '∞' }}</span>
                            </div>
                            @if(isset($remainingLimits['projects']['limit']))
                                <div class="progress-liquid">
                                    <div class="progress-liquid-bar" style="width: {{ min(100, ($stats['total_projects'] / $remainingLimits['projects']['limit']) * 100) }}%"></div>
                                </div>
                            @endif
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('billing.dashboard') }}" class="btn-magnetic inline-flex items-center gap-2 text-sm text-indigo-400 hover:text-indigo-300 transition-colors" wire:navigate>
                                <span class="btn-magnetic-inner">Manage subscription</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
