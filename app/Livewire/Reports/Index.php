<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Models\ScheduledReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function toggleActive(int $id): void
    {
        $report = ScheduledReport::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $report->update(['is_active' => ! $report->is_active]);

        if ($report->is_active && ! $report->next_send_at) {
            $report->calculateNextSendAt();
        }
    }

    public function delete(int $id): void
    {
        $report = ScheduledReport::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $report->delete();

        session()->flash('success', 'Scheduled report deleted.');
    }

    public function render()
    {
        $reports = ScheduledReport::where('team_id', Auth::user()->current_team_id)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.reports.index', [
            'reports' => $reports,
        ])->layout('layouts.app', ['title' => 'Scheduled Reports']);
    }
}
