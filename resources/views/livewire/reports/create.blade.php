<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white mb-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Reports
        </a>
        <h1 class="text-2xl font-bold text-white">{{ $isEdit ? 'Edit Scheduled Report' : 'Schedule Report' }}</h1>
        <p class="text-slate-400 mt-1">Configure automated email reports</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Basic Info -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4">Report Details</h2>

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Report Name</label>
                    <input type="text" id="name" wire:model="name"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                        placeholder="e.g., Weekly Team Report">
                    @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-slate-300 mb-2">Report Type</label>
                    <select id="type" wire:model="type"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                        @foreach($reportTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4">Schedule</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="frequency" class="block text-sm font-medium text-slate-300 mb-2">Frequency</label>
                    <select id="frequency" wire:model.live="frequency"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>

                @if($frequency === 'weekly')
                    <div>
                        <label for="day_of_week" class="block text-sm font-medium text-slate-300 mb-2">Day of Week</label>
                        <select id="day_of_week" wire:model="day_of_week"
                            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($frequency === 'monthly')
                    <div>
                        <label for="day_of_month" class="block text-sm font-medium text-slate-300 mb-2">Day of Month</label>
                        <input type="number" id="day_of_month" wire:model="day_of_month" min="1" max="28"
                            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                    </div>
                @endif

                <div>
                    <label for="time" class="block text-sm font-medium text-slate-300 mb-2">Time</label>
                    <input type="time" id="time" wire:model="time"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                </div>

                <div>
                    <label for="timezone" class="block text-sm font-medium text-slate-300 mb-2">Timezone</label>
                    <select id="timezone" wire:model="timezone"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-violet-500/50">
                        @foreach($timezones as $tz)
                            <option value="{{ $tz }}">{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Recipients -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4">Recipients</h2>

            <div>
                <label for="recipients" class="block text-sm font-medium text-slate-300 mb-2">Email Addresses</label>
                <textarea id="recipients" wire:model="recipients" rows="3"
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                    placeholder="email1@example.com, email2@example.com"></textarea>
                <p class="mt-1 text-xs text-slate-500">Separate multiple emails with commas</p>
                @error('recipients') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Status -->
        <div class="glass rounded-xl p-6 border border-white/10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Active</h2>
                    <p class="text-sm text-slate-400">Enable or disable this scheduled report</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-violet-500/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-600"></div>
                </label>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('reports.index') }}" class="px-6 py-2 text-slate-400 hover:text-white transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors">
                {{ $isEdit ? 'Update Report' : 'Schedule Report' }}
            </button>
        </div>
    </form>
</div>
