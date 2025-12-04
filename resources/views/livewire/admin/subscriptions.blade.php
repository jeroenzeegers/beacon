<div>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-white">Subscription Management</h1>
    </x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass rounded-2xl p-6">
            <p class="text-sm text-gray-400">Total Subscriptions</p>
            <p class="text-3xl font-bold text-white mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-sm text-gray-400">Active</p>
            <p class="text-3xl font-bold text-emerald-400 mt-1">{{ number_format($stats['active']) }}</p>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-sm text-gray-400">Trialing</p>
            <p class="text-3xl font-bold text-amber-400 mt-1">{{ number_format($stats['trialing']) }}</p>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-sm text-gray-400">Canceled</p>
            <p class="text-3xl font-bold text-red-400 mt-1">{{ number_format($stats['canceled']) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass rounded-2xl p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 max-w-md">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search by team name..."
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>
            <div class="flex items-center space-x-2">
                <select
                    wire:model.live="filter"
                    class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
                    <option value="all">All Subscriptions</option>
                    <option value="active">Active</option>
                    <option value="trialing">Trialing</option>
                    <option value="past_due">Past Due</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="glass rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Team</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Current Period End</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($subscriptions as $subscription)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-white font-medium">{{ $subscription->team_name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-400">{{ $subscription->type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'active' => 'bg-emerald-500/20 text-emerald-400',
                                    'trialing' => 'bg-amber-500/20 text-amber-400',
                                    'past_due' => 'bg-orange-500/20 text-orange-400',
                                    'canceled' => 'bg-red-500/20 text-red-400',
                                    'incomplete' => 'bg-gray-500/20 text-gray-400',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$subscription->stripe_status] ?? 'bg-gray-500/20 text-gray-400' }}">
                                {{ ucfirst($subscription->stripe_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-400">{{ \Carbon\Carbon::parse($subscription->created_at)->format('M j, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($subscription->ends_at)
                                <span class="text-gray-400">{{ \Carbon\Carbon::parse($subscription->ends_at)->format('M j, Y') }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No subscriptions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $subscriptions->links() }}
    </div>
</div>
