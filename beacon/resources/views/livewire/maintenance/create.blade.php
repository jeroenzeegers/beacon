<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white mb-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Maintenance Windows
        </a>
        <h1 class="text-2xl font-bold text-white">{{ $isEdit ? 'Edit Maintenance Window' : 'Schedule Maintenance' }}</h1>
        <p class="text-slate-400 mt-1">Plan maintenance to avoid false alerts</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Basic Info -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4">Details</h2>

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Name</label>
                    <input type="text" id="name" wire:model="name"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                        placeholder="e.g., Database Migration">
                    @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300 mb-2">Description (optional)</label>
                    <textarea id="description" wire:model="description" rows="2"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                        placeholder="Brief description of the maintenance..."></textarea>
                </div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4">Schedule</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-slate-300 mb-2">Start Time</label>
                    <input type="datetime-local" id="starts_at" wire:model="starts_at"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                    @error('starts_at') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ends_at" class="block text-sm font-medium text-slate-300 mb-2">End Time</label>
                    <input type="datetime-local" id="ends_at" wire:model="ends_at"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                    @error('ends_at') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Affected Monitors -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-2">Affected Monitors</h2>
            <p class="text-sm text-slate-400 mb-4">Leave empty to apply to all monitors</p>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-64 overflow-y-auto">
                @foreach($monitors as $monitor)
                    <label class="flex items-center gap-2 p-3 bg-white/5 hover:bg-white/10 rounded-lg cursor-pointer transition-colors">
                        <input type="checkbox" wire:model="selected_monitors" value="{{ $monitor->id }}"
                            class="w-4 h-4 rounded border-slate-600 bg-white/10 text-violet-600 focus:ring-violet-500/50">
                        <span class="text-sm text-white truncate">{{ $monitor->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Options -->
        <div class="glass rounded-xl p-6 border border-white/10 space-y-4">
            <h2 class="text-lg font-semibold text-white mb-4">Options</h2>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-medium">Suppress Alerts</p>
                    <p class="text-sm text-slate-400">Don't send alerts for affected monitors during maintenance</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="suppress_alerts" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-violet-500/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-medium">Show on Status Page</p>
                    <p class="text-sm text-slate-400">Display maintenance notice on public status pages</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="show_on_status_page" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-violet-500/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-600"></div>
                </label>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('maintenance.index') }}" class="px-6 py-2 text-slate-400 hover:text-white transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors">
                {{ $isEdit ? 'Update' : 'Schedule Maintenance' }}
            </button>
        </div>
    </form>
</div>
