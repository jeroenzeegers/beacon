<div>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Users</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-xs text-emerald-400 mt-2">
                        +{{ $stats['users_today'] }} today
                    </p>
                </div>
                <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Teams -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Teams</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($stats['total_teams']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Monitors -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Active Monitors</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($stats['active_monitors']) }}</p>
                    <p class="text-xs text-gray-500 mt-2">
                        of {{ number_format($stats['total_monitors']) }} total
                    </p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Active Subscriptions</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($stats['subscriptions_active']) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- System Health -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">System Health</h2>
            <div class="space-y-4">
                @foreach($systemHealth as $service => $health)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @if($health['status'] === 'healthy')
                                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                            @elseif($health['status'] === 'warning')
                                <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                            @else
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            @endif
                            <span class="text-white capitalize">{{ $service }}</span>
                        </div>
                        <span class="text-sm text-gray-400">{{ $health['message'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Users -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Recent Users</h2>
                <a href="{{ route('admin.users') }}" class="text-sm text-indigo-400 hover:text-indigo-300">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($recentUsers as $user)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm text-white">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No users yet</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Recent Activity</h2>
                <a href="{{ route('admin.audit-logs') }}" class="text-sm text-indigo-400 hover:text-indigo-300">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($recentActivity as $log)
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm text-white">{{ $log->description }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $log->user?->name ?? 'System' }} &middot; {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No activity yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-lg font-semibold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.users') }}" class="glass rounded-xl p-4 hover:bg-white/5 transition-colors text-center">
                <svg class="w-8 h-8 text-indigo-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span class="text-sm text-gray-300">Manage Users</span>
            </a>
            <a href="{{ route('admin.teams') }}" class="glass rounded-xl p-4 hover:bg-white/5 transition-colors text-center">
                <svg class="w-8 h-8 text-purple-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-sm text-gray-300">Manage Teams</span>
            </a>
            <a href="{{ route('admin.subscriptions') }}" class="glass rounded-xl p-4 hover:bg-white/5 transition-colors text-center">
                <svg class="w-8 h-8 text-emerald-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span class="text-sm text-gray-300">Subscriptions</span>
            </a>
            <a href="{{ route('admin.audit-logs') }}" class="glass rounded-xl p-4 hover:bg-white/5 transition-colors text-center">
                <svg class="w-8 h-8 text-amber-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm text-gray-300">Audit Logs</span>
            </a>
        </div>
    </div>
</div>
