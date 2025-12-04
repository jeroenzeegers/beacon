<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $monitorId ? __('Edit Monitor') : __('Create Monitor') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <form wire:submit="save">
                <div class="p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input wire:model="name" type="text" id="name" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="My Website">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Monitor Type</label>
                        <select wire:model.live="type" id="type" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Target (URL for HTTP/HTTPS, Host for others) -->
                    <div>
                        <label for="target" class="block text-sm font-medium text-gray-700">
                            @if(in_array($type, ['http', 'https']))
                                URL
                            @else
                                Host
                            @endif
                        </label>
                        <input wire:model="target" type="text" id="target" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ in_array($type, ['http', 'https']) ? 'https://example.com' : 'example.com' }}">
                        @error('target') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Port (for TCP) -->
                    @if($type === 'tcp')
                        <div>
                            <label for="port" class="block text-sm font-medium text-gray-700">Port</label>
                            <input wire:model="port" type="number" id="port" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="443">
                            @error('port') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <!-- Check Interval -->
                    <div>
                        <label for="check_interval" class="block text-sm font-medium text-gray-700">Check Interval</label>
                        <select wire:model="check_interval" id="check_interval" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="60">Every minute</option>
                            <option value="300">Every 5 minutes</option>
                            <option value="600">Every 10 minutes</option>
                            <option value="900">Every 15 minutes</option>
                            <option value="1800">Every 30 minutes</option>
                            <option value="3600">Every hour</option>
                        </select>
                        @error('check_interval') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- HTTP Options -->
                    @if(in_array($type, ['http', 'https']))
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">HTTP Options</h3>

                            <div class="mt-4 space-y-4">
                                <!-- HTTP Method -->
                                <div>
                                    <label for="http_method" class="block text-sm font-medium text-gray-700">HTTP Method</label>
                                    <select wire:model="http_method" id="http_method" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="GET">GET</option>
                                        <option value="POST">POST</option>
                                        <option value="HEAD">HEAD</option>
                                    </select>
                                </div>

                                <!-- Expected Status Codes -->
                                <div>
                                    <label for="expected_status" class="block text-sm font-medium text-gray-700">Expected Status Codes</label>
                                    <input wire:model="expected_status" type="text" id="expected_status" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="200,201,204">
                                    <p class="mt-1 text-sm text-gray-500">Comma-separated list of acceptable status codes</p>
                                    @error('expected_status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Timeout -->
                                <div>
                                    <label for="timeout" class="block text-sm font-medium text-gray-700">Timeout (seconds)</label>
                                    <input wire:model="timeout" type="number" id="timeout" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="30">
                                    @error('timeout') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- SSL Options -->
                    @if($type === 'ssl_expiry')
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">SSL Options</h3>

                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="ssl_warning_days" class="block text-sm font-medium text-gray-700">Warning Threshold (days)</label>
                                    <input wire:model="ssl_warning_days" type="number" id="ssl_warning_days" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="30">
                                    <p class="mt-1 text-sm text-gray-500">Send warning when certificate expires within this many days</p>
                                    @error('ssl_warning_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="ssl_critical_days" class="block text-sm font-medium text-gray-700">Critical Threshold (days)</label>
                                    <input wire:model="ssl_critical_days" type="number" id="ssl_critical_days" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="7">
                                    <p class="mt-1 text-sm text-gray-500">Send critical alert when certificate expires within this many days</p>
                                    @error('ssl_critical_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Project -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700">Project (Optional)</label>
                        <select wire:model="project_id" id="project_id" class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">No Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Enabled -->
                    <div class="flex items-center">
                        <input wire:model="is_enabled" type="checkbox" id="is_enabled" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_enabled" class="ml-2 block text-sm text-gray-900">Enable monitoring</label>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3 rounded-b-lg">
                    <a href="{{ route('monitors.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:navigate>
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ $monitorId ? 'Update Monitor' : 'Create Monitor' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
