<?php

declare(strict_types=1);

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceWindow;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function cancel(int $id): void
    {
        $window = MaintenanceWindow::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $window->update(['status' => MaintenanceWindow::STATUS_CANCELLED]);

        session()->flash('success', 'Maintenance window cancelled.');
    }

    public function delete(int $id): void
    {
        $window = MaintenanceWindow::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $window->delete();

        session()->flash('success', 'Maintenance window deleted.');
    }

    public function render()
    {
        $windows = MaintenanceWindow::where('team_id', Auth::user()->current_team_id)
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->with('monitors')
            ->orderByDesc('starts_at')
            ->paginate(10);

        return view('livewire.maintenance.index', [
            'windows' => $windows,
        ])->layout('layouts.app', ['title' => 'Maintenance Windows']);
    }
}
