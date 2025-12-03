<?php

declare(strict_types=1);

namespace App\Livewire\Heartbeats;

use App\Models\Heartbeat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Heartbeat $heartbeat;

    public function mount(int $id): void
    {
        $this->heartbeat = Heartbeat::where('team_id', Auth::user()->current_team_id)
            ->with('project')
            ->findOrFail($id);
    }

    public function delete(): void
    {
        $this->heartbeat->delete();
        session()->flash('success', 'Heartbeat deleted successfully.');
        $this->redirect(route('heartbeats.index'));
    }

    public function toggleActive(): void
    {
        $this->heartbeat->update(['is_active' => !$this->heartbeat->is_active]);
        $this->heartbeat->refresh();
    }

    public function regenerateSlug(): void
    {
        $this->heartbeat->update(['slug' => \Illuminate\Support\Str::random(32)]);
        $this->heartbeat->refresh();
        session()->flash('success', 'Ping URL regenerated.');
    }

    public function render()
    {
        $pings = $this->heartbeat->pings()
            ->orderByDesc('pinged_at')
            ->paginate(20);

        return view('livewire.heartbeats.show', [
            'pings' => $pings,
        ])->layout('layouts.app', ['title' => $this->heartbeat->name]);
    }
}
