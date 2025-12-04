<div>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-white">System Settings</h1>
    </x-slot>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Application Settings -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Application</h2>

            <div class="space-y-4">
                <div class="glass rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Application Name</p>
                            <p class="text-sm text-gray-400">{{ $appName }}</p>
                        </div>
                    </div>
                </div>

                <div class="glass rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Application URL</p>
                            <p class="text-sm text-gray-400">{{ $appUrl }}</p>
                        </div>
                    </div>
                </div>

                <div class="glass rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Environment</p>
                            <p class="text-sm text-gray-400">{{ app()->environment() }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ app()->environment() === 'production' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                            {{ ucfirst(app()->environment()) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Maintenance</h2>

            <div class="space-y-4">
                <div class="glass rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Maintenance Mode</p>
                            <p class="text-sm text-gray-400">
                                {{ $maintenanceMode ? 'Application is in maintenance mode' : 'Application is running normally' }}
                            </p>
                        </div>
                        <button
                            wire:click="toggleMaintenanceMode"
                            wire:confirm="{{ $maintenanceMode ? 'Disable maintenance mode?' : 'Enable maintenance mode? This will make the application unavailable to users.' }}"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $maintenanceMode ? 'text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20' : 'text-red-400 bg-red-500/10 hover:bg-red-500/20' }}"
                        >
                            {{ $maintenanceMode ? 'Disable' : 'Enable' }}
                        </button>
                    </div>
                </div>

                <div class="glass rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">User Registration</p>
                            <p class="text-sm text-gray-400">
                                {{ $registrationEnabled ? 'New users can register' : 'Registration is disabled' }}
                            </p>
                        </div>
                        <button
                            wire:click="toggleRegistration"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $registrationEnabled ? 'text-red-400 bg-red-500/10 hover:bg-red-500/20' : 'text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20' }}"
                        >
                            {{ $registrationEnabled ? 'Disable' : 'Enable' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Management -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Cache Management</h2>

            <div class="space-y-4">
                <div class="glass rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Clear All Caches</p>
                            <p class="text-sm text-gray-400">Clear config, route, view, and application cache</p>
                        </div>
                        <button
                            wire:click="clearCache"
                            class="px-4 py-2 text-sm font-medium text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 rounded-lg transition-colors"
                        >
                            Clear Cache
                        </button>
                    </div>
                </div>

                <div class="glass rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Optimize Application</p>
                            <p class="text-sm text-gray-400">Cache config, routes, and views for production</p>
                        </div>
                        <button
                            wire:click="optimizeApplication"
                            class="px-4 py-2 text-sm font-medium text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 rounded-lg transition-colors"
                        >
                            Optimize
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">System Information</h2>

            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-gray-400">PHP Version</span>
                    <span class="text-white">{{ PHP_VERSION }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-gray-400">Laravel Version</span>
                    <span class="text-white">{{ app()->version() }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-gray-400">Livewire Version</span>
                    <span class="text-white">{{ \Livewire\Livewire::VERSION ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-gray-400">Debug Mode</span>
                    <span class="{{ config('app.debug') ? 'text-amber-400' : 'text-emerald-400' }}">
                        {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-gray-400">Timezone</span>
                    <span class="text-white">{{ config('app.timezone') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
