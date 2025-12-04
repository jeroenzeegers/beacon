<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Team Members') }}
        </h2>
        @if($isAdmin && $canAddMember)
            <button wire:click="openInviteModal" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center px-4 py-2.5 rounded-xl font-semibold text-sm text-white">
                <span class="btn-magnetic-inner inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Member
                </span>
            </button>
        @endif
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

        <!-- Members List -->
        <div class="glass-liquid rounded-2xl overflow-hidden scroll-reveal" style="--delay: 0.1s">
            <ul role="list" class="divide-y divide-slate-700/50 stagger-list">
                @foreach($members as $index => $member)
                    <li class="px-4 py-4 sm:px-6 hover:bg-slate-800/30 transition-colors duration-300 stagger-item" style="--stagger: {{ $index }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0 gap-4">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-violet-500/30 to-indigo-500/30 flex items-center justify-center border border-violet-500/20">
                                        <span class="text-sm font-semibold text-violet-300">
                                            {{ strtoupper(substr($member->name, 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ $member->name }}</p>
                                    <p class="text-sm text-slate-400 truncate">{{ $member->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                @php
                                    $memberRole = $member->teams->where('id', $team->id)->first()?->pivot->role ?? 'member';
                                    $isMemberOwner = $team->isOwner($member);
                                @endphp

                                @if($isMemberOwner)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-violet-500/20 text-violet-300 border border-violet-500/30">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Owner
                                    </span>
                                @elseif($isAdmin && !$isMemberOwner)
                                    <select wire:change="updateRole({{ $member->id }}, $event.target.value)" class="input-liquid text-sm py-1.5 px-3 rounded-lg">
                                        @foreach($roles as $roleValue => $roleLabel)
                                            <option value="{{ $roleValue }}" {{ $memberRole === $roleValue ? 'selected' : '' }}>
                                                {{ $roleLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-500/20 text-slate-300 border border-slate-500/30">
                                        {{ ucfirst($memberRole) }}
                                    </span>
                                @endif

                                @if($isAdmin && !$isMemberOwner && $member->id !== auth()->id())
                                    <button wire:click="remove({{ $member->id }})" wire:confirm="Are you sure you want to remove this member from the team?" class="p-2 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all duration-300">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        @if(!$canAddMember && $isAdmin)
            <div class="glass-liquid rounded-xl p-4 border border-amber-500/30 scroll-reveal" style="--delay: 0.2s">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center">
                        <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-sm text-amber-300">
                        You've reached your team member limit.
                        <a href="{{ route('billing.plans') }}" class="font-semibold underline text-amber-200 hover:text-amber-100 transition-colors" wire:navigate>
                            Upgrade your plan
                        </a>
                        to add more members.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Invite Modal -->
    @if($showInviteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closeInviteModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom glass-liquid rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl shadow-violet-500/10 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-slate-700/50">
                    <form wire:submit="invite">
                        <div>
                            <div class="text-center sm:text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500/30 to-indigo-500/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-white" id="modal-title">
                                        Add Team Member
                                    </h3>
                                </div>
                                <p class="text-sm text-slate-400">
                                    Enter the email address of the user you want to add to your team.
                                </p>
                            </div>

                            <div class="mt-6 space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                                    <input wire:model="email" type="email" id="email" class="input-liquid w-full" placeholder="user@example.com">
                                    @error('email') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="role" class="block text-sm font-medium text-slate-300 mb-2">Role</label>
                                    <select wire:model="role" id="role" class="input-liquid w-full">
                                        @foreach($roles as $roleValue => $roleLabel)
                                            <option value="{{ $roleValue }}">{{ $roleLabel }}</option>
                                        @endforeach
                                    </select>
                                    @error('role') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button wire:click="closeInviteModal" type="button" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 border border-slate-600 text-sm font-medium rounded-xl text-slate-300 bg-slate-800/50 hover:bg-slate-700/50 hover:border-slate-500 transition-all duration-300 focus-ring">
                                Cancel
                            </button>
                            <button type="submit" class="btn-liquid btn-magnetic ripple-effect w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-xl text-white">
                                <span class="btn-magnetic-inner inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add Member
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
