<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beacon - Modern Uptime Monitoring for Teams</title>
    <meta name="description" content="Monitor your websites, APIs, and services with real-time alerts. Beautiful status pages, team collaboration, and powerful analytics.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

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

        /* Gradient Mesh Background */
        .gradient-mesh {
            background-color: #0a0a0f;
            background-image:
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(34, 211, 238, 0.1) 0px, transparent 50%);
        }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-light {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Glow effects */
        .glow-indigo {
            box-shadow: 0 0 60px -15px rgba(99, 102, 241, 0.4);
        }

        .glow-emerald {
            box-shadow: 0 0 60px -15px rgba(16, 185, 129, 0.4);
        }

        /* Text gradient */
        .text-gradient {
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 50%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animated border */
        .border-gradient {
            position: relative;
            background: linear-gradient(#0a0a0f, #0a0a0f) padding-box,
                        linear-gradient(135deg, rgba(99, 102, 241, 0.5), rgba(139, 92, 246, 0.5), rgba(236, 72, 153, 0.3)) border-box;
            border: 1px solid transparent;
        }

        /* Smooth scroll */
        html { scroll-behavior: smooth; }

        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }

        /* Pulse ring animation */
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0; }
            50% { opacity: 0.5; }
            100% { transform: scale(1.3); opacity: 0; }
        }
        .animate-pulse-ring { animation: pulse-ring 2s ease-out infinite; }

        /* Grid pattern */
        .grid-pattern {
            background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }
    </style>
</head>
<body class="antialiased font-sans gradient-mesh text-white min-h-screen">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl blur opacity-30"></div>
                    </div>
                    <span class="text-xl font-bold">Beacon</span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#features" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Features</a>
                    <a href="#pricing" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Pricing</a>
                    <a href="#testimonials" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Testimonials</a>
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Sign In</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-white text-gray-900 rounded-full font-semibold text-sm hover:bg-gray-100 transition-all hover:scale-105">
                        Start Free Trial
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-gray-400 hover:text-white">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="mobileOpen" x-cloak x-transition class="lg:hidden pb-4 space-y-2">
                <a href="#features" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-lg">Features</a>
                <a href="#pricing" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-lg">Pricing</a>
                <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-lg">Sign In</a>
                <a href="{{ route('register') }}" class="block px-4 py-3 bg-white text-gray-900 rounded-lg font-semibold text-center mt-4">Start Free Trial</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden grid-pattern">
        <!-- Floating orbs -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-purple-500/20 rounded-full blur-3xl animate-float" style="animation-delay: -3s;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 glass rounded-full mb-8 border border-indigo-500/20">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm text-gray-300">All systems operational</span>
                </div>

                <!-- Headline -->
                <h1 class="text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-black tracking-tight mb-6">
                    <span class="text-gradient">Monitor.</span><br>
                    <span class="text-white">Alert.</span>
                    <span class="text-gray-500">Resolve.</span>
                </h1>

                <!-- Subheadline -->
                <p class="text-xl lg:text-2xl text-gray-400 max-w-3xl mx-auto mb-10 leading-relaxed">
                    Real-time uptime monitoring for modern teams. Know the moment something goes wrong, before your customers do.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                    <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-white text-gray-900 rounded-full font-bold text-lg hover:bg-gray-100 transition-all hover:scale-105 w-full sm:w-auto">
                        <span class="relative z-10">Start Free Trial</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full blur opacity-0 group-hover:opacity-40 transition-opacity"></div>
                    </a>
                    <a href="#demo" class="flex items-center gap-2 px-8 py-4 glass rounded-full font-semibold text-lg hover:bg-white/10 transition-all w-full sm:w-auto justify-center">
                        <svg class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                        </svg>
                        Watch Demo
                    </a>
                </div>

                <!-- Social Proof -->
                <div class="flex flex-col items-center gap-4">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 border-2 border-gray-900"></div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-gray-900"></div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 border-2 border-gray-900"></div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 border-2 border-gray-900"></div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 border-2 border-gray-900 flex items-center justify-center text-xs font-bold">+2K</div>
                    </div>
                    <p class="text-gray-500 text-sm">Trusted by <span class="text-white font-semibold">2,000+</span> teams worldwide</p>
                </div>
            </div>

            <!-- Dashboard Preview -->
            <div class="mt-20 relative">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0f] via-transparent to-transparent z-10 pointer-events-none"></div>
                <div class="relative glass rounded-2xl lg:rounded-3xl p-2 border-gradient glow-indigo">
                    <div class="bg-gray-900/80 rounded-xl lg:rounded-2xl overflow-hidden">
                        <!-- Browser chrome -->
                        <div class="flex items-center gap-2 px-4 py-3 bg-gray-800/50 border-b border-gray-700/50">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                            </div>
                            <div class="flex-1 flex justify-center">
                                <div class="px-4 py-1.5 bg-gray-900/50 rounded-lg text-gray-500 text-sm font-mono">app.beacon.io/dashboard</div>
                            </div>
                        </div>
                        <!-- Dashboard content -->
                        <div class="p-6 lg:p-8 space-y-6">
                            <!-- Stats row -->
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700/30">
                                    <div class="text-3xl font-bold text-emerald-400">99.98%</div>
                                    <div class="text-gray-500 text-sm">Uptime</div>
                                </div>
                                <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700/30">
                                    <div class="text-3xl font-bold text-white">142ms</div>
                                    <div class="text-gray-500 text-sm">Avg Response</div>
                                </div>
                                <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700/30">
                                    <div class="text-3xl font-bold text-white">24</div>
                                    <div class="text-gray-500 text-sm">Monitors</div>
                                </div>
                                <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700/30">
                                    <div class="text-3xl font-bold text-white">0</div>
                                    <div class="text-gray-500 text-sm">Incidents</div>
                                </div>
                            </div>
                            <!-- Chart placeholder -->
                            <div class="bg-gray-800/30 rounded-xl p-6 border border-gray-700/30 h-48 flex items-end gap-1">
                                @for($i = 0; $i < 30; $i++)
                                    <div class="flex-1 bg-gradient-to-t from-indigo-500/50 to-indigo-400/80 rounded-t transition-all hover:from-indigo-500 hover:to-indigo-400" style="height: {{ rand(40, 100) }}%"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Logos Section -->
    <section class="py-20 border-y border-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm font-medium uppercase tracking-wider mb-10">Trusted by engineering teams at</p>
            <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-8 opacity-50">
                <div class="text-2xl font-bold text-gray-400">Stripe</div>
                <div class="text-2xl font-bold text-gray-400">Vercel</div>
                <div class="text-2xl font-bold text-gray-400">Linear</div>
                <div class="text-2xl font-bold text-gray-400">Notion</div>
                <div class="text-2xl font-bold text-gray-400">Figma</div>
                <div class="text-2xl font-bold text-gray-400">GitLab</div>
            </div>
        </div>
    </section>

    <!-- Features Bento Grid -->
    <section id="features" class="py-24 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold mb-4">Everything you need to stay online</h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">Powerful monitoring tools designed for modern teams. Simple to set up, impossible to ignore.</p>
            </div>

            <!-- Bento Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                <!-- Large Feature Card -->
                <div class="md:col-span-2 glass rounded-3xl p-8 lg:p-10 border-gradient group hover:bg-white/5 transition-all duration-500">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-8">
                        <div class="flex-1">
                            <div class="inline-flex items-center px-3 py-1 bg-emerald-500/10 text-emerald-400 rounded-full text-sm font-medium mb-4">
                                <span class="relative flex h-2 w-2 mr-2">
                                    <span class="animate-pulse-ring absolute inline-flex h-full w-full rounded-full bg-emerald-400"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                Real-time
                            </div>
                            <h3 class="text-2xl lg:text-3xl font-bold mb-4">30-Second Monitoring</h3>
                            <p class="text-gray-400 text-lg leading-relaxed">Know the instant something goes wrong. Our global network checks your services every 30 seconds from multiple locations worldwide.</p>
                        </div>
                        <div class="flex-shrink-0 w-full lg:w-72 h-48 bg-gray-800/50 rounded-2xl flex items-center justify-center overflow-hidden">
                            <div class="relative">
                                <div class="w-20 h-20 rounded-full border-4 border-emerald-500/20 flex items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center animate-pulse">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="absolute inset-0 rounded-full border-4 border-emerald-500/40 animate-pulse-ring"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Small Feature Card -->
                <div class="glass rounded-3xl p-8 border-gradient group hover:bg-white/5 transition-all duration-500">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Instant Alerts</h3>
                    <p class="text-gray-400">Get notified via Slack, Discord, email, SMS, or webhooks. Never miss a critical incident again.</p>
                </div>

                <!-- Feature Card -->
                <div class="glass rounded-3xl p-8 border-gradient group hover:bg-white/5 transition-all duration-500">
                    <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Beautiful Status Pages</h3>
                    <p class="text-gray-400">Custom branded status pages that keep your customers informed during incidents.</p>
                </div>

                <!-- Feature Card -->
                <div class="glass rounded-3xl p-8 border-gradient group hover:bg-white/5 transition-all duration-500">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Team Collaboration</h3>
                    <p class="text-gray-400">Invite your team with role-based permissions. Everyone stays in the loop.</p>
                </div>

                <!-- Large Feature Card -->
                <div class="md:col-span-2 glass rounded-3xl p-8 lg:p-10 border-gradient group hover:bg-white/5 transition-all duration-500">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-8">
                        <div class="flex-shrink-0 w-full lg:w-72 h-48 bg-gray-800/50 rounded-2xl p-6">
                            <!-- Mini chart -->
                            <div class="text-xs text-gray-500 mb-2">Response Time (ms)</div>
                            <div class="h-32 flex items-end gap-1">
                                @for($i = 0; $i < 20; $i++)
                                    <div class="flex-1 bg-gradient-to-t from-cyan-500/50 to-cyan-400/80 rounded-t" style="height: {{ rand(20, 100) }}%"></div>
                                @endfor
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="inline-flex items-center px-3 py-1 bg-cyan-500/10 text-cyan-400 rounded-full text-sm font-medium mb-4">
                                Analytics
                            </div>
                            <h3 class="text-2xl lg:text-3xl font-bold mb-4">Detailed Performance Metrics</h3>
                            <p class="text-gray-400 text-lg leading-relaxed">Track response times, SSL certificates, DNS resolution, and more. Historical data up to 365 days on premium plans.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 lg:py-32 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-indigo-500/5 to-transparent"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold mb-4">Simple, transparent pricing</h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">Start free and scale as you grow. No hidden fees, no surprises.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Free Plan -->
                <div class="glass rounded-3xl p-8 border-gradient hover:bg-white/5 transition-all duration-500">
                    <div class="text-gray-400 font-medium mb-2">Free</div>
                    <div class="text-4xl font-bold mb-1">&euro;0</div>
                    <div class="text-gray-500 text-sm mb-6">Forever free</div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            5 Monitors
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            5-minute checks
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Email alerts
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            7-day history
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center glass rounded-xl font-semibold hover:bg-white/10 transition-all">
                        Get Started
                    </a>
                </div>

                <!-- Pro Plan (Featured) -->
                <div class="relative glass rounded-3xl p-8 border-2 border-indigo-500/50 glow-indigo hover:bg-white/5 transition-all duration-500 lg:scale-105">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full text-sm font-semibold">
                        Most Popular
                    </div>
                    <div class="text-indigo-400 font-medium mb-2">Pro</div>
                    <div class="text-4xl font-bold mb-1">&euro;29</div>
                    <div class="text-gray-500 text-sm mb-6">per month</div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            50 Monitors
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            30-second checks
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            All alert channels
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            90-day history
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Status pages
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            API access
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl font-semibold hover:opacity-90 transition-all">
                        Start Free Trial
                    </a>
                </div>

                <!-- Business Plan -->
                <div class="glass rounded-3xl p-8 border-gradient hover:bg-white/5 transition-all duration-500">
                    <div class="text-gray-400 font-medium mb-2">Business</div>
                    <div class="text-4xl font-bold mb-1">&euro;99</div>
                    <div class="text-gray-500 text-sm mb-6">per month</div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Unlimited monitors
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            30-second checks
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            365-day history
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Priority support
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Custom domain
                        </li>
                        <li class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            SSO & SAML
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center glass rounded-xl font-semibold hover:bg-white/10 transition-all">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-24 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold mb-4">Loved by developers</h2>
                <p class="text-xl text-gray-400">See what our customers have to say</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Testimonial 1 -->
                <div class="glass rounded-2xl p-6 border-gradient hover:bg-white/5 transition-all">
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-6">"Beacon caught a critical API outage at 3 AM before any customer complaints. The Slack integration is seamless and the response time graphs helped us identify a slow database query."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-600"></div>
                        <div>
                            <div class="font-semibold">Sarah Chen</div>
                            <div class="text-gray-500 text-sm">CTO at TechCorp</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="glass rounded-2xl p-6 border-gradient hover:bg-white/5 transition-all">
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-6">"Switched from a competitor and the difference is night and day. Setup took 5 minutes, the UI is beautiful, and the pricing is fair. Finally, monitoring that just works."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-cyan-600"></div>
                        <div>
                            <div class="font-semibold">Marcus Weber</div>
                            <div class="text-gray-500 text-sm">DevOps Lead at StartupXYZ</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="glass rounded-2xl p-6 border-gradient hover:bg-white/5 transition-all">
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-6">"The status page feature alone is worth the subscription. Our customers love the transparency and we've reduced support tickets by 40% during maintenance windows."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-400 to-rose-600"></div>
                        <div>
                            <div class="font-semibold">Lisa Johnson</div>
                            <div class="text-gray-500 text-sm">Engineering Manager at CloudCo</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 lg:py-32">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass rounded-3xl p-8 lg:p-16 border-gradient text-center relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>

                <div class="relative">
                    <h2 class="text-4xl lg:text-5xl font-bold mb-6">Ready to never miss another outage?</h2>
                    <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto">Join thousands of teams who trust Beacon to keep their services running. Start your 14-day free trial today.</p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-white text-gray-900 rounded-full font-bold text-lg hover:bg-gray-100 transition-all hover:scale-105 w-full sm:w-auto">
                            Start Free Trial
                        </a>
                        <a href="#pricing" class="px-8 py-4 glass rounded-full font-semibold text-lg hover:bg-white/10 transition-all w-full sm:w-auto">
                            View Pricing
                        </a>
                    </div>

                    <p class="mt-6 text-gray-500 text-sm">No credit card required. 14-day free trial.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-800/50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 mb-12">
                <!-- Brand -->
                <div class="col-span-2 lg:col-span-1">
                    <a href="/" class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold">Beacon</span>
                    </a>
                    <p class="text-gray-500 text-sm">Modern uptime monitoring for teams who care about reliability.</p>
                </div>

                <!-- Product -->
                <div>
                    <h4 class="font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Integrations</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h4 class="font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Privacy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Security</a></li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between pt-8 border-t border-gray-800/50">
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} Beacon. All rights reserved.</p>
                <div class="flex items-center gap-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Alpine.js for mobile menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
