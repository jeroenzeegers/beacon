<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gradient">
            {{ __('Alert Rules') }}
        </h2>
        <a href="{{ route('alerts.rules.create') }}" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium" wire:navigate>
            <span class="btn-magnetic-inner flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Rule
            </span>
        </a>
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

        <!-- Filters -->
        <div class="glass rounded-2xl mb-6 scroll-reveal">
            <div class="p-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="search" id="search" class="input-liquid block w-full pl-11 pr-4" placeholder="Search rules...">
                        </div>
                    </div>
                    <div>
                        <label for="trigger" class="sr-only">Trigger</label>
                        <select wire:model.live="trigger" id="trigger" class="input-liquid block w-full">
                            <option value="">All Triggers</option>
                            @foreach($triggers as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rules Table -->
        <div class="glass rounded-2xl overflow-hidden scroll-reveal card-hover-shine">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Trigger
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Scope
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Channels
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Cooldown
                            </th>
                            <th scope="col" class="relative px-6 py-4">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-white/[0.02] transition-colors group" wire:key="rule-{{ $rule->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($rule->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-500/10 text-gray-400 border border-gray-500/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-white">{{ $rule->name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-400 px-2 py-0.5 rounded bg-white/5 border border-white/10">
                                        {{ $triggers[$rule->trigger] ?? $rule->trigger }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    @if($rule->monitor)
                                        <span class="text-cyan-400">{{ $rule->monitor->name }}</span>
                                    @elseif($rule->project)
                                        <span class="text-violet-400">{{ $rule->project->name }}</span>
                                    @else
                                        <span class="text-amber-400">Global</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        @foreach($rule->channels->take(3) as $channel)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                                {{ $channel->name }}
                                            </span>
                                        @endforeach
                                        @if($rule->channels->count() > 3)
                                            <span class="text-xs text-gray-500">+{{ $rule->channels->count() - 3 }}</span>
                                        @endif
                                        @if($rule->channels->isEmpty())
                                            <span class="text-xs text-gray-500">No channels</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $rule->cooldown_minutes }} min
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="toggleActive({{ $rule->id }})" class="text-gray-400 hover:text-white transition-colors text-xs">
                                            {{ $rule->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                        <a href="{{ route('alerts.rules.edit', $rule->id) }}" class="text-indigo-400 hover:text-indigo-300 transition-colors" wire:navigate>
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button wire:click="delete({{ $rule->id }})" wire:confirm="Are you sure you want to delete this rule?" class="text-red-400 hover:text-red-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-violet-500/10 to-indigo-500/10 flex items-center justify-center mb-4 float-depth-1">
                                        <svg class="w-10 h-10 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-white mb-1">No alert rules</h3>
                                    <p class="text-gray-500 mb-6">Create rules to get notified when monitors change status.</p>
                                    <a href="{{ route('alerts.rules.create') }}" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium" wire:navigate>
                                        <span class="btn-magnetic-inner flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Create Rule
                                        </span>
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rules->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $rules->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
