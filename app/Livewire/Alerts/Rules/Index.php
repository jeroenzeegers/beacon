<?php

declare(strict_types=1);

namespace App\Livewire\Alerts\Rules;

use App\Models\AlertRule;
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
    public string $trigger = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $rule = AlertRule::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $rule->delete();

        session()->flash('message', 'Alert rule deleted successfully.');
    }

    public function toggleActive(int $id): void
    {
        $rule = AlertRule::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $rule->update(['is_active' => ! $rule->is_active]);
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        $rules = AlertRule::where('team_id', $team->id)
            ->with(['monitor', 'project', 'channels'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->trigger, function ($query) {
                $query->where('trigger', $this->trigger);
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.alerts.rules.index', [
            'rules' => $rules,
            'triggers' => AlertRule::getAvailableTriggers(),
        ])->layout('layouts.app');
    }
}
