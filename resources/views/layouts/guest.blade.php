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
            .gradient-mesh {
                background-color: #0a0a0f;
                background-image:
                    radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.1) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, rgba(34, 211, 238, 0.1) 0px, transparent 50%);
            }
            .glass {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
            .border-gradient {
                background: linear-gradient(#0a0a0f, #0a0a0f) padding-box,
                            linear-gradient(135deg, rgba(99, 102, 241, 0.5), rgba(139, 92, 246, 0.5), rgba(236, 72, 153, 0.3)) border-box;
                border: 1px solid transparent;
            }
            .glow-indigo {
                box-shadow: 0 0 60px -15px rgba(99, 102, 241, 0.3);
            }
        </style>

        @livewireStyles
        @fluxStyles
    </head>
    <body class="font-sans antialiased gradient-mesh text-white min-h-screen">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-3 mb-8">
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl blur opacity-30"></div>
                </div>
                <span class="text-2xl font-bold">Beacon</span>
            </a>

            <!-- Card -->
            <div class="w-full sm:max-w-md glass rounded-2xl border-gradient glow-indigo p-8">
                {{ $slot }}
            </div>

            <!-- Footer Link -->
            <p class="mt-8 text-gray-500 text-sm">
                <a href="/" class="hover:text-white transition-colors">&larr; Back to home</a>
            </p>
        </div>

        @livewireScripts
        @fluxScripts
    </body>
</html>
