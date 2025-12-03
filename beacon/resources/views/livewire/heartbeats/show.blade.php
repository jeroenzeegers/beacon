<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex items-start justify-between mb-8">
        <div>
            <a href="{{ route('heartbeats.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white mb-4 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Heartbeats
            </a>
            <div class="flex items-center gap-3">
                @php
                    $statusColor = match($heartbeat->status) {
                        'healthy' => 'bg-emerald-500',
                        'late' => 'bg-amber-500',
                        'missing' => 'bg-red-500',
                        default => 'bg-slate-500',
                    };
                @endphp
                <span class="relative flex h-4 w-4">
                    @if($heartbeat->status === 'healthy')
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $statusColor }} opacity-75"></span>
                    @endif
                    <span class="relative inline-flex rounded-full h-4 w-4 {{ $statusColor }}"></span>
                </span>
                <h1 class="text-2xl font-bold text-white">{{ $heartbeat->name }}</h1>
                <span class="px-2 py-1 text-xs rounded-full capitalize
                    @if($heartbeat->status === 'healthy') bg-emerald-500/20 text-emerald-400
                    @elseif($heartbeat->status === 'late') bg-amber-500/20 text-amber-400
                    @elseif($heartbeat->status === 'missing') bg-red-500/20 text-red-400
                    @else bg-slate-500/20 text-slate-400
                    @endif">
                    {{ $heartbeat->status }}
                </span>
            </div>
            @if($heartbeat->description)
                <p class="text-slate-400 mt-2">{{ $heartbeat->description }}</p>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="toggleActive" class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white rounded-lg transition-colors">
                {{ $heartbeat->is_active ? 'Pause' : 'Resume' }}
            </button>
            <a href="{{ route('heartbeats.edit', $heartbeat->id) }}" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors">
                Edit
            </a>
            <button wire:click="delete" wire:confirm="Are you sure you want to delete this heartbeat?" class="px-4 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-lg transition-colors">
                Delete
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Ping URL Card -->
        <div class="lg:col-span-2 glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4">Ping URL</h2>
            <p class="text-sm text-slate-400 mb-4">Send a request to this URL from your cron job or scheduled task:</p>

            <div class="relative">
                <code class="block w-full p-4 bg-slate-900/50 rounded-lg text-emerald-400 text-sm font-mono break-all">
                    {{ $heartbeat->getPingUrl() }}
                </code>
                <button onclick="navigator.clipboard.writeText('{{ $heartbeat->getPingUrl() }}')"
                    class="absolute top-2 right-2 p-2 text-slate-400 hover:text-white transition-colors" title="Copy URL">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>

            <div class="mt-4 flex items-center gap-4">
                <button wire:click="regenerateSlug" wire:confirm="Regenerate URL? Old URL will stop working."
                    class="text-sm text-slate-400 hover:text-white transition-colors">
                    Regenerate URL
                </button>
            </div>

            <div class="mt-6 space-y-3">
                <h3 class="text-sm font-medium text-slate-300">Examples:</h3>
                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-slate-900/30 rounded-lg">
                        <p class="text-slate-400 mb-1">cURL:</p>
                        <code class="text-cyan-400">curl -X POST {{ $heartbeat->getPingUrl() }}</code>
                    </div>
                    <div class="p-3 bg-slate-900/30 rounded-lg">
                        <p class="text-slate-400 mb-1">wget:</p>
                        <code class="text-cyan-400">wget -q --spider {{ $heartbeat->getPingUrl() }}</code>
                    </div>
                    <div class="p-3 bg-slate-900/30 rounded-lg">
                        <p class="text-slate-400 mb-1">Report failure:</p>
                        <code class="text-cyan-400">curl -X POST {{ $heartbeat->getPingUrl() }}/fail</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4">Status</h2>

            <div class="space-y-4">
                <div>
                    <p class="text-sm text-slate-400">Expected Interval</p>
                    <p class="text-white font-medium">{{ $heartbeat->expected_interval }} minutes</p>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Grace Period</p>
                    <p class="text-white font-medium">{{ $heartbeat->grace_period }} minutes</p>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Last Ping</p>
                    <p class="text-white font-medium">
                        {{ $heartbeat->last_ping_at ? $heartbeat->last_ping_at->diffForHumans() : 'Never' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Next Expected</p>
                    <p class="text-white font-medium">
                        {{ $heartbeat->next_expected_at ? $heartbeat->next_expected_at->format('Y-m-d H:i:s') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Uptime (30 days)</p>
                    <p class="text-white font-medium">{{ $heartbeat->getUptimePercentage(30) }}%</p>
                </div>
                @if($heartbeat->project)
                    <div>
                        <p class="text-sm text-slate-400">Project</p>
                        <a href="{{ route('projects.show', $heartbeat->project_id) }}" class="text-violet-400 hover:text-violet-300 font-medium">
                            {{ $heartbeat->project->name }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ping History -->
    <div class="glass rounded-xl p-6 border border-white/10">
        <h2 class="text-lg font-semibold text-white mb-4">Ping History</h2>

        @if($pings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-slate-400 border-b border-white/10">
                            <th class="pb-3 font-medium">Time</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">IP Address</th>
                            <th class="pb-3 font-medium">User Agent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($pings as $ping)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-3 text-white">
                                    {{ $ping->pinged_at->format('Y-m-d H:i:s') }}
                                    <span class="text-xs text-slate-500">({{ $ping->pinged_at->diffForHumans() }})</span>
                                </td>
                                <td class="py-3">
                                    @if($ping->status === 'success')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-emerald-500/20 text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Success
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-400 text-sm font-mono">
                                    {{ $ping->ip_address ?? '-' }}
                                </td>
                                <td class="py-3 text-slate-400 text-sm truncate max-w-xs" title="{{ $ping->user_agent }}">
                                    {{ Str::limit($ping->user_agent, 50) ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $pings->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-slate-400">No pings recorded yet. Send your first ping to start monitoring.</p>
            </div>
        @endif
    </div>
</div>
