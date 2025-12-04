<x-slot name="header">
    <h2 class="font-semibold text-xl text-white leading-tight">
        {{ __('Team Settings') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="glass-liquid rounded-xl p-4 border border-emerald-500/30 scroll-reveal" style="--delay: 0">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-emerald-300">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="glass-liquid rounded-xl p-4 border border-red-500/30 scroll-reveal" style="--delay: 0">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Team Name -->
        <div class="glass-liquid rounded-2xl overflow-hidden scroll-reveal" style="--delay: 0.1s">
            <form wire:submit="save">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500/30 to-indigo-500/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Team Name</h3>
                    </div>
                    <p class="text-sm text-slate-400 mb-6">
                        Update your team's display name.
                    </p>

                    <div class="max-w-xl">
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Name</label>
                        <input wire:model="name" type="text" id="name" class="input-liquid w-full" {{ !$isAdmin ? 'disabled' : '' }}>
                        @error('name') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if($isAdmin)
                    <div class="px-6 py-4 bg-slate-800/30 border-t border-slate-700/50 flex justify-end">
                        <button type="submit" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center px-5 py-2.5 text-sm font-semibold rounded-xl text-white">
                            <span class="btn-magnetic-inner inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Save
                            </span>
                        </button>
                    </div>
                @endif
            </form>
        </div>

        <!-- Team Information -->
        <div class="glass-liquid rounded-2xl overflow-hidden scroll-reveal" style="--delay: 0.2s">
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500/30 to-blue-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Team Information</h3>
                </div>
                <div class="glass-liquid rounded-xl overflow-hidden">
                    <dl class="divide-y divide-slate-700/50">
                        <div class="p-4 sm:grid sm:grid-cols-3 sm:gap-4 hover:bg-slate-800/30 transition-colors">
                            <dt class="text-sm font-medium text-slate-400 flex items-center gap-2">
                                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                                Team ID
                            </dt>
                            <dd class="mt-1 text-sm text-white sm:mt-0 sm:col-span-2 font-mono">{{ $team->id }}</dd>
                        </div>
                        <div class="p-4 sm:grid sm:grid-cols-3 sm:gap-4 hover:bg-slate-800/30 transition-colors">
                            <dt class="text-sm font-medium text-slate-400 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Created
                            </dt>
                            <dd class="mt-1 text-sm text-white sm:mt-0 sm:col-span-2">{{ $team->created_at->format('M j, Y') }}</dd>
                        </div>
                        <div class="p-4 sm:grid sm:grid-cols-3 sm:gap-4 hover:bg-slate-800/30 transition-colors">
                            <dt class="text-sm font-medium text-slate-400 flex items-center gap-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Owner
                            </dt>
                            <dd class="mt-1 text-sm text-white sm:mt-0 sm:col-span-2">{{ $team->owner->name }}</dd>
                        </div>
                        <div class="p-4 sm:grid sm:grid-cols-3 sm:gap-4 hover:bg-slate-800/30 transition-colors">
                            <dt class="text-sm font-medium text-slate-400 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Your Role
                            </dt>
                            <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                @if($isOwner)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-violet-500/20 text-violet-300 border border-violet-500/30">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Owner
                                    </span>
                                @elseif($isAdmin)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-500/20 text-slate-300 border border-slate-500/30">
                                        Member
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
