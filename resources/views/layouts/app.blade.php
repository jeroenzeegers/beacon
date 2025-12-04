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
            /* === Simplified 2026 Design System === */
            :root {
                /* Color Palette */
                --bg-void: #050508;
                --bg-primary: #08080c;
                --bg-secondary: #0c0c12;
                --bg-elevated: #101018;
                --bg-surface: #141420;

                /* Accent Colors */
                --liquid-primary: rgba(139, 92, 246, 0.85);
                --liquid-secondary: rgba(99, 102, 241, 0.75);
                --liquid-cyan: rgba(34, 211, 238, 0.75);
                --liquid-emerald: rgba(16, 185, 129, 0.75);

                /* Shadows */
                --shadow-sm: 0 2px 8px -2px rgba(0,0,0,0.5);
                --shadow-md: 0 4px 16px -4px rgba(0,0,0,0.6);
                --shadow-lg: 0 8px 32px -8px rgba(0,0,0,0.7);

                /* Glass Properties */
                --glass-blur: 24px;

                /* Motion */
                --ease-default: cubic-bezier(0.4, 0, 0.2, 1);
                --duration-fast: 150ms;
                --duration-base: 200ms;
            }

            [x-cloak] { display: none !important; }

            /* === Background === */
            .gradient-mesh {
                background-color: var(--bg-void);
                background-image:
                    radial-gradient(ellipse 100% 80% at 10% -30%, rgba(139, 92, 246, 0.15) 0%, transparent 55%),
                    radial-gradient(ellipse 80% 60% at 90% -10%, rgba(99, 102, 241, 0.12) 0%, transparent 50%),
                    radial-gradient(ellipse 60% 60% at 0% 100%, rgba(34, 211, 238, 0.06) 0%, transparent 40%);
                background-attachment: fixed;
                min-height: 100vh;
            }

            /* === Glass Effects === */
            .glass {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(var(--glass-blur)) saturate(150%);
                -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(150%);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 16px;
                box-shadow: var(--shadow-md);
                transition: border-color var(--duration-base) var(--ease-default);
            }

            .glass:hover {
                border-color: rgba(255, 255, 255, 0.12);
            }

            .glass-liquid {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(32px) saturate(180%);
                -webkit-backdrop-filter: blur(32px) saturate(180%);
                border: 1px solid rgba(255, 255, 255, 0.06);
                border-radius: 20px;
                box-shadow: var(--shadow-lg);
                transition: border-color var(--duration-base) var(--ease-default);
            }

            .glass-liquid:hover {
                border-color: rgba(255, 255, 255, 0.1);
            }

            .glass-darker {
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(var(--glass-blur)) saturate(180%);
                -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(180%);
                border: 1px solid rgba(255, 255, 255, 0.05);
                box-shadow: var(--shadow-lg);
            }

            /* === Text Gradients === */
            .text-gradient {
                background: linear-gradient(135deg, #a78bfa 0%, #818cf8 50%, #c084fc 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .text-gradient-cyan {
                background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .text-gradient-emerald {
                background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* === Status Indicators === */
            .status-online {
                box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
            }

            .status-offline {
                box-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
            }

            .status-degraded {
                box-shadow: 0 0 8px rgba(245, 158, 11, 0.5);
            }

            /* === Bento Grid === */
            .bento-grid {
                display: grid;
                gap: 1.25rem;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            }

            .bento-span-2 {
                grid-column: span 2;
            }

            .bento-span-row {
                grid-row: span 2;
            }

            /* === Scrollbar === */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.02);
            }

            ::-webkit-scrollbar-thumb {
                background: rgba(139, 92, 246, 0.25);
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: rgba(139, 92, 246, 0.4);
            }

            /* === Focus States === */
            *:focus-visible {
                outline: 2px solid var(--liquid-primary);
                outline-offset: 2px;
                border-radius: 4px;
            }

            .focus-ring:focus-visible {
                outline: 2px solid var(--liquid-primary);
                outline-offset: 3px;
            }

            /* === Form Inputs === */
            .input-liquid {
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 10px;
                padding: 10px 14px;
                color: white;
                transition: border-color var(--duration-fast) var(--ease-default);
            }

            .input-liquid:focus {
                border-color: var(--liquid-primary);
                outline: none;
                box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.15);
            }

            .input-liquid::placeholder {
                color: rgba(255, 255, 255, 0.35);
            }

            /* === Progress Bars === */
            .progress-liquid {
                height: 6px;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 3px;
                overflow: hidden;
            }

            .progress-liquid-bar {
                height: 100%;
                background: linear-gradient(90deg, var(--liquid-primary), var(--liquid-cyan));
                border-radius: 3px;
                transition: width var(--duration-base) var(--ease-default);
            }

            /* === Buttons === */
            .btn-liquid {
                background: linear-gradient(135deg, var(--liquid-primary), var(--liquid-secondary));
                border: none;
                border-radius: 10px;
                padding: 10px 20px;
                color: white;
                font-weight: 600;
                transition: opacity var(--duration-fast) var(--ease-default);
                box-shadow: var(--shadow-sm);
            }

            .btn-liquid:hover {
                opacity: 0.9;
            }

            /* === Skeleton Loading === */
            .skeleton {
                background: linear-gradient(90deg, rgba(255,255,255,0.02) 0%, rgba(255,255,255,0.05) 50%, rgba(255,255,255,0.02) 100%);
                background-size: 200% 100%;
                animation: skeleton-shimmer 1.5s infinite;
                border-radius: 6px;
            }

            @keyframes skeleton-shimmer {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }

            /* === Tooltips === */
            .tooltip {
                position: relative;
            }

            .tooltip::after {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%) translateY(-6px);
                padding: 6px 10px;
                background: var(--bg-surface);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 6px;
                font-size: 12px;
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: opacity var(--duration-fast) var(--ease-default);
            }

            .tooltip:hover::after {
                opacity: 1;
                visibility: visible;
            }

            /* === Cards === */
            .interactive-card {
                transition: transform var(--duration-fast) var(--ease-default);
            }

            .interactive-card:hover {
                transform: translateY(-2px);
            }

            /* === High Contrast Support === */
            @media (prefers-contrast: high) {
                .glass, .glass-liquid, .glass-darker {
                    background: rgba(0, 0, 0, 0.9);
                    border: 2px solid rgba(255, 255, 255, 0.5);
                }

                .text-gradient, .text-gradient-cyan, .text-gradient-emerald {
                    -webkit-text-fill-color: currentColor;
                    background: none;
                }
            }

            /* === Reduced Motion === */
            @media (prefers-reduced-motion: reduce) {
                *, *::before, *::after {
                    animation-duration: 0.01ms !important;
                    transition-duration: 0.01ms !important;
                }
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

        <!-- Chart.js for data visualization -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

        <!-- Pusher & Echo for real-time updates -->
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        <script>
            try {
                console.log('Echo constructor available:', typeof Echo);
                console.log('Pusher available:', typeof Pusher);

                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: '{{ config('reverb.apps.apps.0.key') }}',
                    cluster: 'mt1',
                    wsHost: '{{ config('reverb.apps.apps.0.options.host') }}',
                    wsPort: {{ config('reverb.apps.apps.0.options.port', 8080) }},
                    wssPort: {{ config('reverb.apps.apps.0.options.port', 8080) }},
                    forceTLS: {{ config('reverb.apps.apps.0.options.scheme') === 'https' ? 'true' : 'false' }},
                    disableStats: true,
                    enabledTransports: ['ws', 'wss'],
                    authEndpoint: '/broadcasting/auth',
                });

                console.log('Echo instance created:', window.Echo);
                console.log('Echo.private available:', typeof window.Echo.private);
                console.log('Echo.channel available:', typeof window.Echo.channel);
            } catch (e) {
                console.error('Echo initialization error:', e);
            }
        </script>

        @livewireScripts

        <!-- Smooth scroll for anchor links -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function(e) {
                        const targetId = this.getAttribute('href');
                        if (targetId === '#') return;
                        const target = document.querySelector(targetId);
                        if (target) {
                            e.preventDefault();
                            target.scrollIntoView({
                                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
                            });
                        }
                    });
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
