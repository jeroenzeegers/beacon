<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Analytics</h1>
            <select
                wire:model.live="period"
                class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
            >
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last year</option>
            </select>
        </div>
    </x-slot>

    <!-- User Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">New Users</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($userStats['new_users']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Last {{ $period }} days</p>
                </div>
                <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Active Users</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($userStats['active_users']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Last {{ $period }} days</p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Users</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($userStats['total_users']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Verified Users</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($userStats['verified_users']) }}</p>
                    @if($userStats['total_users'] > 0)
                        <p class="text-xs text-emerald-400 mt-1">{{ round(($userStats['verified_users'] / $userStats['total_users']) * 100) }}% of total</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass rounded-2xl p-6">
            <p class="text-sm text-gray-400">Total Monitors</p>
            <p class="text-3xl font-bold text-white mt-1">{{ number_format($usageStats['total_monitors']) }}</p>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-sm text-gray-400">Active Monitors</p>
            <p class="text-3xl font-bold text-emerald-400 mt-1">{{ number_format($usageStats['active_monitors']) }}</p>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-sm text-gray-400">Total Teams</p>
            <p class="text-3xl font-bold text-white mt-1">{{ number_format($usageStats['total_teams']) }}</p>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-sm text-gray-400">Checks Today</p>
            <p class="text-3xl font-bold text-white mt-1">{{ number_format($usageStats['checks_today']) }}</p>
        </div>
    </div>

    <!-- Top Teams -->
    <div class="glass rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Top Teams by Monitors</h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Team</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Monitors</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($topTeams as $index => $team)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-gray-400">#{{ $index + 1 }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white text-sm font-medium">
                                        {{ substr($team->name, 0, 1) }}
                                    </div>
                                    <span class="text-white font-medium">{{ $team->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-white">{{ number_format($team->monitors_count) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-400">{{ $team->created_at->format('M j, Y') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                No teams found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
