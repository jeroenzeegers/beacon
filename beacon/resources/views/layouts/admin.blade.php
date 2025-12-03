<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin - {{ config('app.name', 'Beacon') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'system-ui', 'sans-serif'],
                        },
                    }
                }
            }
        </script>

        <style>
            [x-cloak] { display: none !important; }
            .gradient-mesh {
                background-color: #0a0a0f;
                background-image:
                    radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.08) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.05) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, rgba(34, 211, 238, 0.05) 0px, transparent 50%);
            }
            .glass {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
            .glass-darker {
                background: rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
        </style>

        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-toast-notifications />
        <div class="min-h-screen gradient-mesh text-white flex">
            <!-- Sidebar -->
            <aside class="w-64 glass-darker border-r border-white/5 flex flex-col fixed h-full">
                <!-- Logo -->
                <div class="p-6 border-b border-white/5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-lg font-bold text-white">Beacon</span>
                            <span class="block text-xs text-red-400 font-medium">Admin Panel</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                        Dashboard
                    </a>

                    <div class="pt-4 pb-2">
                        <span class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">User Management</span>
                    </div>

                    <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Users
                    </a>

                    <a href="{{ route('admin.teams') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.teams') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Teams
                    </a>

                    <div class="pt-4 pb-2">
                        <span class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Billing</span>
                    </div>

                    <a href="{{ route('admin.subscriptions') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.subscriptions') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Subscriptions
                    </a>

                    <a href="{{ route('admin.plans') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.plans') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Plans & Pricing
                    </a>

                    <div class="pt-4 pb-2">
                        <span class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">System</span>
                    </div>

                    <a href="{{ route('admin.system-health') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.system-health') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        System Health
                    </a>

                    <a href="{{ route('admin.analytics') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.analytics') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Analytics
                    </a>

                    <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.settings') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>

                    <div class="pt-4 pb-2">
                        <span class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Security</span>
                    </div>

                    <a href="{{ route('admin.audit-logs') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.audit-logs') ? 'text-white bg-white/10' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Audit Logs
                    </a>
                </nav>

                <!-- User Section -->
                <div class="p-4 border-t border-white/5">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Back to App
                    </a>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 ml-64">
                <!-- Top Bar -->
                <header class="glass border-b border-white/5 sticky top-0 z-10">
                    <div class="px-8 py-4 flex items-center justify-between">
                        <div>
                            @if (isset($header))
                                {{ $header }}
                            @endif
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-400">{{ auth()->user()->name }}</span>
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Pusher & Echo for real-time updates -->
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        <script>
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: '{{ config('reverb.apps.apps.0.key') }}',
                wsHost: '{{ config('reverb.apps.apps.0.options.host') }}',
                wsPort: {{ config('reverb.apps.apps.0.options.port', 8080) }},
                wssPort: {{ config('reverb.apps.apps.0.options.port', 8080) }},
                forceTLS: {{ config('reverb.apps.apps.0.options.scheme') === 'https' ? 'true' : 'false' }},
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
            });

            // Make Echo available globally for Livewire components
            window.BeaconEcho = window.Echo;
        </script>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
