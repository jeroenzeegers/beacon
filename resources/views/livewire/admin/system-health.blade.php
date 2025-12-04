<div>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-white">System Health</h1>
    </x-slot>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- Services Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($services as $name => $service)
            <div class="glass rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white capitalize">{{ $name }}</h3>
                    @if($service['status'] === 'healthy')
                        <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                    @elseif($service['status'] === 'warning')
                        <div class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></div>
                    @else
                        <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                    @endif
                </div>
                <p class="text-sm text-gray-400">{{ $service['message'] }}</p>
                @if(isset($service['latency']))
                    <p class="text-2xl font-bold text-white mt-2">{{ $service['latency'] }}ms</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Queue Status -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Queue Status</h2>
                <div class="flex items-center space-x-2">
                    <button
                        wire:click="retryFailedJobs"
                        class="px-3 py-1.5 text-sm font-medium text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 rounded-lg transition-colors"
                    >
                        Retry Failed
                    </button>
                    <button
                        wire:click="clearFailedJobs"
                        wire:confirm="Are you sure you want to clear all failed jobs?"
                        class="px-3 py-1.5 text-sm font-medium text-red-400 bg-red-500/10 hover:bg-red-500/20 rounded-lg transition-colors"
                    >
                        Clear Failed
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-white">{{ number_format($queues['pending']) }}</p>
                    <p class="text-sm text-gray-400">Pending</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-red-400">{{ number_format($queues['failed']) }}</p>
                    <p class="text-sm text-gray-400">Failed</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-emerald-400">{{ number_format($queues['processed_today']) }}</p>
                    <p class="text-sm text-gray-400">Today</p>
                </div>
            </div>
        </div>

        <!-- Monitor Stats -->
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Monitor Stats</h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="glass rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-white">{{ number_format($monitors['total']) }}</p>
                    <p class="text-sm text-gray-400">Total</p>
                </div>
                <div class="glass rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-emerald-400">{{ number_format($monitors['active']) }}</p>
                    <p class="text-sm text-gray-400">Active</p>
                </div>
                <div class="glass rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-gray-400">{{ number_format($monitors['paused']) }}</p>
                    <p class="text-sm text-gray-400">Paused</p>
                </div>
                <div class="glass rounded-xl p-4 text-center">
                    <p class="text-3xl font-bold text-red-400">{{ number_format($monitors['down']) }}</p>
                    <p class="text-sm text-gray-400">Down</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage -->
    <div class="glass rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Storage</h2>

        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-400">Disk Usage</span>
                <span class="text-sm text-white">{{ $storage['used'] }} / {{ $storage['total'] }}</span>
            </div>
            <div class="h-3 bg-white/10 rounded-full overflow-hidden">
                <div
                    class="h-full {{ $storage['percentage'] > 90 ? 'bg-red-500' : ($storage['percentage'] > 70 ? 'bg-amber-500' : 'bg-emerald-500') }} rounded-full transition-all"
                    style="width: {{ $storage['percentage'] }}%"
                ></div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-lg font-bold text-white">{{ $storage['total'] }}</p>
                <p class="text-sm text-gray-400">Total</p>
            </div>
            <div>
                <p class="text-lg font-bold text-white">{{ $storage['used'] }}</p>
                <p class="text-sm text-gray-400">Used</p>
            </div>
            <div>
                <p class="text-lg font-bold text-white">{{ $storage['free'] }}</p>
                <p class="text-sm text-gray-400">Free</p>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="mt-6 glass rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-white mb-4">System Information</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass rounded-xl p-4">
                <p class="text-sm text-gray-400">PHP Version</p>
                <p class="text-lg font-bold text-white">{{ PHP_VERSION }}</p>
            </div>
            <div class="glass rounded-xl p-4">
                <p class="text-sm text-gray-400">Laravel Version</p>
                <p class="text-lg font-bold text-white">{{ app()->version() }}</p>
            </div>
            <div class="glass rounded-xl p-4">
                <p class="text-sm text-gray-400">Environment</p>
                <p class="text-lg font-bold text-white">{{ app()->environment() }}</p>
            </div>
            <div class="glass rounded-xl p-4">
                <p class="text-sm text-gray-400">Debug Mode</p>
                <p class="text-lg font-bold {{ config('app.debug') ? 'text-amber-400' : 'text-emerald-400' }}">
                    {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                </p>
            </div>
        </div>
    </div>
</div>
