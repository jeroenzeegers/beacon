<x-slot name="header">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-white transition-colors" wire:navigate>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gradient">
                {{ $project->name }}
            </h2>
            @php
                $envColors = [
                    'production' => 'bg-red-500/10 text-red-400 border-red-500/20',
                    'staging' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                    'development' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                ];
            @endphp
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $envColors[$project->environment] ?? 'bg-gray-500/10 text-gray-400 border-gray-500/20' }}">
                {{ ucfirst($project->environment ?? 'N/A') }}
            </span>
        </div>
        <a href="{{ route('projects.edit', $project->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium text-gray-300 bg-white/5 border border-white/10 hover:bg-white/10 transition-colors" wire:navigate>
            Edit
        </a>
    </div>
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

        <!-- Project Info -->
        @if($project->description)
            <div class="glass rounded-2xl mb-6 p-6 scroll-reveal">
                <p class="text-gray-400">{{ $project->description }}</p>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="bento-grid mb-8 stagger-list">
            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500/20 to-indigo-500/20 flex items-center justify-center float-depth-1">
                        <svg class="h-6 w-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Monitors</p>
                        <p class="text-3xl font-bold text-white text-fluid">{{ $monitors->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-green-500/20 flex items-center justify-center status-online float-depth-2">
                        <svg class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Monitors Up</p>
                        <p class="text-3xl font-bold text-emerald-400 text-fluid">{{ $monitors->where('status', 'up')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500/20 to-rose-500/20 flex items-center justify-center {{ $monitors->where('status', 'down')->count() > 0 ? 'status-offline' : '' }} float-depth-1">
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Monitors Down</p>
                        <p class="text-3xl font-bold text-red-400 text-fluid">{{ $monitors->where('status', 'down')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-yellow-500/20 flex items-center justify-center {{ $activeIncidents->count() > 0 ? 'status-degraded' : '' }} float-depth-3">
                        <svg class="h-6 w-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Active Incidents</p>
                        <p class="text-3xl font-bold text-amber-400 text-fluid">{{ $activeIncidents->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monitors List -->
        <div class="glass rounded-2xl overflow-hidden scroll-reveal card-hover-shine">
            <div class="px-6 py-5 border-b border-white/5 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white">Monitors</h3>
                <a href="{{ route('monitors.create') }}?project_id={{ $project->id }}" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-4 py-2 text-sm font-medium" wire:navigate>
                    <span class="btn-magnetic-inner flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Monitor
                    </span>
                </a>
            </div>
            <ul role="list" class="divide-y divide-white/5">
                @forelse($monitors as $monitor)
                    <li wire:key="project-monitor-{{ $monitor->id }}">
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
                                            <span class="text-cyan-400 font-mono">{{ $monitor->latestCheck->response_time }}ms</span>
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
                        <p class="text-gray-400 mb-3">No monitors in this project yet.</p>
                        <a href="{{ route('monitors.create') }}?project_id={{ $project->id }}" class="inline-flex items-center gap-2 text-sm text-indigo-400 hover:text-indigo-300 transition-colors" wire:navigate>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add your first monitor
                        </a>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
