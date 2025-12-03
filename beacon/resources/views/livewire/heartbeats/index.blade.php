<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Heartbeats</h1>
            <p class="text-slate-400 mt-1">Monitor your cron jobs and scheduled tasks</p>
        </div>
        <a href="{{ route('heartbeats.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Heartbeat
        </a>
    </div>

    <!-- Filters -->
    <div class="glass rounded-xl p-4 mb-6 border border-white/10">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search heartbeats..."
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50">
            </div>
            <select wire:model.live="statusFilter" class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                <option value="">All Statuses</option>
                <option value="healthy">Healthy</option>
                <option value="late">Late</option>
                <option value="missing">Missing</option>
                <option value="pending">Pending</option>
            </select>
        </div>
    </div>

    <!-- Heartbeats Grid -->
    @if($heartbeats->count() > 0)
        <div class="grid gap-4">
            @foreach($heartbeats as $heartbeat)
                <div class="glass rounded-xl p-5 border border-white/10 hover:border-white/20 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <!-- Status Indicator -->
                            <div class="mt-1">
                                @php
                                    $statusColor = match($heartbeat->status) {
                                        'healthy' => 'bg-emerald-500',
                                        'late' => 'bg-amber-500',
                                        'missing' => 'bg-red-500',
                                        default => 'bg-slate-500',
                                    };
                                @endphp
                                <span class="relative flex h-3 w-3">
                                    @if($heartbeat->status === 'healthy')
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $statusColor }} opacity-75"></span>
                                    @endif
                                    <span class="relative inline-flex rounded-full h-3 w-3 {{ $statusColor }}"></span>
                                </span>
                            </div>

                            <div>
                                <a href="{{ route('heartbeats.show', $heartbeat->id) }}" class="text-lg font-semibold text-white hover:text-violet-400 transition-colors">
                                    {{ $heartbeat->name }}
                                </a>
                                @if($heartbeat->description)
                                    <p class="text-sm text-slate-400 mt-1">{{ Str::limit($heartbeat->description, 100) }}</p>
                                @endif

                                <div class="flex flex-wrap items-center gap-4 mt-3 text-sm">
                                    <span class="flex items-center gap-1 text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Every {{ $heartbeat->expected_interval }} min
                                    </span>
                                    <span class="flex items-center gap-1 text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $heartbeat->grace_period }} min grace
                                    </span>
                                    @if($heartbeat->last_ping_at)
                                        <span class="flex items-center gap-1 text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Last ping {{ $heartbeat->last_ping_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">Awaiting first ping</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs rounded-full capitalize
                                @if($heartbeat->status === 'healthy') bg-emerald-500/20 text-emerald-400
                                @elseif($heartbeat->status === 'late') bg-amber-500/20 text-amber-400
                                @elseif($heartbeat->status === 'missing') bg-red-500/20 text-red-400
                                @else bg-slate-500/20 text-slate-400
                                @endif">
                                {{ $heartbeat->status }}
                            </span>

                            <button wire:click="toggleActive({{ $heartbeat->id }})" class="p-2 text-slate-400 hover:text-white transition-colors" title="{{ $heartbeat->is_active ? 'Pause' : 'Resume' }}">
                                @if($heartbeat->is_active)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </button>

                            <a href="{{ route('heartbeats.edit', $heartbeat->id) }}" class="p-2 text-slate-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $heartbeats->links() }}
        </div>
    @else
        <div class="glass rounded-xl p-12 border border-white/10 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-violet-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">No heartbeats yet</h3>
            <p class="text-slate-400 mb-6">Create your first heartbeat to start monitoring cron jobs and scheduled tasks.</p>
            <a href="{{ route('heartbeats.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Heartbeat
            </a>
        </div>
    @endif
</div>
