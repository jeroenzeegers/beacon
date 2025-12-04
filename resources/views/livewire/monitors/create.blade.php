<x-slot name="header">
    <h2 class="text-2xl font-bold text-gradient">
        {{ $monitorId ? __('Edit Monitor') : __('Create Monitor') }}
    </h2>
</x-slot>

<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if (session('error'))
            <div class="mb-6 p-4 glass rounded-xl border border-red-500/30 text-sm text-red-400">
                {{ session('error') }}
            </div>
        @endif

        @if (session('message'))
            <div class="mb-6 p-4 glass rounded-xl border border-emerald-500/30 text-sm text-emerald-400">
                {{ session('message') }}
            </div>
        @endif

        <div class="glass rounded-2xl overflow-hidden">
            <form wire:submit="save">
                <div class="p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Name</label>
                        <input wire:model="name" type="text" id="name" class="input-liquid block w-full" placeholder="My Website">
                        @error('name') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-300 mb-2">Monitor Type</label>
                        <select wire:model.live="type" id="type" class="input-liquid block w-full">
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Target (URL for HTTP/HTTPS, Host for others) -->
                    <div>
                        <label for="target" class="block text-sm font-medium text-gray-300 mb-2">
                            @if(in_array($type, ['http', 'https']))
                                URL
                            @else
                                Host
                            @endif
                        </label>
                        <input wire:model="target" type="text" id="target" class="input-liquid block w-full" placeholder="{{ in_array($type, ['http', 'https']) ? 'https://example.com' : 'example.com' }}">
                        @error('target') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Port (for TCP) -->
                    @if($type === 'tcp')
                        <div>
                            <label for="port" class="block text-sm font-medium text-gray-300 mb-2">Port</label>
                            <input wire:model="port" type="number" id="port" class="input-liquid block w-full" placeholder="443">
                            @error('port') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <!-- Check Interval -->
                    <div>
                        <label for="check_interval" class="block text-sm font-medium text-gray-300 mb-2">Check Interval</label>
                        <select wire:model="check_interval" id="check_interval" class="input-liquid block w-full">
                            <option value="60">Every minute</option>
                            <option value="300">Every 5 minutes</option>
                            <option value="600">Every 10 minutes</option>
                            <option value="900">Every 15 minutes</option>
                            <option value="1800">Every 30 minutes</option>
                            <option value="3600">Every hour</option>
                        </select>
                        @error('check_interval') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- HTTP Options -->
                    @if(in_array($type, ['http', 'https']))
                        <div class="border-t border-white/5 pt-6">
                            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                                HTTP Options
                            </h3>

                            <div class="space-y-4">
                                <!-- HTTP Method -->
                                <div>
                                    <label for="http_method" class="block text-sm font-medium text-gray-300 mb-2">HTTP Method</label>
                                    <select wire:model="http_method" id="http_method" class="input-liquid block w-full">
                                        <option value="GET">GET</option>
                                        <option value="POST">POST</option>
                                        <option value="HEAD">HEAD</option>
                                    </select>
                                </div>

                                <!-- Expected Status Codes -->
                                <div>
                                    <label for="expected_status" class="block text-sm font-medium text-gray-300 mb-2">Expected Status Codes</label>
                                    <input wire:model="expected_status" type="text" id="expected_status" class="input-liquid block w-full" placeholder="200,201,204">
                                    <p class="mt-2 text-sm text-gray-500">Comma-separated list of acceptable status codes</p>
                                    @error('expected_status') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <!-- Timeout -->
                                <div>
                                    <label for="timeout" class="block text-sm font-medium text-gray-300 mb-2">Timeout (seconds)</label>
                                    <input wire:model="timeout" type="number" id="timeout" class="input-liquid block w-full" placeholder="30">
                                    @error('timeout') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- SSL Options -->
                    @if($type === 'ssl_expiry')
                        <div class="border-t border-white/5 pt-6">
                            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                SSL Options
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="ssl_warning_days" class="block text-sm font-medium text-gray-300 mb-2">Warning Threshold (days)</label>
                                    <input wire:model="ssl_warning_days" type="number" id="ssl_warning_days" class="input-liquid block w-full" placeholder="30">
                                    <p class="mt-2 text-sm text-gray-500">Send warning when certificate expires within this many days</p>
                                    @error('ssl_warning_days') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="ssl_critical_days" class="block text-sm font-medium text-gray-300 mb-2">Critical Threshold (days)</label>
                                    <input wire:model="ssl_critical_days" type="number" id="ssl_critical_days" class="input-liquid block w-full" placeholder="7">
                                    <p class="mt-2 text-sm text-gray-500">Send critical alert when certificate expires within this many days</p>
                                    @error('ssl_critical_days') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Project -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-300 mb-2">Project (Optional)</label>
                        <select wire:model="project_id" id="project_id" class="input-liquid block w-full">
                            <option value="">No Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Active -->
                    <div class="flex items-center gap-3">
                        <input wire:model="is_active" type="checkbox" id="is_active" class="w-5 h-5 rounded bg-white/5 border-white/10 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                        <label for="is_active" class="text-sm text-gray-300">Enable monitoring</label>
                    </div>
                </div>

                <div class="px-6 py-4 bg-white/[0.02] border-t border-white/5 flex justify-end gap-3">
                    <a href="{{ route('monitors.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium text-gray-300 bg-white/5 border border-white/10 hover:bg-white/10 transition-colors" wire:navigate>
                        Cancel
                    </a>
                    <button type="submit" class="btn-liquid inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium" wire:loading.attr="disabled">
                        <svg wire:loading.remove class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>{{ $monitorId ? 'Update Monitor' : 'Create Monitor' }}</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
