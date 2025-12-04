<?php

declare(strict_types=1);

namespace App\Livewire\Incidents;

use App\Models\Incident;
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
    public string $severity = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $incident = Incident::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $incident->delete();

        session()->flash('message', 'Incident deleted successfully.');
    }

    public function resolve(int $id): void
    {
        $incident = Incident::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $incident->resolve();

        session()->flash('message', 'Incident marked as resolved.');
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        $incidents = Incident::where('team_id', $team->id)
            ->with(['monitor', 'updates'])
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%");
            })
            ->when($this->status, function ($query) {
                if ($this->status === 'active') {
                    $query->active();
                } else {
                    $query->where('status', $this->status);
                }
            })
            ->when($this->severity, function ($query) {
                $query->where('severity', $this->severity);
            })
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return view('livewire.incidents.index', [
            'incidents' => $incidents,
            'statuses' => Incident::getStatuses(),
            'severities' => Incident::getSeverities(),
        ])->layout('layouts.app');
    }
}
