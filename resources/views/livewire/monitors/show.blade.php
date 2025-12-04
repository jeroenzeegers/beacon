<x-slot name="header">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('monitors.index') }}" class="text-gray-400 hover:text-white transition-colors" wire:navigate>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gradient">
                {{ $monitor->name }}
            </h2>
            @if($monitor->status === 'up')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 status-online animate-breathe"></span>
                    Up
                </span>
            @elseif($monitor->status === 'down')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-400 status-offline animate-pulse"></span>
                    Down
                </span>
            @elseif($monitor->status === 'degraded')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400 status-degraded"></span>
                    Degraded
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-500/10 text-gray-400 border border-gray-500/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                    Pending
                </span>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="toggleActive" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $monitor->is_active ? 'text-gray-300 bg-white/5 border border-white/10 hover:bg-white/10' : 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 hover:bg-emerald-500/20' }}">
                {{ $monitor->is_active ? 'Pause' : 'Resume' }}
            </button>
            <a href="{{ route('monitors.edit', $monitor->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium text-gray-300 bg-white/5 border border-white/10 hover:bg-white/10 transition-colors" wire:navigate>
                Edit
            </a>
        </div>
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

        <!-- Stats Cards -->
        <div class="bento-grid mb-8 stagger-list">
            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center float-depth-1">
                        <svg class="h-6 w-6 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Current Response</p>
                        <p class="text-3xl font-bold text-cyan-400 text-fluid font-mono">{{ $monitor->latestCheck?->response_time ?? '-' }}ms</p>
                    </div>
                </div>
            </div>

            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500/20 to-indigo-500/20 flex items-center justify-center float-depth-2">
                        <svg class="h-6 w-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Avg Response (24h)</p>
                        <p class="text-3xl font-bold text-white text-fluid font-mono">{{ number_format($avgResponseTime) }}ms</p>
                    </div>
                </div>
            </div>

            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-green-500/20 flex items-center justify-center status-online float-depth-1">
                        <svg class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Uptime (30d)</p>
                        <p class="text-3xl font-bold text-emerald-400 text-fluid font-mono">{{ number_format($uptime, 2) }}%</p>
                    </div>
                </div>
            </div>

            <div class="glass-liquid tilt-3d p-6 hover-lift">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-500/20 to-slate-500/20 flex items-center justify-center float-depth-3">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Last Checked</p>
                        <p class="text-xl font-bold text-white text-fluid">{{ $monitor->last_checked_at?->diffForHumans() ?? 'Never' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Response Time Chart -->
                <div class="glass rounded-2xl overflow-hidden scroll-reveal card-hover-shine">
                    <div class="px-6 py-5 border-b border-white/5">
                        <h3 class="text-lg font-semibold text-white">Response Time (24h)</h3>
                    </div>
                    <div class="p-6">
                        @if($responseTimeData->isNotEmpty())
                            <div class="h-64" x-data="{
                                init() {
                                    const ctx = this.$refs.canvas.getContext('2d');
                                    new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: {{ Js::from($responseTimeData->pluck('time')) }},
                                            datasets: [{
                                                label: 'Response Time (ms)',
                                                data: {{ Js::from($responseTimeData->pluck('value')) }},
                                                borderColor: 'rgb(139, 92, 246)',
                                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                                fill: true,
                                                tension: 0.4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: { display: false }
                                            },
                                            scales: {
                                                x: {
                                                    grid: { color: 'rgba(255,255,255,0.05)' },
                                                    ticks: { color: 'rgba(255,255,255,0.5)' }
                                                },
                                                y: {
                                                    beginAtZero: true,
                                                    title: { display: true, text: 'ms', color: 'rgba(255,255,255,0.5)' },
                                                    grid: { color: 'rgba(255,255,255,0.05)' },
                                                    ticks: { color: 'rgba(255,255,255,0.5)' }
                                                }
                                            }
                                        }
                                    });
                                }
                            }">
                                <canvas x-ref="canvas"></canvas>
                            </div>
                        @else
                            <div class="h-64 flex items-center justify-center text-gray-500">
                                No data available yet
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Checks -->
                <div class="glass rounded-2xl overflow-hidden scroll-reveal card-hover-shine">
                    <div class="px-6 py-5 border-b border-white/5">
                        <h3 class="text-lg font-semibold text-white">Recent Checks</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Response Time</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Code</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Checked At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($recentChecks as $check)
                                    <tr class="hover:bg-white/[0.02] transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($check->status === 'up')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                    Success
                                                </span>
                                            @elseif($check->status === 'degraded')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                    Degraded
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                                    Failed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-cyan-400 font-mono">
                                            {{ $check->response_time }}ms
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                                            {{ $check->status_code ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $check->checked_at->format('M j, Y H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            No checks yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6 stagger-reveal">
                @if($monitor->type === 'ssl_expiry' && $monitor->latestCheck?->ssl_info)
                    @php $sslInfo = $monitor->latestCheck->ssl_info; @endphp
                    <!-- SSL Certificate Details -->
                    <div class="glass rounded-2xl overflow-hidden scroll-reveal-right">
                        <div class="px-6 py-5 border-b border-white/5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-white">SSL Certificate</h3>
                                @if($sslInfo['is_properly_configured'] ?? true)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Valid
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Issues
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="px-6 py-5 space-y-4">
                            @if(!empty($sslInfo['configuration_issues']))
                                <div class="rounded-xl bg-amber-500/10 border border-amber-500/20 p-4">
                                    <div class="flex gap-3">
                                        <svg class="h-5 w-5 text-amber-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-amber-400">Configuration Issues</h4>
                                            <ul class="mt-2 text-sm text-amber-300/80 list-disc pl-4 space-y-1">
                                                @foreach($sslInfo['configuration_issues'] as $issue)
                                                    <li>{{ $issue }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-400">Expires</dt>
                                <dd class="mt-1 flex items-center justify-between">
                                    <span class="text-sm text-white">{{ \Carbon\Carbon::parse($sslInfo['valid_to'])->format('M j, Y') }}</span>
                                    @php
                                        $daysRemaining = $sslInfo['days_remaining'] ?? 0;
                                        $badgeClass = match(true) {
                                            $daysRemaining <= 7 => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            $daysRemaining <= 30 => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                            default => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $badgeClass }}">
                                        {{ $daysRemaining }} days
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-400">Subject</dt>
                                <dd class="mt-1 text-sm text-white break-all">{{ $sslInfo['subject'] ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-400">Issued By</dt>
                                <dd class="mt-1 text-sm text-white">
                                    @if(is_array($sslInfo['issuer'] ?? null))
                                        {{ $sslInfo['issuer']['organization'] ?? $sslInfo['issuer']['common_name'] ?? '-' }}
                                    @else
                                        {{ $sslInfo['issuer'] ?? '-' }}
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Monitor Details -->
                <div class="glass rounded-2xl overflow-hidden scroll-reveal-right">
                    <div class="px-6 py-5 border-b border-white/5">
                        <h3 class="text-lg font-semibold text-white">Details</h3>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Type</dt>
                            <dd class="mt-1 text-sm text-white px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 inline-block">{{ strtoupper($monitor->type) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Target</dt>
                            <dd class="mt-1 text-sm text-cyan-400 break-all font-mono">{{ $monitor->target }}</dd>
                        </div>
                        @if($monitor->port)
                            <div>
                                <dt class="text-sm font-medium text-gray-400">Port</dt>
                                <dd class="mt-1 text-sm text-white font-mono">{{ $monitor->port }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Check Interval</dt>
                            <dd class="mt-1 text-sm text-white">
                                @if($monitor->check_interval < 60)
                                    {{ $monitor->check_interval }} seconds
                                @elseif($monitor->check_interval < 3600)
                                    {{ $monitor->check_interval / 60 }} minutes
                                @else
                                    {{ $monitor->check_interval / 3600 }} hours
                                @endif
                            </dd>
                        </div>
                        @if($monitor->project)
                            <div>
                                <dt class="text-sm font-medium text-gray-400">Project</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('projects.show', $monitor->project_id) }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors" wire:navigate>
                                        {{ $monitor->project->name }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Created</dt>
                            <dd class="mt-1 text-sm text-white">{{ $monitor->created_at->format('M j, Y') }}</dd>
                        </div>
                    </div>
                </div>

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
                    <ul class="divide-y divide-white/5">
                        @forelse($activeIncidents as $incident)
                            <li class="px-6 py-4 hover:bg-white/[0.02] transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="h-2 w-2 rounded-full bg-red-400 status-offline animate-pulse flex-shrink-0"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-white truncate">{{ $incident->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $incident->started_at->diffForHumans() }}</p>
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
                                <p class="text-emerald-400 font-medium">No active incidents</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
