<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gradient">
            {{ __('Status Pages') }}
        </h2>
        <a href="{{ route('status-pages.create') }}" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium" wire:navigate>
            <span class="btn-magnetic-inner flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Status Page
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

        <!-- Search -->
        <div class="glass rounded-2xl mb-6 scroll-reveal">
            <div class="p-5">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="search" class="input-liquid block w-full pl-11 pr-4" placeholder="Search status pages...">
                </div>
            </div>
        </div>

        <!-- Status Pages Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($statusPages as $statusPage)
                <div class="glass rounded-2xl p-6 scroll-reveal card-hover-shine relative group" wire:key="status-page-{{ $statusPage->id }}">
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        @if($statusPage->is_public)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                Public
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                                Private
                            </span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-white">{{ $statusPage->name }}</h3>
                        @if($statusPage->description)
                            <p class="text-sm text-gray-400 mt-1 line-clamp-2">{{ $statusPage->description }}</p>
                        @endif
                    </div>

                    <!-- Overall Status -->
                    <div class="mb-4">
                        @php
                            $overallStatus = $statusPage->overall_status;
                        @endphp
                        @if($overallStatus === 'up')
                            <div class="flex items-center gap-2 text-emerald-400">
                                <span class="h-2 w-2 rounded-full bg-emerald-400 status-online"></span>
                                <span class="text-sm font-medium">All Systems Operational</span>
                            </div>
                        @elseif($overallStatus === 'degraded')
                            <div class="flex items-center gap-2 text-amber-400">
                                <span class="h-2 w-2 rounded-full bg-amber-400 status-degraded"></span>
                                <span class="text-sm font-medium">Partial Outage</span>
                            </div>
                        @elseif($overallStatus === 'down')
                            <div class="flex items-center gap-2 text-red-400">
                                <span class="h-2 w-2 rounded-full bg-red-400 status-offline"></span>
                                <span class="text-sm font-medium">Major Outage</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-gray-400">
                                <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                <span class="text-sm font-medium">Unknown</span>
                            </div>
                        @endif
                    </div>

                    <!-- Monitors Count -->
                    <div class="flex items-center gap-4 text-sm text-gray-400 mb-4">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>{{ $statusPage->monitors->count() }} monitors</span>
                        </div>
                    </div>

                    <!-- URL -->
                    @if($statusPage->is_public)
                        <div class="mb-4">
                            <a href="{{ route('status-page.show', $statusPage->slug) }}" target="_blank" class="text-sm text-indigo-400 hover:text-indigo-300 flex items-center gap-1 truncate">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <span class="truncate">{{ route('status-page.show', $statusPage->slug) }}</span>
                            </a>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="pt-4 border-t border-white/5 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="flex items-center gap-2">
                            <button wire:click="togglePublic({{ $statusPage->id }})" class="text-sm text-gray-400 hover:text-white transition-colors">
                                {{ $statusPage->is_public ? 'Make Private' : 'Make Public' }}
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('status-pages.edit', $statusPage->id) }}" class="text-indigo-400 hover:text-indigo-300 transition-colors" wire:navigate>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <button wire:click="delete({{ $statusPage->id }})" wire:confirm="Are you sure you want to delete this status page?" class="text-red-400 hover:text-red-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="glass rounded-2xl p-12 text-center">
                        <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-violet-500/10 to-indigo-500/10 flex items-center justify-center mb-4 float-depth-1">
                            <svg class="w-10 h-10 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-white mb-1">No status pages</h3>
                        <p class="text-gray-500 mb-6">Create a public status page to keep your users informed.</p>
                        <a href="{{ route('status-pages.create') }}" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium" wire:navigate>
                            <span class="btn-magnetic-inner flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Status Page
                            </span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        @if($statusPages->hasPages())
            <div class="mt-6">
                {{ $statusPages->links() }}
            </div>
        @endif
    </div>
</div>
