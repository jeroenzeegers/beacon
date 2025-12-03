<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Users extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all';
    public ?int $selectedUserId = null;
    public bool $showUserModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $users = User::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->filter === 'admin', fn ($q) => $q->where('is_admin', true))
            ->when($this->filter === 'verified', fn ($q) => $q->whereNotNull('email_verified_at'))
            ->when($this->filter === 'unverified', fn ($q) => $q->whereNull('email_verified_at'))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.users', [
            'users' => $users,
            'selectedUser' => $this->selectedUserId ? User::find($this->selectedUserId) : null,
        ]);
    }

    public function viewUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->showUserModal = true;
    }

    public function closeModal(): void
    {
        $this->showUserModal = false;
        $this->selectedUserId = null;
    }

    public function toggleAdmin(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'You cannot modify your own admin status.');
            return;
        }

        $user->update(['is_admin' => !$user->is_admin]);

        AuditLog::log(
            'admin_toggle',
            $user->is_admin
                ? "Made {$user->name} an admin"
                : "Removed admin from {$user->name}",
            $user
        );

        session()->flash('success', $user->is_admin
            ? "{$user->name} is now an admin."
            : "{$user->name} is no longer an admin.");
    }

    public function impersonate(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'You cannot impersonate yourself.');
            return;
        }

        Session::put('impersonator_id', Auth::id());

        AuditLog::log(
            'impersonate',
            "Started impersonating {$user->name}",
            $user
        );

        Auth::login($user);

        $this->redirect(route('dashboard'));
    }

    public function verifyEmail(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->markEmailAsVerified();

        AuditLog::log(
            'verify_email',
            "Manually verified email for {$user->name}",
            $user
        );

        session()->flash('success', "Email verified for {$user->name}.");
    }

    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'You cannot delete yourself.');
            return;
        }

        $name = $user->name;

        AuditLog::log(
            'delete',
            "Deleted user {$name}",
            $user
        );

        $user->delete();

        session()->flash('success', "User {$name} has been deleted.");
        $this->closeModal();
    }
}
