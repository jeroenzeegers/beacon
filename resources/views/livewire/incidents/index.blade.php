<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gradient">
            {{ __('Incidents') }}
        </h2>
        <a href="{{ route('incidents.create') }}" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium" wire:navigate>
            <span class="btn-magnetic-inner flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Report Incident
            </span>
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

        <!-- Filters -->
        <div class="glass rounded-2xl mb-6 scroll-reveal">
            <div class="p-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div class="sm:col-span-2">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="search" id="search" class="input-liquid block w-full pl-11 pr-4" placeholder="Search incidents...">
                        </div>
                    </div>
                    <div>
                        <label for="status" class="sr-only">Status</label>
                        <select wire:model.live="status" id="status" class="input-liquid block w-full">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="severity" class="sr-only">Severity</label>
                        <select wire:model.live="severity" id="severity" class="input-liquid block w-full">
                            <option value="">All Severities</option>
                            @foreach($severities as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incidents List -->
        <div class="space-y-4">
            @forelse($incidents as $incident)
                <div class="glass rounded-2xl p-6 scroll-reveal group" wire:key="incident-{{ $incident->id }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <!-- Severity Badge -->
                                @if($incident->severity === 'critical')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                        Critical
                                    </span>
                                @elseif($incident->severity === 'major')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Major
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        Minor
                                    </span>
                                @endif

                                <!-- Status Badge -->
                                @if($incident->status === 'resolved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Resolved
                                    </span>
                                @elseif($incident->status === 'investigating')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-violet-500/10 text-violet-400 border border-violet-500/20">
                                        <span class="h-1.5 w-1.5 rounded-full bg-violet-400 animate-pulse"></span>
                                        Investigating
                                    </span>
                                @elseif($incident->status === 'identified')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                        Identified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                        Monitoring
                                    </span>
                                @endif

                                @if($incident->is_public)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs text-gray-500">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                        Public
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('incidents.show', $incident->id) }}" class="text-lg font-semibold text-white hover:text-indigo-400 transition-colors" wire:navigate>
                                {{ $incident->title }}
                            </a>

                            @if($incident->description)
                                <p class="text-sm text-gray-400 mt-1 line-clamp-2">{{ $incident->description }}</p>
                            @endif

                            <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                                @if($incident->monitor)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span>{{ $incident->monitor->name }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Started {{ $incident->started_at?->diffForHumans() ?? 'N/A' }}</span>
                                </div>
                                @if($incident->duration)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span>Duration: {{ $incident->duration }} min</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if($incident->isActive())
                                <button wire:click="resolve({{ $incident->id }})" wire:confirm="Mark this incident as resolved?" class="px-3 py-1.5 text-sm text-emerald-400 hover:text-emerald-300 border border-emerald-500/30 hover:border-emerald-500/50 rounded-lg transition-colors">
                                    Resolve
                                </button>
                            @endif
                            <a href="{{ route('incidents.show', $incident->id) }}" class="p-2 text-indigo-400 hover:text-indigo-300 transition-colors" wire:navigate>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <button wire:click="delete({{ $incident->id }})" wire:confirm="Are you sure you want to delete this incident?" class="p-2 text-red-400 hover:text-red-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="glass rounded-2xl p-12 text-center">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-emerald-500/10 to-cyan-500/10 flex items-center justify-center mb-4 float-depth-1">
                        <svg class="w-10 h-10 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-1">No incidents</h3>
                    <p class="text-gray-500 mb-6">Great news! There are no incidents to report.</p>
                    <a href="{{ route('incidents.create') }}" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium" wire:navigate>
                        <span class="btn-magnetic-inner flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Report Incident
                        </span>
                    </a>
                </div>
            @endforelse
        </div>

        @if($incidents->hasPages())
            <div class="mt-6">
                {{ $incidents->links() }}
            </div>
        @endif
    </div>
</div>
