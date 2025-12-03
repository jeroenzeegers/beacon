<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="glass border-b border-white/5">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">Beacon</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('monitors.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('monitors.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        {{ __('Monitors') }}
                    </a>
                    <a href="{{ route('projects.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('projects.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        {{ __('Projects') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:space-x-3">
                <!-- Team Switcher -->
                @if(auth()->user()->teams->count() > 1)
                <div x-data="{ teamOpen: false }" class="relative">
                    <button @click="teamOpen = !teamOpen" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg transition-colors">
                        {{ auth()->user()->currentTeam->name }}
                        <svg class="ms-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                        </svg>
                    </button>
                    <div x-show="teamOpen" @click.away="teamOpen = false" x-cloak class="absolute right-0 mt-2 w-48 glass-darker rounded-xl shadow-xl py-1 z-50">
                        @foreach(auth()->user()->teams as $team)
                            <a href="{{ route('dashboard') }}?switch_team={{ $team->id }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 {{ $team->id === auth()->user()->current_team_id ? 'bg-white/10' : '' }}">
                                {{ $team->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- User Dropdown -->
                <div x-data="{ userOpen: false }" class="relative">
                    <button @click="userOpen = !userOpen" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg transition-colors">
                        <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                        <svg class="ms-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="userOpen" @click.away="userOpen = false" x-cloak class="absolute right-0 mt-2 w-56 glass-darker rounded-xl shadow-xl py-1 z-50">
                        <div class="px-4 py-2 text-xs text-gray-500 uppercase tracking-wider">Account</div>
                        <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5">Profile</a>

                        <div class="px-4 py-2 text-xs text-gray-500 uppercase tracking-wider border-t border-white/5 mt-1 pt-2">Team</div>
                        <a href="{{ route('team.settings') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5">Team Settings</a>
                        <a href="{{ route('team.members') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5">Team Members</a>
                        <a href="{{ route('billing.dashboard') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5">Billing</a>

                        @if(auth()->user()->is_admin)
                            <div class="px-4 py-2 text-xs text-gray-500 uppercase tracking-wider border-t border-white/5 mt-1 pt-2">Admin</div>
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-white/5">Admin Panel</a>
                        @endif

                        <div class="border-t border-white/5 mt-1 pt-1">
                            <button wire:click="logout" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-white/5">
                                Log Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/5">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Dashboard
            </a>
            <a href="{{ route('monitors.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('monitors.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Monitors
            </a>
            <a href="{{ route('projects.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('projects.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Projects
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-white/5 px-4">
            <div class="mb-3">
                <div class="font-medium text-white" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 rounded-lg">Profile</a>
                <a href="{{ route('team.settings') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 rounded-lg">Team Settings</a>
                <a href="{{ route('team.members') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 rounded-lg">Team Members</a>
                <a href="{{ route('billing.dashboard') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 rounded-lg">Billing</a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-white/5 rounded-lg">Admin Panel</a>
                @endif
                <button wire:click="logout" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-white/5 rounded-lg">
                    Log Out
                </button>
            </div>
        </div>
    </div>
</nav>
