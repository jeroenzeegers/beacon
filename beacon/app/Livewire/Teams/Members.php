<?php

declare(strict_types=1);

namespace App\Livewire\Teams;

use App\Models\User;
use App\Services\UsageLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Members extends Component
{
    public string $email = '';

    public string $role = 'member';

    public bool $showInviteModal = false;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'role' => ['required', Rule::in(['admin', 'member', 'viewer'])],
        ];
    }

    public function openInviteModal(): void
    {
        $this->showInviteModal = true;
    }

    public function closeInviteModal(): void
    {
        $this->showInviteModal = false;
        $this->reset(['email', 'role']);
    }

    public function invite(): void
    {
        $this->validate();

        $team = Auth::user()->currentTeam;

        if (! $team->isAdmin(Auth::user())) {
            session()->flash('error', 'You do not have permission to invite members.');

            return;
        }

        $usageLimiter = app(UsageLimiter::class);
        if (! $usageLimiter->canAddTeamMember($team)) {
            session()->flash('error', 'You have reached your team member limit. Please upgrade your plan.');

            return;
        }

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            // TODO: Send invitation email to create account
            session()->flash('error', 'User not found. Please ask them to create an account first.');

            return;
        }

        if ($team->hasUser($user)) {
            session()->flash('error', 'This user is already a member of the team.');

            return;
        }

        $team->addUser($user, $this->role);

        session()->flash('message', 'Team member added successfully.');

        $this->closeInviteModal();
    }

    public function updateRole(int $userId, string $role): void
    {
        $team = Auth::user()->currentTeam;

        if (! $team->isAdmin(Auth::user())) {
            session()->flash('error', 'You do not have permission to update member roles.');

            return;
        }

        $user = User::findOrFail($userId);

        if ($team->isOwner($user)) {
            session()->flash('error', 'Cannot change the owner\'s role.');

            return;
        }

        $team->updateUserRole($user, $role);

        session()->flash('message', 'Member role updated successfully.');
    }

    public function remove(int $userId): void
    {
        $team = Auth::user()->currentTeam;

        if (! $team->isAdmin(Auth::user())) {
            session()->flash('error', 'You do not have permission to remove members.');

            return;
        }

        $user = User::findOrFail($userId);

        if ($team->isOwner($user)) {
            session()->flash('error', 'Cannot remove the team owner.');

            return;
        }

        $team->removeUser($user);

        session()->flash('message', 'Team member removed successfully.');
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;
        $members = $team->users()->with('teams')->get();

        $usageLimiter = app(UsageLimiter::class);
        $canAddMember = $usageLimiter->canAddTeamMember($team);

        return view('livewire.teams.members', [
            'team' => $team,
            'members' => $members,
            'canAddMember' => $canAddMember,
            'isOwner' => $team->isOwner(Auth::user()),
            'isAdmin' => $team->isAdmin(Auth::user()),
            'roles' => [
                'admin' => 'Admin',
                'member' => 'Member',
                'viewer' => 'Viewer',
            ],
        ])->layout('layouts.app');
    }
}
