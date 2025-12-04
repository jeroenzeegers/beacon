<div>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-white">Audit Logs</h1>
    </x-slot>

    <!-- Filters -->
    <div class="glass rounded-2xl p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-400 mb-1">Search</label>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search by description or user..."
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>
            <div class="w-full lg:w-48">
                <label class="block text-sm font-medium text-gray-400 mb-1">Action</label>
                <select
                    wire:model.live="action"
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}">{{ ucfirst(str_replace('_', ' ', $act)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full lg:w-40">
                <label class="block text-sm font-medium text-gray-400 mb-1">From</label>
                <input
                    wire:model.live="dateFrom"
                    type="date"
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>
            <div class="w-full lg:w-40">
                <label class="block text-sm font-medium text-gray-400 mb-1">To</label>
                <input
                    wire:model.live="dateTo"
                    type="date"
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>
            <button
                wire:click="clearFilters"
                class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-colors"
            >
                Clear
            </button>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="glass rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($logs as $log)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-white text-sm">{{ $log->created_at->format('M j, Y') }}</p>
                                <p class="text-gray-500 text-xs">{{ $log->created_at->format('H:i:s') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->user)
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ substr($log->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-white text-sm">{{ $log->user->name }}</p>
                                        <p class="text-gray-500 text-xs">{{ $log->user->email }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-500">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $actionColors = [
                                    'login' => 'bg-emerald-500/20 text-emerald-400',
                                    'logout' => 'bg-gray-500/20 text-gray-400',
                                    'create' => 'bg-indigo-500/20 text-indigo-400',
                                    'update' => 'bg-amber-500/20 text-amber-400',
                                    'delete' => 'bg-red-500/20 text-red-400',
                                    'impersonate' => 'bg-purple-500/20 text-purple-400',
                                    'admin_toggle' => 'bg-pink-500/20 text-pink-400',
                                    'verify_email' => 'bg-cyan-500/20 text-cyan-400',
                                    'maintenance' => 'bg-orange-500/20 text-orange-400',
                                    'cache' => 'bg-blue-500/20 text-blue-400',
                                    'settings' => 'bg-violet-500/20 text-violet-400',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $actionColors[$log->action] ?? 'bg-gray-500/20 text-gray-400' }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-white text-sm">{{ Str::limit($log->description, 50) }}</p>
                            @if($log->auditable_type)
                                <p class="text-gray-500 text-xs">
                                    {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                </p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-400 text-sm font-mono">{{ $log->ip_address ?? '-' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No audit logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
