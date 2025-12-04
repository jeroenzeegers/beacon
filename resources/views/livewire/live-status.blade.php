<div class="py-8" wire:poll.10s="loadData">
    <!-- Header with Overall Stats -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold">
                    <span class="text-gradient">Live Status</span>
                    <span class="text-white"> Overview</span>
                </h1>
                <p class="text-slate-400 mt-1">Real-time monitoring dashboard with live updates</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="glass rounded-full px-4 py-2 flex items-center gap-2 text-sm text-slate-300">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 status-online"></span>
                    </span>
                    Live updates active
                </span>
            </div>
        </div>

        <!-- Stats Cards - Bento Style -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Overall Uptime -->
            <div class="glass interactive-card rounded-2xl p-6 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Overall Uptime</p>
                        <p class="text-4xl font-bold text-white mt-2 tracking-tight">{{ $overallUptime }}%</p>
                        <p class="text-xs text-slate-500 mt-2">Last 30 days</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors duration-300">
                        <svg class="w-7 h-7 text-emerald-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-full transition-all duration-1000" style="width: {{ $overallUptime }}%"></div>
                </div>
            </div>

            <!-- Average Response Time -->
            <div class="glass interactive-card rounded-2xl p-6 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Avg Response Time</p>
                        <p class="text-4xl font-bold text-white mt-2 tracking-tight">{{ $averageResponseTime }}<span class="text-lg text-slate-400">ms</span></p>
                        <p class="text-xs text-slate-500 mt-2">Today</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-violet-500/10 flex items-center justify-center group-hover:bg-violet-500/20 transition-colors duration-300">
                        <svg class="w-7 h-7 text-violet-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Checks Today -->
            <div class="glass interactive-card rounded-2xl p-6 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Checks Today</p>
                        <p class="text-4xl font-bold text-white mt-2 tracking-tight">{{ number_format($totalChecksToday) }}</p>
                        <p class="text-xs text-slate-500 mt-2">Total requests</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 flex items-center justify-center group-hover:bg-cyan-500/20 transition-colors duration-300">
                        <svg class="w-7 h-7 text-cyan-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Monitors -->
            <div class="glass interactive-card rounded-2xl p-6 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Active Monitors</p>
                        <p class="text-4xl font-bold text-white mt-2 tracking-tight">{{ count($monitors) }}</p>
                        <p class="text-xs text-slate-500 mt-2">Being tracked</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 flex items-center justify-center group-hover:bg-amber-500/20 transition-colors duration-300">
                        <svg class="w-7 h-7 text-amber-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Response Time Chart -->
        <div class="lg:col-span-2 glass rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-white">Response Time Trend</h2>
                <span class="text-xs text-slate-500 bg-white/5 px-3 py-1 rounded-full">Last 24 hours</span>
            </div>
            <div class="h-72" wire:ignore>
                <canvas id="responseTimeChart"></canvas>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-white">Status Distribution</h2>
            </div>
            <div class="h-64 flex items-center justify-center" wire:ignore>
                <canvas id="statusChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                <div class="p-2 rounded-lg bg-emerald-500/10">
                    <p class="text-lg font-bold text-emerald-400">{{ $statusCounts['up'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400">Online</p>
                </div>
                <div class="p-2 rounded-lg bg-red-500/10">
                    <p class="text-lg font-bold text-red-400">{{ $statusCounts['down'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400">Offline</p>
                </div>
                <div class="p-2 rounded-lg bg-amber-500/10">
                    <p class="text-lg font-bold text-amber-400">{{ $statusCounts['degraded'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400">Degraded</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Uptime Chart & Monitor Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Uptime by Monitor -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-white">Uptime by Monitor</h2>
                <span class="text-sm text-slate-400">Last 30 days</span>
            </div>
            <div class="h-72" wire:ignore>
                <canvas id="uptimeChart"></canvas>
            </div>
        </div>

        <!-- Live Monitor Grid -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-white">Monitor Status Grid</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-72 overflow-y-auto custom-scrollbar">
                @forelse($monitors as $monitor)
                    <a href="{{ route('monitors.show', $monitor['id']) }}"
                       class="group p-3 rounded-lg border transition-all duration-200 hover:scale-105
                              @if($monitor['status'] === 'up')
                                  bg-emerald-500/10 border-emerald-500/30 hover:bg-emerald-500/20
                              @elseif($monitor['status'] === 'down')
                                  bg-red-500/10 border-red-500/30 hover:bg-red-500/20
                              @else
                                  bg-amber-500/10 border-amber-500/30 hover:bg-amber-500/20
                              @endif">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="relative flex h-2 w-2">
                                @if($monitor['status'] === 'up')
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                @elseif($monitor['status'] === 'down')
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                @else
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                @endif
                            </span>
                            <span class="text-sm font-medium text-white truncate">{{ $monitor['name'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">{{ $monitor['response_time'] ?? '--' }}ms</span>
                            <span class="@if($monitor['uptime_percentage'] >= 99) text-emerald-400 @elseif($monitor['uptime_percentage'] >= 95) text-amber-400 @else text-red-400 @endif">
                                {{ $monitor['uptime_percentage'] }}%
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-8 text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No monitors configured</p>
                        <a href="{{ route('monitors.create') }}" class="text-violet-400 hover:text-violet-300 mt-2 inline-block">Create your first monitor →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Checks Table -->
    <div class="glass rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-white">Recent Checks</h2>
            <span class="text-sm text-slate-400">Live feed</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-slate-400 border-b border-white/10">
                        <th class="pb-3 font-medium">Monitor</th>
                        <th class="pb-3 font-medium">Type</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium">Response Time</th>
                        <th class="pb-3 font-medium">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recentChecks as $check)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-3">
                                <span class="text-white font-medium">{{ $check['monitor_name'] }}</span>
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-slate-700/50 text-slate-300 uppercase">
                                    {{ $check['monitor_type'] }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($check['status'] === 'up')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-emerald-500/20 text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Online
                                    </span>
                                @elseif($check['status'] === 'down')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Offline
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-amber-500/20 text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        Degraded
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="text-slate-300">{{ $check['response_time'] ?? '--' }}ms</span>
                            </td>
                            <td class="py-3">
                                <span class="text-slate-400 text-sm">{{ $check['created_at'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                No checks recorded yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function() {
        // Chart instances
        let responseTimeChart = null;
        let statusChart = null;
        let uptimeChart = null;

        // Chart.js defaults for dark theme
        Chart.defaults.color = 'rgba(148, 163, 184, 1)';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;

        // Chart configurations
        const responseTimeConfig = {
            type: 'line',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 300 },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { position: 'top', align: 'end' },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: 'rgba(255, 255, 255, 1)',
                        bodyColor: 'rgba(148, 163, 184, 1)',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: (ctx) => ctx.dataset.label + ': ' + ctx.parsed.y + 'ms'
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, ticks: { callback: (v) => v + 'ms' } }
                }
            }
        };

        const statusConfig = {
            type: 'doughnut',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 300 },
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: 'rgba(255, 255, 255, 1)',
                        bodyColor: 'rgba(148, 163, 184, 1)',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                    }
                }
            }
        };

        const uptimeConfig = {
            type: 'bar',
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 300 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: 'rgba(255, 255, 255, 1)',
                        bodyColor: 'rgba(148, 163, 184, 1)',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: (ctx) => 'Uptime: ' + ctx.parsed.x + '%'
                        }
                    }
                },
                scales: {
                    x: { min: 0, max: 100, grid: { display: true }, ticks: { callback: (v) => v + '%' } },
                    y: { grid: { display: false } }
                }
            }
        };

        function getResponseTimeData(data) {
            return {
                labels: data.map(d => d.label),
                datasets: [{
                    label: 'Average',
                    data: data.map(d => d.avg),
                    borderColor: 'rgba(139, 92, 246, 1)',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(139, 92, 246, 1)',
                    pointBorderColor: 'rgba(139, 92, 246, 1)',
                    pointRadius: 3,
                    pointHoverRadius: 6,
                }, {
                    label: 'Max',
                    data: data.map(d => d.max),
                    borderColor: 'rgba(239, 68, 68, 0.5)',
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 0,
                }, {
                    label: 'Min',
                    data: data.map(d => d.min),
                    borderColor: 'rgba(34, 197, 94, 0.5)',
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 0,
                }]
            };
        }

        function getStatusData(counts) {
            return {
                labels: ['Online', 'Offline', 'Degraded'],
                datasets: [{
                    data: [counts.up || 0, counts.down || 0, counts.degraded || 0],
                    backgroundColor: ['rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(245, 158, 11, 0.8)'],
                    borderColor: ['rgba(34, 197, 94, 1)', 'rgba(239, 68, 68, 1)', 'rgba(245, 158, 11, 1)'],
                    borderWidth: 2,
                    hoverOffset: 10,
                }]
            };
        }

        function getUptimeData(data) {
            return {
                labels: data.map(d => d.name),
                datasets: [{
                    label: 'Uptime %',
                    data: data.map(d => d.uptime),
                    backgroundColor: data.map(d => d.uptime >= 99 ? 'rgba(34, 197, 94, 0.8)' : d.uptime >= 95 ? 'rgba(245, 158, 11, 0.8)' : 'rgba(239, 68, 68, 0.8)'),
                    borderColor: data.map(d => d.uptime >= 99 ? 'rgba(34, 197, 94, 1)' : d.uptime >= 95 ? 'rgba(245, 158, 11, 1)' : 'rgba(239, 68, 68, 1)'),
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            };
        }

        function initCharts() {
            const responseTimeCtx = document.getElementById('responseTimeChart');
            const statusCtx = document.getElementById('statusChart');
            const uptimeCtx = document.getElementById('uptimeChart');

            if (!responseTimeCtx || !statusCtx || !uptimeCtx) return;

            const initialResponseTimeData = @json($responseTimeData);
            const initialStatusCounts = @json($statusCounts);
            const initialUptimeData = @json($uptimeData);

            responseTimeChart = new Chart(responseTimeCtx.getContext('2d'), {
                ...responseTimeConfig,
                data: getResponseTimeData(initialResponseTimeData)
            });

            statusChart = new Chart(statusCtx.getContext('2d'), {
                ...statusConfig,
                data: getStatusData(initialStatusCounts)
            });

            uptimeChart = new Chart(uptimeCtx.getContext('2d'), {
                ...uptimeConfig,
                data: getUptimeData(initialUptimeData)
            });
        }

        function updateCharts(responseTimeData, statusCounts, uptimeData) {
            if (responseTimeChart) {
                responseTimeChart.data = getResponseTimeData(responseTimeData);
                responseTimeChart.update('none');
            }

            if (statusChart) {
                statusChart.data = getStatusData(statusCounts);
                statusChart.update('none');
            }

            if (uptimeChart) {
                uptimeChart.data = getUptimeData(uptimeData);
                uptimeChart.update('none');
            }
        }

        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCharts);
        } else {
            initCharts();
        }

        // Listen for Livewire chart data updates
        document.addEventListener('livewire:init', () => {
            Livewire.on('chartsDataUpdated', (eventData) => {
                const data = eventData[0] || eventData;
                if (data.responseTimeData && data.statusCounts && data.uptimeData) {
                    updateCharts(data.responseTimeData, data.statusCounts, data.uptimeData);
                }
            });
        });
    })();
</script>
@endpush
