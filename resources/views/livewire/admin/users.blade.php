<div>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-white">User Management</h1>
    </x-slot>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="glass rounded-2xl p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 max-w-md">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search users..."
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>
            <div class="flex items-center space-x-2">
                <select
                    wire:model.live="filter"
                    class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
                    <option value="all">All Users</option>
                    <option value="admin">Admins Only</option>
                    <option value="verified">Verified</option>
                    <option value="unverified">Unverified</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Teams</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Last Login</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($users as $user)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-medium">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                @if($user->is_admin)
                                    <span class="px-2 py-1 text-xs font-medium bg-red-500/20 text-red-400 rounded-full">Admin</span>
                                @endif
                                @if($user->email_verified_at)
                                    <span class="px-2 py-1 text-xs font-medium bg-emerald-500/20 text-emerald-400 rounded-full">Verified</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-amber-500/20 text-amber-400 rounded-full">Unverified</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white">{{ $user->teams->count() }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-400">{{ $user->created_at->format('M j, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-400">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end space-x-2">
                                <button
                                    wire:click="viewUser({{ $user->id }})"
                                    class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                                    title="View Details"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button
                                    wire:click="impersonate({{ $user->id }})"
                                    wire:confirm="Are you sure you want to impersonate this user?"
                                    class="p-2 text-gray-400 hover:text-amber-400 hover:bg-white/10 rounded-lg transition-colors"
                                    title="Impersonate"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                <button
                                    wire:click="toggleAdmin({{ $user->id }})"
                                    wire:confirm="Are you sure you want to toggle admin status for this user?"
                                    class="p-2 text-gray-400 hover:text-indigo-400 hover:bg-white/10 rounded-lg transition-colors"
                                    title="{{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>

    <!-- User Detail Modal -->
    @if($showUserModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:click.self="$wire.closeModal()">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                <div class="relative glass-darker rounded-2xl w-full max-w-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-white">User Details</h2>
                        <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- User Info -->
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                {{ substr($selectedUser->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white">{{ $selectedUser->name }}</h3>
                                <p class="text-gray-400">{{ $selectedUser->email }}</p>
                            </div>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="glass rounded-xl p-4">
                                <p class="text-sm text-gray-400">Joined</p>
                                <p class="text-white font-medium">{{ $selectedUser->created_at->format('M j, Y') }}</p>
                            </div>
                            <div class="glass rounded-xl p-4">
                                <p class="text-sm text-gray-400">Last Login</p>
                                <p class="text-white font-medium">{{ $selectedUser->last_login_at?->format('M j, Y H:i') ?? 'Never' }}</p>
                            </div>
                            <div class="glass rounded-xl p-4">
                                <p class="text-sm text-gray-400">Email Status</p>
                                <p class="text-white font-medium">{{ $selectedUser->email_verified_at ? 'Verified' : 'Unverified' }}</p>
                            </div>
                            <div class="glass rounded-xl p-4">
                                <p class="text-sm text-gray-400">Teams</p>
                                <p class="text-white font-medium">{{ $selectedUser->teams->count() }}</p>
                            </div>
                        </div>

                        <!-- Teams List -->
                        @if($selectedUser->teams->count() > 0)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Teams</h4>
                                <div class="space-y-2">
                                    @foreach($selectedUser->teams as $team)
                                        <div class="glass rounded-xl p-3 flex items-center justify-between">
                                            <span class="text-white">{{ $team->name }}</span>
                                            <span class="px-2 py-1 text-xs font-medium bg-indigo-500/20 text-indigo-400 rounded-full">
                                                {{ $team->pivot->role }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-white/5">
                            @if(!$selectedUser->email_verified_at)
                                <button
                                    wire:click="verifyEmail({{ $selectedUser->id }})"
                                    class="px-4 py-2 text-sm font-medium text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 rounded-lg transition-colors"
                                >
                                    Verify Email
                                </button>
                            @endif
                            <button
                                wire:click="impersonate({{ $selectedUser->id }})"
                                wire:confirm="Are you sure you want to impersonate this user?"
                                class="px-4 py-2 text-sm font-medium text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 rounded-lg transition-colors"
                            >
                                Impersonate
                            </button>
                            <button
                                wire:click="deleteUser({{ $selectedUser->id }})"
                                wire:confirm="Are you sure you want to delete this user? This action cannot be undone."
                                class="px-4 py-2 text-sm font-medium text-red-400 bg-red-500/10 hover:bg-red-500/20 rounded-lg transition-colors"
                            >
                                Delete User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
