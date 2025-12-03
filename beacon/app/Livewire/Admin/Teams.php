<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Teams extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = 'all';

    public ?int $selectedTeamId = null;

    public bool $showTeamModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $teams = Team::query()
            ->with(['owner', 'users'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filter === 'subscribed', fn ($q) => $q->whereHas('subscriptions', function ($sq) {
                $sq->where('stripe_status', 'active');
            }))
            ->when($this->filter === 'free', fn ($q) => $q->whereDoesntHave('subscriptions', function ($sq) {
                $sq->where('stripe_status', 'active');
            }))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.teams', [
            'teams' => $teams,
            'selectedTeam' => $this->selectedTeamId ? Team::with(['owner', 'users', 'monitors', 'projects'])->find($this->selectedTeamId) : null,
        ]);
    }

    public function viewTeam(int $teamId): void
    {
        $this->selectedTeamId = $teamId;
        $this->showTeamModal = true;
    }

    public function closeModal(): void
    {
        $this->showTeamModal = false;
        $this->selectedTeamId = null;
    }

    public function deleteTeam(int $teamId): void
    {
        $team = Team::findOrFail($teamId);
        $name = $team->name;

        AuditLog::log(
            'delete',
            "Deleted team {$name}",
            $team
        );

        $team->delete();

        session()->flash('success', "Team {$name} has been deleted.");
        $this->closeModal();
    }
}
