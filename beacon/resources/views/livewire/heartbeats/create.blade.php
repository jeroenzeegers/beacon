<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('heartbeats.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white mb-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Heartbeats
        </a>
        <h1 class="text-2xl font-bold text-white">{{ $isEdit ? 'Edit Heartbeat' : 'Create Heartbeat' }}</h1>
        <p class="text-slate-400 mt-1">{{ $isEdit ? 'Update your heartbeat configuration' : 'Monitor your cron jobs and scheduled tasks' }}</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Basic Information -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4">Basic Information</h2>

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Name</label>
                    <input type="text" id="name" wire:model="name"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                        placeholder="e.g., Daily Backup Job">
                    @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300 mb-2">Description (optional)</label>
                    <textarea id="description" wire:model="description" rows="3"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                        placeholder="Describe what this heartbeat monitors..."></textarea>
                    @error('description') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="project_id" class="block text-sm font-medium text-slate-300 mb-2">Project (optional)</label>
                    <select id="project_id" wire:model="project_id"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                        <option value="">No Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Timing Configuration -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4">Timing Configuration</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="expected_interval" class="block text-sm font-medium text-slate-300 mb-2">Expected Interval (minutes)</label>
                    <input type="number" id="expected_interval" wire:model="expected_interval" min="1" max="10080"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                    <p class="mt-1 text-xs text-slate-500">How often should we expect a ping?</p>
                    @error('expected_interval') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="grace_period" class="block text-sm font-medium text-slate-300 mb-2">Grace Period (minutes)</label>
                    <input type="number" id="grace_period" wire:model="grace_period" min="1" max="60"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                    <p class="mt-1 text-xs text-slate-500">Wait time before marking as missing</p>
                    @error('grace_period') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-4 p-4 bg-slate-800/50 rounded-lg">
                <h3 class="text-sm font-medium text-slate-300 mb-2">Timeline Preview</h3>
                <div class="flex items-center gap-2 text-xs">
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-400">Healthy (0-{{ $expected_interval }} min)</span>
                    </div>
                    <span class="text-slate-600">→</span>
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="text-slate-400">Late ({{ $expected_interval }}-{{ $expected_interval + $grace_period }} min)</span>
                    </div>
                    <span class="text-slate-600">→</span>
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <span class="text-slate-400">Missing (>{{ $expected_interval + $grace_period }} min)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Active</h2>
                    <p class="text-sm text-slate-400">Enable or disable monitoring for this heartbeat</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-violet-500/50 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-600"></div>
                </label>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('heartbeats.index') }}" class="px-6 py-2 text-slate-400 hover:text-white transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors">
                {{ $isEdit ? 'Update Heartbeat' : 'Create Heartbeat' }}
            </button>
        </div>
    </form>
</div>
