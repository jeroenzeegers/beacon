<?php

declare(strict_types=1);

namespace App\Livewire\Heartbeats;

use App\Models\Heartbeat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $heartbeat = Heartbeat::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $heartbeat->delete();

        session()->flash('success', 'Heartbeat deleted successfully.');
    }

    public function toggleActive(int $id): void
    {
        $heartbeat = Heartbeat::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $heartbeat->update(['is_active' => !$heartbeat->is_active]);
    }

    public function render()
    {
        $heartbeats = Heartbeat::where('team_id', Auth::user()->current_team_id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.heartbeats.index', [
            'heartbeats' => $heartbeats,
        ])->layout('layouts.app', ['title' => 'Heartbeats']);
    }
}
