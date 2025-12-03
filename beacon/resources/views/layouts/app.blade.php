<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Beacon') }}</title>

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
        <div class="min-h-screen gradient-mesh text-white">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="border-b border-white/5">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
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
