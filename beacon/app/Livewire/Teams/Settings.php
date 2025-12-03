<?php

declare(strict_types=1);

namespace App\Livewire\Teams;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{
    public string $name = '';

    public function mount(): void
    {
        $team = Auth::user()->currentTeam;
        $this->name = $team->name;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $team = Auth::user()->currentTeam;

        if (!$team->isAdmin(Auth::user())) {
            session()->flash('error', 'You do not have permission to update team settings.');
            return;
        }

        $team->update(['name' => $this->name]);

        session()->flash('message', 'Team settings updated successfully.');
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        return view('livewire.teams.settings', [
            'team' => $team,
            'isOwner' => $team->isOwner(Auth::user()),
            'isAdmin' => $team->isAdmin(Auth::user()),
        ])->layout('layouts.app');
    }
}
