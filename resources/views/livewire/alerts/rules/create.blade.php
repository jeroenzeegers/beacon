<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gradient">
            {{ $isEditing ? __('Edit Alert Rule') : __('Create Alert Rule') }}
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
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Rule Name</label>
                        <input type="text" id="name" wire:model="name" class="input-liquid w-full" placeholder="e.g., Critical Downtime Alert">
                        @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="trigger" class="block text-sm font-medium text-gray-300 mb-2">Trigger Event</label>
                        <select id="trigger" wire:model="trigger" class="input-liquid w-full">
                            @foreach($triggers as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('trigger') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Scope -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Scope</h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-3">
                        <label class="relative flex items-center justify-center p-4 rounded-xl border cursor-pointer transition-colors {{ $scope === 'global' ? 'border-indigo-500 bg-indigo-500/10' : 'border-white/10 hover:border-white/20' }}">
                            <input type="radio" wire:model.live="scope" value="global" class="sr-only">
                            <div class="text-center">
                                <svg class="w-6 h-6 mx-auto mb-2 {{ $scope === 'global' ? 'text-indigo-400' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium {{ $scope === 'global' ? 'text-white' : 'text-gray-400' }}">Global</span>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-4 rounded-xl border cursor-pointer transition-colors {{ $scope === 'project' ? 'border-indigo-500 bg-indigo-500/10' : 'border-white/10 hover:border-white/20' }}">
                            <input type="radio" wire:model.live="scope" value="project" class="sr-only">
                            <div class="text-center">
                                <svg class="w-6 h-6 mx-auto mb-2 {{ $scope === 'project' ? 'text-indigo-400' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span class="text-sm font-medium {{ $scope === 'project' ? 'text-white' : 'text-gray-400' }}">Project</span>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-4 rounded-xl border cursor-pointer transition-colors {{ $scope === 'monitor' ? 'border-indigo-500 bg-indigo-500/10' : 'border-white/10 hover:border-white/20' }}">
                            <input type="radio" wire:model.live="scope" value="monitor" class="sr-only">
                            <div class="text-center">
                                <svg class="w-6 h-6 mx-auto mb-2 {{ $scope === 'monitor' ? 'text-indigo-400' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="text-sm font-medium {{ $scope === 'monitor' ? 'text-white' : 'text-gray-400' }}">Monitor</span>
                            </div>
                        </label>
                    </div>

                    @if($scope === 'project')
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-300 mb-2">Select Project</label>
                            <select id="project_id" wire:model="project_id" class="input-liquid w-full">
                                <option value="">Choose a project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @elseif($scope === 'monitor')
                        <div>
                            <label for="monitor_id" class="block text-sm font-medium text-gray-300 mb-2">Select Monitor</label>
                            <select id="monitor_id" wire:model="monitor_id" class="input-liquid w-full">
                                <option value="">Choose a monitor...</option>
                                @foreach($monitors as $monitor)
                                    <option value="{{ $monitor->id }}">{{ $monitor->name }}</option>
                                @endforeach
                            </select>
                            @error('monitor_id') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notification Channels -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Notification Channels</h3>

                @if($channels->isEmpty())
                    <div class="text-center py-6">
                        <p class="text-gray-400 mb-4">No active channels available.</p>
                        <a href="{{ route('alerts.channels.create') }}" class="text-indigo-400 hover:text-indigo-300 text-sm" wire:navigate>
                            Create a channel first
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($channels as $channel)
                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors {{ in_array($channel->id, $channel_ids) ? 'border-indigo-500/50 bg-indigo-500/10' : 'border-white/10 hover:border-white/20' }}">
                                <input type="checkbox" wire:model="channel_ids" value="{{ $channel->id }}" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-white">{{ $channel->name }}</span>
                                    <span class="text-xs text-gray-500 ml-2">{{ ucfirst($channel->type) }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('channel_ids') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                @endif
            </div>

            <!-- Advanced Settings -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Advanced Settings</h3>

                <div class="space-y-4">
                    <div>
                        <label for="cooldown_minutes" class="block text-sm font-medium text-gray-300 mb-2">Cooldown Period (minutes)</label>
                        <input type="number" id="cooldown_minutes" wire:model="cooldown_minutes" class="input-liquid w-full" min="1" max="1440">
                        <p class="mt-1 text-xs text-gray-500">Minimum time between alerts for this rule.</p>
                        @error('cooldown_minutes') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    @if($trigger === 'response_slow')
                        <div>
                            <label for="response_time_threshold" class="block text-sm font-medium text-gray-300 mb-2">Response Time Threshold (ms)</label>
                            <input type="number" id="response_time_threshold" wire:model="response_time_threshold" class="input-liquid w-full" min="100" placeholder="e.g., 2000">
                            <p class="mt-1 text-xs text-gray-500">Alert when response time exceeds this value.</p>
                        </div>
                    @endif

                    @if($trigger === 'ssl_expiring')
                        <div>
                            <label for="ssl_days_threshold" class="block text-sm font-medium text-gray-300 mb-2">SSL Days Threshold</label>
                            <input type="number" id="ssl_days_threshold" wire:model="ssl_days_threshold" class="input-liquid w-full" min="1" placeholder="e.g., 14">
                            <p class="mt-1 text-xs text-gray-500">Alert when SSL certificate expires within this many days.</p>
                        </div>
                    @endif

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                        <div>
                            <span class="text-sm font-medium text-white">Active</span>
                            <p class="text-xs text-gray-500">Enable this rule to trigger alerts.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('alerts.rules.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors" wire:navigate>
                    Cancel
                </a>
                <button type="submit" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium">
                    <span class="btn-magnetic-inner flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $isEditing ? 'Update Rule' : 'Create Rule' }}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
