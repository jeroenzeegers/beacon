<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gradient">
            {{ $isEditing ? __('Edit Status Page') : __('Create Status Page') }}
        </h2>
    </div>
</x-slot>

<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <form wire:submit="save" class="space-y-6">
            <!-- Basic Information -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Page Name</label>
                        <input type="text" id="name" wire:model.live="name" class="input-liquid w-full" placeholder="e.g., My App Status">
                        @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-300 mb-2">URL Slug</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">{{ url('/status') }}/</span>
                            <input type="text" id="slug" wire:model="slug" class="input-liquid flex-1" placeholder="my-app-status">
                        </div>
                        @error('slug') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea id="description" wire:model="description" rows="3" class="input-liquid w-full" placeholder="A brief description of what this status page shows..."></textarea>
                        @error('description') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Monitors -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Monitors to Display</h3>

                @if($monitors->isEmpty())
                    <div class="text-center py-6">
                        <p class="text-gray-400 mb-4">No monitors available.</p>
                        <a href="{{ route('monitors.create') }}" class="text-indigo-400 hover:text-indigo-300 text-sm" wire:navigate>
                            Create a monitor first
                        </a>
                    </div>
                @else
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($monitors as $monitor)
                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors {{ in_array($monitor->id, $monitor_ids) ? 'border-indigo-500/50 bg-indigo-500/10' : 'border-white/10 hover:border-white/20' }}">
                                <input type="checkbox" wire:model="monitor_ids" value="{{ $monitor->id }}" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                                <div class="flex-1 flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-white">{{ $monitor->name }}</span>
                                        <span class="text-xs text-gray-500 ml-2">{{ strtoupper($monitor->type) }}</span>
                                    </div>
                                    @if($monitor->status === 'up')
                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    @elseif($monitor->status === 'down')
                                        <span class="h-2 w-2 rounded-full bg-red-400"></span>
                                    @else
                                        <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('monitor_ids') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                @endif
            </div>

            <!-- Display Options -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Display Options</h3>

                <div class="space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="is_public" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                        <div>
                            <span class="text-sm font-medium text-white">Public</span>
                            <p class="text-xs text-gray-500">Make this status page visible to everyone.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="show_uptime" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                        <div>
                            <span class="text-sm font-medium text-white">Show Uptime</span>
                            <p class="text-xs text-gray-500">Display uptime percentage for each monitor.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="show_response_time" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                        <div>
                            <span class="text-sm font-medium text-white">Show Response Time</span>
                            <p class="text-xs text-gray-500">Display average response time for each monitor.</p>
                        </div>
                    </label>

                    <div>
                        <label for="uptime_days_shown" class="block text-sm font-medium text-gray-300 mb-2">Uptime History (days)</label>
                        <select id="uptime_days_shown" wire:model="uptime_days_shown" class="input-liquid w-full">
                            <option value="7">7 days</option>
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90">90 days</option>
                            <option value="180">180 days</option>
                            <option value="365">365 days</option>
                        </select>
                        @error('uptime_days_shown') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Branding -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Branding</h3>

                <div class="space-y-4">
                    <div>
                        <label for="logo_url" class="block text-sm font-medium text-gray-300 mb-2">Logo URL</label>
                        <input type="url" id="logo_url" wire:model="logo_url" class="input-liquid w-full" placeholder="https://example.com/logo.png">
                        @error('logo_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-300 mb-2">Primary Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="primary_color" wire:model="primary_color" class="w-12 h-10 rounded border border-white/10 bg-transparent cursor-pointer">
                            <input type="text" wire:model="primary_color" class="input-liquid flex-1" placeholder="#8B5CF6">
                        </div>
                        @error('primary_color') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="custom_domain" class="block text-sm font-medium text-gray-300 mb-2">Custom Domain (optional)</label>
                        <input type="text" id="custom_domain" wire:model="custom_domain" class="input-liquid w-full" placeholder="status.yourdomain.com">
                        <p class="mt-1 text-xs text-gray-500">Point a CNAME record to our status page domain.</p>
                        @error('custom_domain') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('status-pages.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors" wire:navigate>
                    Cancel
                </a>
                <button type="submit" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium">
                    <span class="btn-magnetic-inner flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $isEditing ? 'Update Status Page' : 'Create Status Page' }}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
