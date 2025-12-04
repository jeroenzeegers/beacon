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
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex sm:items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('monitors.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('monitors.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        {{ __('Monitors') }}
                    </a>
                    <a href="{{ route('heartbeats.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('heartbeats.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        {{ __('Heartbeats') }}
                    </a>
                    <a href="{{ route('live-status') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('live-status') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        {{ __('Live') }}
                    </a>

                    <!-- Alerts Dropdown -->
                    <div x-data="{ alertsOpen: false }" class="relative">
                        <button @click="alertsOpen = !alertsOpen" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs(['alerts.*']) ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            {{ __('Alerts') }}
                            <svg class="ms-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="alertsOpen" @click.away="alertsOpen = false" x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute left-0 mt-2 w-56 glass-darker rounded-xl shadow-xl py-2 z-50">
                            <a href="{{ route('alerts.channels.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 {{ request()->routeIs('alerts.channels.*') ? 'bg-white/10 text-white' : '' }}">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Channels
                            </a>
                            <a href="{{ route('alerts.rules.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 {{ request()->routeIs('alerts.rules.*') ? 'bg-white/10 text-white' : '' }}">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Rules
                            </a>
                            <a href="{{ route('alerts.logs.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 {{ request()->routeIs('alerts.logs.*') ? 'bg-white/10 text-white' : '' }}">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Logs
                            </a>
                        </div>
                    </div>

                    <!-- More Dropdown -->
                    <div x-data="{ moreOpen: false }" class="relative">
                        <button @click="moreOpen = !moreOpen" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs(['projects.*', 'maintenance.*', 'reports.*', 'status-pages.*', 'incidents.*']) ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            {{ __('More') }}
                            <svg class="ms-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="moreOpen" @click.away="moreOpen = false" x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute left-0 mt-2 w-56 glass-darker rounded-xl shadow-xl py-2 z-50">
                            <a href="{{ route('projects.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 {{ request()->routeIs('projects.*') ? 'bg-white/10 text-white' : '' }}">
                                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                Projects
                            </a>
                            <a href="{{ route('status-pages.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 {{ request()->routeIs('status-pages.*') ? 'bg-white/10 text-white' : '' }}">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Status Pages
                            </a>
                            <a href="{{ route('incidents.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 {{ request()->routeIs('incidents.*') ? 'bg-white/10 text-white' : '' }}">
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Incidents
                            </a>
                            <a href="{{ route('maintenance.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 {{ request()->routeIs('maintenance.*') ? 'bg-white/10 text-white' : '' }}">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Maintenance
                            </a>
                            <a href="{{ route('reports.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 {{ request()->routeIs('reports.*') ? 'bg-white/10 text-white' : '' }}">
                                <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Reports
                            </a>
                        </div>
                    </div>
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
            <a href="{{ route('heartbeats.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('heartbeats.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Heartbeats
            </a>
            <a href="{{ route('projects.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('projects.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Projects
            </a>
            <a href="{{ route('maintenance.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('maintenance.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Maintenance
            </a>
            <a href="{{ route('reports.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('reports.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                Reports
            </a>
            <a href="{{ route('live-status') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('live-status') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Live Status
            </a>

            <div class="border-t border-white/5 my-2 pt-2">
                <span class="block px-4 py-1 text-xs text-gray-500 uppercase">Alerts</span>
                <a href="{{ route('alerts.channels.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('alerts.channels.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    Channels
                </a>
                <a href="{{ route('alerts.rules.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('alerts.rules.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    Rules
                </a>
                <a href="{{ route('alerts.logs.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('alerts.logs.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    Logs
                </a>
            </div>

            <div class="border-t border-white/5 my-2 pt-2">
                <a href="{{ route('status-pages.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('status-pages.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    Status Pages
                </a>
                <a href="{{ route('incidents.index') }}" wire:navigate class="block px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('incidents.*') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    Incidents
                </a>
            </div>
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
