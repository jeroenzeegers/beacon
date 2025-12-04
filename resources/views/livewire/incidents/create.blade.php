<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gradient">
            {{ $isEditing ? __('Edit Incident') : __('Report Incident') }}
        </h2>
    </div>
</x-slot>

<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <form wire:submit="save" class="space-y-6">
            <!-- Basic Information -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Incident Details</h3>

                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Title</label>
                        <input type="text" id="title" wire:model="title" class="input-liquid w-full" placeholder="Brief description of the incident">
                        @error('title') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea id="description" wire:model="description" rows="4" class="input-liquid w-full" placeholder="Provide more details about the incident..."></textarea>
                        @error('description') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="monitor_id" class="block text-sm font-medium text-gray-300 mb-2">Affected Monitor (optional)</label>
                        <select id="monitor_id" wire:model="monitor_id" class="input-liquid w-full">
                            <option value="">No specific monitor</option>
                            @foreach($monitors as $monitor)
                                <option value="{{ $monitor->id }}">{{ $monitor->name }}</option>
                            @endforeach
                        </select>
                        @error('monitor_id') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Status & Severity -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Status & Severity</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <div class="space-y-2">
                            @foreach($statuses as $value => $label)
                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors {{ $status === $value ? 'border-indigo-500/50 bg-indigo-500/10' : 'border-white/10 hover:border-white/20' }}">
                                    <input type="radio" wire:model="status" value="{{ $value }}" class="w-4 h-4 text-indigo-500 bg-gray-700 border-gray-600 focus:ring-indigo-500 focus:ring-offset-0">
                                    <span class="text-sm font-medium {{ $status === $value ? 'text-white' : 'text-gray-400' }}">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('status') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Severity</label>
                        <div class="space-y-2">
                            @foreach($severities as $value => $label)
                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors {{ $severity === $value ? 'border-indigo-500/50 bg-indigo-500/10' : 'border-white/10 hover:border-white/20' }}">
                                    <input type="radio" wire:model="severity" value="{{ $value }}" class="w-4 h-4 text-indigo-500 bg-gray-700 border-gray-600 focus:ring-indigo-500 focus:ring-offset-0">
                                    <div class="flex items-center gap-2">
                                        @if($value === 'critical')
                                            <span class="h-2 w-2 rounded-full bg-red-400"></span>
                                        @elseif($value === 'major')
                                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                        @else
                                            <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                                        @endif
                                        <span class="text-sm font-medium {{ $severity === $value ? 'text-white' : 'text-gray-400' }}">{{ $label }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('severity') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Visibility -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Visibility</h3>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="is_public" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                    <div>
                        <span class="text-sm font-medium text-white">Public Incident</span>
                        <p class="text-xs text-gray-500">Show this incident on public status pages.</p>
                    </div>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('incidents.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors" wire:navigate>
                    Cancel
                </a>
                <button type="submit" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium">
                    <span class="btn-magnetic-inner flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $isEditing ? 'Update Incident' : 'Create Incident' }}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
