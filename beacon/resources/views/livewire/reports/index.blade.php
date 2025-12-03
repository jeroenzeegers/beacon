<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Scheduled Reports</h1>
            <p class="text-slate-400 mt-1">Automated email reports for your monitoring data</p>
        </div>
        <a href="{{ route('reports.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Schedule Report
        </a>
    </div>

    @if($reports->count() > 0)
        <div class="space-y-4">
            @foreach($reports as $report)
                <div class="glass rounded-xl p-5 border border-white/10 hover:border-white/20 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-white">{{ $report->name }}</h3>
                                <span class="px-2 py-1 text-xs rounded-full bg-violet-500/20 text-violet-400">
                                    {{ ucwords(str_replace('_', ' ', $report->type)) }}
                                </span>
                                @if(!$report->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-slate-500/20 text-slate-400">Paused</span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-400">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ ucfirst($report->frequency) }} at {{ $report->time }} ({{ $report->timezone }})
                                </span>
                                @if($report->frequency === 'weekly')
                                    <span>on {{ ucfirst($report->day_of_week) }}</span>
                                @elseif($report->frequency === 'monthly')
                                    <span>on day {{ $report->day_of_month }}</span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ count($report->recipients) }} recipient(s)
                                </span>
                            </div>

                            @if($report->next_send_at)
                                <p class="text-xs text-slate-500 mt-2">
                                    Next: {{ $report->next_send_at->format('M d, Y H:i') }} ({{ $report->next_send_at->diffForHumans() }})
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <button wire:click="toggleActive({{ $report->id }})" class="p-2 text-slate-400 hover:text-white transition-colors" title="{{ $report->is_active ? 'Pause' : 'Resume' }}">
                                @if($report->is_active)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </button>
                            <a href="{{ route('reports.edit', $report->id) }}" class="p-2 text-slate-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button wire:click="delete({{ $report->id }})" wire:confirm="Delete this scheduled report?" class="p-2 text-red-400 hover:text-red-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @else
        <div class="glass rounded-xl p-12 border border-white/10 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-cyan-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">No scheduled reports</h3>
            <p class="text-slate-400 mb-6">Set up automated reports to receive regular updates via email.</p>
            <a href="{{ route('reports.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Schedule Report
            </a>
        </div>
    @endif
</div>
