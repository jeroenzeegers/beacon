<x-slot name="header">
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('incidents.index') }}" class="text-sm text-gray-400 hover:text-white flex items-center gap-1 mb-2" wire:navigate>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Incidents
            </a>
            <h2 class="text-2xl font-bold text-white">
                {{ $incident->title }}
            </h2>
        </div>
        <div class="flex items-center gap-2">
            @if($incident->isActive())
                <button wire:click="resolve" wire:confirm="Mark this incident as resolved?" class="px-4 py-2 text-sm font-medium text-emerald-400 hover:text-emerald-300 border border-emerald-500/30 hover:border-emerald-500/50 rounded-lg transition-colors">
                    Mark Resolved
                </button>
            @endif
            <a href="{{ route('incidents.edit', $incident->id) }}" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-4 py-2 text-sm font-medium" wire:navigate>
                <span class="btn-magnetic-inner flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </span>
            </a>
        </div>
    </div>
</x-slot>

<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-6 glass rounded-xl p-4 border-l-4 border-emerald-500">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Incident Details -->
                <div class="glass rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <!-- Severity Badge -->
                        @if($incident->severity === 'critical')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                Critical
                            </span>
                        @elseif($incident->severity === 'major')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                Major
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                Minor
                            </span>
                        @endif

                        <!-- Status Badge -->
                        @if($incident->status === 'resolved')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Resolved
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-violet-500/10 text-violet-400 border border-violet-500/20">
                                <span class="h-2 w-2 rounded-full bg-violet-400 animate-pulse"></span>
                                {{ $statuses[$incident->status] ?? $incident->status }}
                            </span>
                        @endif
                    </div>

                    @if($incident->description)
                        <p class="text-gray-300 whitespace-pre-wrap">{{ $incident->description }}</p>
                    @else
                        <p class="text-gray-500 italic">No description provided.</p>
                    @endif
                </div>

                <!-- Add Update Form -->
                @if($incident->isActive())
                    <div class="glass rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Post Update</h3>

                        <form wire:submit="addUpdate" class="space-y-4">
                            <div>
                                <label for="updateStatus" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                                <select id="updateStatus" wire:model="updateStatus" class="input-liquid w-full">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="updateMessage" class="block text-sm font-medium text-gray-300 mb-2">Message</label>
                                <textarea id="updateMessage" wire:model="updateMessage" rows="3" class="input-liquid w-full" placeholder="Describe the current status or progress..."></textarea>
                                @error('updateMessage') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-4 py-2 text-sm font-medium">
                                <span class="btn-magnetic-inner flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Post Update
                                </span>
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Timeline -->
                <div class="glass rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Timeline</h3>

                    <div class="relative">
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-white/10"></div>

                        <div class="space-y-6">
                            @forelse($incident->updates as $update)
                                <div class="relative pl-10">
                                    <div class="absolute left-2.5 w-3 h-3 rounded-full bg-indigo-500 border-2 border-gray-900"></div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-medium text-white">{{ $statuses[$update->status] ?? $update->status }}</span>
                                            <span class="text-xs text-gray-500">{{ $update->created_at->diffForHumans() }}</span>
                                            @if($update->user)
                                                <span class="text-xs text-gray-500">by {{ $update->user->name }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-400">{{ $update->message }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="relative pl-10">
                                    <div class="absolute left-2.5 w-3 h-3 rounded-full bg-gray-600 border-2 border-gray-900"></div>
                                    <p class="text-sm text-gray-500">Incident created {{ $incident->started_at?->diffForHumans() ?? 'N/A' }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Info Card -->
                <div class="glass rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Details</h3>

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Started</dt>
                            <dd class="text-sm text-white">{{ $incident->started_at?->format('M j, Y g:i A') ?? 'N/A' }}</dd>
                        </div>

                        @if($incident->resolved_at)
                            <div>
                                <dt class="text-sm text-gray-500">Resolved</dt>
                                <dd class="text-sm text-white">{{ $incident->resolved_at->format('M j, Y g:i A') }}</dd>
                            </div>
                        @endif

                        @if($incident->duration)
                            <div>
                                <dt class="text-sm text-gray-500">Duration</dt>
                                <dd class="text-sm text-white">{{ $incident->duration }} minutes</dd>
                            </div>
                        @endif

                        @if($incident->monitor)
                            <div>
                                <dt class="text-sm text-gray-500">Affected Monitor</dt>
                                <dd>
                                    <a href="{{ route('monitors.show', $incident->monitor->id) }}" class="text-sm text-indigo-400 hover:text-indigo-300" wire:navigate>
                                        {{ $incident->monitor->name }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm text-gray-500">Visibility</dt>
                            <dd class="text-sm text-white">{{ $incident->is_public ? 'Public' : 'Private' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
