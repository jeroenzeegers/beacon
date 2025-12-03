<div>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-white">Team Management</h1>
    </x-slot>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="glass rounded-2xl p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 max-w-md">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search teams..."
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>
            <div class="flex items-center space-x-2">
                <select
                    wire:model.live="filter"
                    class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
                    <option value="all">All Teams</option>
                    <option value="subscribed">Subscribed</option>
                    <option value="free">Free</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Teams Table -->
    <div class="glass rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Team</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Owner</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Members</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Monitors</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($teams as $team)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white font-medium">
                                    {{ substr($team->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $team->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-400">{{ $team->owner?->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white">{{ $team->users->count() }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white">{{ $team->monitors_count ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-400">{{ $team->created_at->format('M j, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end space-x-2">
                                <button
                                    wire:click="viewTeam({{ $team->id }})"
                                    class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                                    title="View Details"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No teams found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $teams->links() }}
    </div>

    <!-- Team Detail Modal -->
    @if($showTeamModal && $selectedTeam)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-on:click.self="$wire.closeModal()">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                <div class="relative glass-darker rounded-2xl w-full max-w-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-white">Team Details</h2>
                        <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Team Info -->
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center text-white text-2xl font-bold">
                                {{ substr($selectedTeam->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white">{{ $selectedTeam->name }}</h3>
                                <p class="text-gray-400">Owner: {{ $selectedTeam->owner?->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="glass rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-white">{{ $selectedTeam->users->count() }}</p>
                                <p class="text-sm text-gray-400">Members</p>
                            </div>
                            <div class="glass rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-white">{{ $selectedTeam->monitors->count() }}</p>
                                <p class="text-sm text-gray-400">Monitors</p>
                            </div>
                            <div class="glass rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-white">{{ $selectedTeam->projects->count() }}</p>
                                <p class="text-sm text-gray-400">Projects</p>
                            </div>
                        </div>

                        <!-- Members List -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Team Members</h4>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($selectedTeam->users as $user)
                                    <div class="glass rounded-xl p-3 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm text-white">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium bg-indigo-500/20 text-indigo-400 rounded-full">
                                            {{ $user->pivot->role }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-white/5">
                            <button
                                wire:click="deleteTeam({{ $selectedTeam->id }})"
                                wire:confirm="Are you sure you want to delete this team? This will delete all associated monitors, projects, and data. This action cannot be undone."
                                class="px-4 py-2 text-sm font-medium text-red-400 bg-red-500/10 hover:bg-red-500/20 rounded-lg transition-colors"
                            >
                                Delete Team
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
