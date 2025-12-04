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
            /* === 2025 Design System === */
            :root {
                /* Low Light Color Palette */
                --bg-primary: #08080c;
                --bg-secondary: #0d0d14;
                --bg-elevated: #12121a;

                /* Accent Colors - Muted & Atmospheric */
                --accent-primary: rgba(139, 92, 246, 0.9);
                --accent-secondary: rgba(99, 102, 241, 0.8);
                --accent-tertiary: rgba(236, 72, 153, 0.7);
                --accent-cyan: rgba(34, 211, 238, 0.7);

                /* Glow Colors */
                --glow-primary: rgba(139, 92, 246, 0.15);
                --glow-secondary: rgba(99, 102, 241, 0.12);
                --glow-success: rgba(34, 197, 94, 0.15);
                --glow-danger: rgba(239, 68, 68, 0.15);

                /* Glass Properties */
                --glass-bg: rgba(255, 255, 255, 0.02);
                --glass-border: rgba(255, 255, 255, 0.06);
                --glass-blur: 24px;

                /* Transitions - 2025 Smooth */
                --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
                --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
                --transition-slow: 400ms cubic-bezier(0.4, 0, 0.2, 1);
                --transition-bounce: 500ms cubic-bezier(0.34, 1.56, 0.64, 1);
            }

            [x-cloak] { display: none !important; }

            /* Enhanced Gradient Mesh Background */
            .gradient-mesh {
                background-color: var(--bg-primary);
                background-image:
                    radial-gradient(ellipse 80% 50% at 20% -20%, rgba(139, 92, 246, 0.12) 0%, transparent 50%),
                    radial-gradient(ellipse 60% 40% at 80% 0%, rgba(99, 102, 241, 0.1) 0%, transparent 45%),
                    radial-gradient(ellipse 50% 30% at 90% 80%, rgba(236, 72, 153, 0.06) 0%, transparent 40%),
                    radial-gradient(ellipse 40% 40% at 10% 90%, rgba(34, 211, 238, 0.06) 0%, transparent 40%),
                    radial-gradient(circle at 50% 50%, rgba(139, 92, 246, 0.03) 0%, transparent 70%);
                background-attachment: fixed;
            }

            /* Enhanced Glassmorphism - 2025 Low Light Style */
            .glass {
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
                backdrop-filter: blur(var(--glass-blur)) saturate(120%);
                -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(120%);
                border: 1px solid var(--glass-border);
                box-shadow:
                    0 4px 24px -1px rgba(0, 0, 0, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.04);
                transition: all var(--transition-base);
            }

            .glass:hover {
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.02) 100%);
                border-color: rgba(255, 255, 255, 0.1);
                box-shadow:
                    0 8px 32px -2px rgba(0, 0, 0, 0.4),
                    0 0 0 1px rgba(255, 255, 255, 0.05),
                    inset 0 1px 0 rgba(255, 255, 255, 0.06);
            }

            .glass-darker {
                background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.3) 100%);
                backdrop-filter: blur(var(--glass-blur)) saturate(150%);
                -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(150%);
                border: 1px solid rgba(255, 255, 255, 0.04);
                box-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);
            }

            /* Ambient Glow Effects */
            .glow-primary {
                box-shadow: 0 0 40px var(--glow-primary), 0 0 80px rgba(139, 92, 246, 0.05);
            }

            .glow-success {
                box-shadow: 0 0 30px var(--glow-success);
            }

            .glow-danger {
                box-shadow: 0 0 30px var(--glow-danger);
            }

            /* 2025 Micro-animations */
            @keyframes subtle-pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }

            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-4px); }
            }

            @keyframes glow-pulse {
                0%, 100% { box-shadow: 0 0 20px var(--glow-primary); }
                50% { box-shadow: 0 0 40px var(--glow-primary), 0 0 60px rgba(139, 92, 246, 0.1); }
            }

            .animate-subtle-pulse {
                animation: subtle-pulse 3s ease-in-out infinite;
            }

            .animate-float {
                animation: float 4s ease-in-out infinite;
            }

            .animate-glow-pulse {
                animation: glow-pulse 3s ease-in-out infinite;
            }

            /* Enhanced Interactive Elements */
            .interactive-card {
                transition: all var(--transition-base);
                cursor: pointer;
            }

            .interactive-card:hover {
                transform: translateY(-2px);
            }

            .interactive-card:active {
                transform: translateY(0);
                transition: all var(--transition-fast);
            }

            /* Button Enhancements */
            .btn-glow {
                position: relative;
                overflow: hidden;
                transition: all var(--transition-base);
            }

            .btn-glow::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
                opacity: 0;
                transition: opacity var(--transition-base);
            }

            .btn-glow:hover::before {
                opacity: 1;
            }

            .btn-glow:hover {
                box-shadow: 0 0 30px var(--glow-primary);
            }

            /* Smooth Focus States */
            *:focus-visible {
                outline: 2px solid var(--accent-primary);
                outline-offset: 2px;
                transition: outline-offset var(--transition-fast);
            }

            /* Enhanced Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.02);
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 4px;
                transition: background var(--transition-base);
            }

            ::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.2);
            }

            /* Text Gradient Effect */
            .text-gradient {
                background: linear-gradient(135deg, #a78bfa 0%, #818cf8 50%, #c084fc 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* Bento Grid Helper */
            .bento-grid {
                display: grid;
                gap: 1rem;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }

            /* Status Indicator Glow */
            .status-online {
                box-shadow: 0 0 12px rgba(34, 197, 94, 0.5);
            }

            .status-offline {
                box-shadow: 0 0 12px rgba(239, 68, 68, 0.5);
            }

            .status-degraded {
                box-shadow: 0 0 12px rgba(245, 158, 11, 0.5);
            }
        </style>
        @stack('styles')

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
