<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $statusPage->name }} - Status</title>
    <meta name="description" content="Real-time status for {{ $statusPage->name }}">

    @if($statusPage->favicon_url)
        <link rel="icon" href="{{ $statusPage->favicon_url }}">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $statusPage->primary_color ?? "#4F46E5" }}',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($statusPage->logo_url)
                        <img src="{{ $statusPage->logo_url }}" alt="{{ $statusPage->name }}" class="h-10 w-auto">
                    @endif
                    <h1 class="text-2xl font-bold text-gray-900">{{ $statusPage->name }}</h1>
                </div>
                <div class="text-sm text-gray-500">
                    Last updated: {{ now()->format('M j, Y g:i A T') }}
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Overall Status Banner -->
        <div class="rounded-lg p-6 mb-8 {{ $overallStatus === 'up' ? 'bg-green-50 border border-green-200' : ($overallStatus === 'degraded' ? 'bg-yellow-50 border border-yellow-200' : 'bg-red-50 border border-red-200') }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if($overallStatus === 'up')
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-green-800">All Systems Operational</h2>
                            <p class="text-sm text-green-600">Everything is working normally</p>
                        </div>
                    @elseif($overallStatus === 'degraded')
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-yellow-800">Partial System Outage</h2>
                            <p class="text-sm text-yellow-600">Some systems are experiencing issues</p>
                        </div>
                    @else
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-red-800">Major System Outage</h2>
                            <p class="text-sm text-red-600">We are experiencing a service disruption</p>
                        </div>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold {{ $overallStatus === 'up' ? 'text-green-700' : ($overallStatus === 'degraded' ? 'text-yellow-700' : 'text-red-700') }}">
                        {{ number_format($uptimePercentage, 2) }}%
                    </div>
                    <div class="text-sm {{ $overallStatus === 'up' ? 'text-green-600' : ($overallStatus === 'degraded' ? 'text-yellow-600' : 'text-red-600') }}">
                        Uptime ({{ $statusPage->uptime_days_shown ?? 90 }} days)
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Incidents -->
        @if($activeIncidents->isNotEmpty())
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Active Incidents</h3>
                <div class="space-y-4">
                    @foreach($activeIncidents as $incident)
                        <div class="bg-white rounded-lg shadow border-l-4 {{ $incident->severity === 'critical' ? 'border-red-500' : ($incident->severity === 'major' ? 'border-orange-500' : 'border-yellow-500') }} p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $incident->title }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $incident->description }}</p>
                                    <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                        <span>Started: {{ $incident->started_at->diffForHumans() }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $incident->severity === 'critical' ? 'bg-red-100 text-red-800' : ($incident->severity === 'major' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($incident->severity) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($incident->updates && $incident->updates->isNotEmpty())
                                <div class="mt-4 border-t border-gray-100 pt-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Updates</h5>
                                    <div class="space-y-2">
                                        @foreach($incident->updates->take(3) as $update)
                                            <div class="text-sm">
                                                <span class="text-gray-500">{{ $update->created_at->format('M j, g:i A') }}</span>
                                                <p class="text-gray-700">{{ $update->message }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Services Status -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Services</h3>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($monitors as $monitor)
                        @php
                            $data = $uptimeData[$monitor->id] ?? ['uptime_percentage' => 0, 'average_response_time' => 0];
                            $displayName = $monitor->pivot->display_name ?? $monitor->name;
                        @endphp
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <!-- Status Indicator -->
                                    <div class="flex-shrink-0">
                                        @if($monitor->status === 'up')
                                            <span class="inline-block h-3 w-3 rounded-full bg-green-500"></span>
                                        @elseif($monitor->status === 'degraded')
                                            <span class="inline-block h-3 w-3 rounded-full bg-yellow-500"></span>
                                        @elseif($monitor->status === 'down')
                                            <span class="inline-block h-3 w-3 rounded-full bg-red-500"></span>
                                        @else
                                            <span class="inline-block h-3 w-3 rounded-full bg-gray-400"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $displayName }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($monitor->status === 'up')
                                                Operational
                                            @elseif($monitor->status === 'degraded')
                                                Degraded Performance
                                            @elseif($monitor->status === 'down')
                                                Service Disruption
                                            @else
                                                Unknown
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($data['uptime_percentage'], 2) }}% uptime
                                    </div>
                                    @if($data['average_response_time'] > 0)
                                        <div class="text-xs text-gray-500">
                                            Avg response: {{ number_format($data['average_response_time'], 0) }}ms
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Uptime Bar (last 90 days) -->
                            @if($statusPage->show_uptime_chart)
                                <div class="mt-3">
                                    <div class="flex space-px h-8">
                                        @for($i = 0; $i < 90; $i++)
                                            @php
                                                // Simplified: show as green for demo, in production would pull actual day data
                                                $dayStatus = 'up';
                                            @endphp
                                            <div class="flex-1 rounded-sm {{ $dayStatus === 'up' ? 'bg-green-400' : ($dayStatus === 'degraded' ? 'bg-yellow-400' : 'bg-red-400') }}" title="Day {{ 90 - $i }}"></div>
                                        @endfor
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-400 mt-1">
                                        <span>90 days ago</span>
                                        <span>Today</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            No services configured for this status page.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Incidents -->
        @if($recentIncidents->isNotEmpty())
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Past Incidents</h3>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        @foreach($recentIncidents as $incident)
                            <div class="p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $incident->title }}</h4>
                                        <div class="mt-1 text-sm text-gray-500">
                                            {{ $incident->started_at->format('M j, Y') }}
                                            @if($incident->resolved_at)
                                                - Resolved after {{ $incident->started_at->diffForHumans($incident->resolved_at, true) }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $incident->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($incident->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white mt-12">
        <div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <div>
                    @if($statusPage->support_url)
                        <a href="{{ $statusPage->support_url }}" class="text-indigo-600 hover:text-indigo-500">Contact Support</a>
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    @if($statusPage->show_subscribe_button)
                        <button onclick="alert('Subscribe functionality coming soon!')" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Subscribe to Updates
                        </button>
                    @endif
                    <span>Powered by <a href="https://beacon.app" class="text-indigo-600 hover:text-indigo-500">Beacon</a></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Auto-refresh every 60 seconds -->
    <script>
        setTimeout(function() {
            window.location.reload();
        }, 60000);
    </script>
</body>
</html>
