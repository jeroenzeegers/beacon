<?php

declare(strict_types=1);

namespace App\Livewire\Monitors;

use App\Models\Monitor;
use App\Models\Project;
use App\Services\UsageLimiter;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $project = '';

    #[Url]
    public string $type = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $monitor = Monitor::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $monitor->delete();

        session()->flash('message', 'Monitor deleted successfully.');
    }

    public function toggleActive(int $id): void
    {
        $monitor = Monitor::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $monitor->update(['is_active' => ! $monitor->is_active]);
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        $monitors = Monitor::where('team_id', $team->id)
            ->with(['project', 'latestCheck'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('target', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->project, function ($query) {
                $query->where('project_id', $this->project);
            })
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->orderBy('name')
            ->paginate(15);

        $projects = Project::where('team_id', $team->id)->get();

        $usageLimiter = app(UsageLimiter::class);
        $canCreateMonitor = $usageLimiter->canCreateMonitor($team);

        return view('livewire.monitors.index', [
            'monitors' => $monitors,
            'projects' => $projects,
            'canCreateMonitor' => $canCreateMonitor,
            'monitorTypes' => Monitor::getAvailableTypes(),
        ])->layout('layouts.app');
    }
}
