<?php

declare(strict_types=1);

namespace App\Livewire\Alerts\Channels;

use App\Models\AlertChannel;
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
    public string $type = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $channel = AlertChannel::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $channel->delete();

        session()->flash('message', 'Alert channel deleted successfully.');
    }

    public function toggleActive(int $id): void
    {
        $channel = AlertChannel::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $channel->update(['is_active' => ! $channel->is_active]);
    }

    public function setAsDefault(int $id): void
    {
        $team = Auth::user()->currentTeam;

        // Remove default from all other channels
        AlertChannel::where('team_id', $team->id)->update(['is_default' => false]);

        // Set this one as default
        AlertChannel::where('team_id', $team->id)
            ->where('id', $id)
            ->update(['is_default' => true]);

        session()->flash('message', 'Default channel updated successfully.');
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        $channels = AlertChannel::where('team_id', $team->id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.alerts.channels.index', [
            'channels' => $channels,
            'types' => AlertChannel::getAvailableTypes(),
        ])->layout('layouts.app');
    }
}
