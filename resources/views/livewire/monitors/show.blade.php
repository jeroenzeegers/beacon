<x-slot name="header">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="{{ route('monitors.index') }}" class="text-gray-400 hover:text-gray-500" wire:navigate>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $monitor->name }}
            </h2>
            @if($monitor->status === 'up')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Up
                </span>
            @elseif($monitor->status === 'down')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    Down
                </span>
            @elseif($monitor->status === 'degraded')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Degraded
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    Pending
                </span>
            @endif
        </div>
        <div class="flex items-center space-x-3">
            <button wire:click="toggleActive" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium {{ $monitor->is_active ? 'text-gray-700 bg-white hover:bg-gray-50' : 'text-green-700 bg-green-50 hover:bg-green-100' }}">
                {{ $monitor->is_active ? 'Pause' : 'Resume' }}
            </button>
            <a href="{{ route('monitors.edit', $monitor->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50" wire:navigate>
                Edit
            </a>
        </div>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-4 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Current Response</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $monitor->latestCheck?->response_time ?? '-' }}ms</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Avg Response (24h)</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ number_format($avgResponseTime) }}ms</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Uptime (30d)</dt>
                                <dd class="text-lg font-semibold text-green-600">{{ number_format($uptime, 2) }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Last Checked</dt>
                                <dd class="text-lg font-semibold text-gray-900">
                                    {{ $monitor->last_checked_at?->diffForHumans() ?? 'Never' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Response Time Chart -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Response Time (24h)</h3>
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
                                                borderColor: 'rgb(79, 70, 229)',
                                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
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
                                                y: {
                                                    beginAtZero: true,
                                                    title: { display: true, text: 'ms' }
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
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Checks</h3>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Response Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checked At</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentChecks as $check)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($check->status === 'up')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Success
                                                </span>
                                            @elseif($check->status === 'degraded')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Degraded
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Failed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $check->response_time }}ms
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $check->status_code ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $check->checked_at->format('M j, Y H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
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
            <div class="space-y-6">
                <!-- Monitor Details -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Details</h3>
                    </div>
                    <div class="px-4 py-5 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ strtoupper($monitor->type) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">{{ $monitor->target }}</dd>
                        </div>
                        @if($monitor->port)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Port</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $monitor->port }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Check Interval</dt>
                            <dd class="mt-1 text-sm text-gray-900">
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
                                <dt class="text-sm font-medium text-gray-500">Project</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ route('projects.show', $monitor->project_id) }}" class="text-indigo-600 hover:text-indigo-500" wire:navigate>
                                        {{ $monitor->project->name }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $monitor->created_at->format('M j, Y') }}</dd>
                        </div>
                    </div>
                </div>

                <!-- Active Incidents -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Active Incidents</h3>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse($activeIncidents as $incident)
                            <li class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    <span class="h-2 w-2 rounded-full bg-red-400 flex-shrink-0"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $incident->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $incident->started_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-4 py-8 text-center text-gray-500">
                                No active incidents
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
