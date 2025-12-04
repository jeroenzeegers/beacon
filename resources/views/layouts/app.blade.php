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
            /* === 2026 Design System - Liquid Glass Era === */
            :root {
                /* Deep Space Color Palette */
                --bg-void: #050508;
                --bg-primary: #08080c;
                --bg-secondary: #0c0c12;
                --bg-elevated: #101018;
                --bg-surface: #141420;

                /* Liquid Glass Colors - Translucent & Fluid */
                --liquid-primary: rgba(139, 92, 246, 0.85);
                --liquid-secondary: rgba(99, 102, 241, 0.75);
                --liquid-accent: rgba(236, 72, 153, 0.7);
                --liquid-cyan: rgba(34, 211, 238, 0.75);
                --liquid-emerald: rgba(16, 185, 129, 0.75);

                /* 3D Depth Shadows */
                --shadow-3d-sm: 0 2px 8px -2px rgba(0,0,0,0.5), 0 4px 16px -4px rgba(0,0,0,0.3);
                --shadow-3d-md: 0 4px 16px -4px rgba(0,0,0,0.6), 0 8px 32px -8px rgba(0,0,0,0.4);
                --shadow-3d-lg: 0 8px 32px -8px rgba(0,0,0,0.7), 0 16px 64px -16px rgba(0,0,0,0.5);
                --shadow-3d-xl: 0 16px 48px -12px rgba(0,0,0,0.8), 0 32px 96px -24px rgba(0,0,0,0.6);

                /* Ambient Glow - More Pronounced */
                --glow-violet: rgba(139, 92, 246, 0.25);
                --glow-indigo: rgba(99, 102, 241, 0.2);
                --glow-success: rgba(16, 185, 129, 0.25);
                --glow-danger: rgba(239, 68, 68, 0.25);
                --glow-warning: rgba(245, 158, 11, 0.25);
                --glow-cyan: rgba(34, 211, 238, 0.2);

                /* Liquid Glass Properties */
                --glass-blur: 32px;
                --glass-saturation: 180%;
                --glass-brightness: 1.05;

                /* 2026 Motion - Fluid & Natural */
                --ease-liquid: cubic-bezier(0.4, 0, 0.2, 1);
                --ease-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
                --ease-elastic: cubic-bezier(0.68, -0.55, 0.265, 1.55);
                --ease-smooth: cubic-bezier(0.25, 0.1, 0.25, 1);

                --duration-instant: 100ms;
                --duration-fast: 200ms;
                --duration-base: 350ms;
                --duration-slow: 500ms;
                --duration-slower: 700ms;

                /* Kinetic Typography */
                --font-weight-light: 300;
                --font-weight-normal: 400;
                --font-weight-medium: 500;
                --font-weight-semibold: 600;
                --font-weight-bold: 700;
                --font-weight-black: 900;
            }

            [x-cloak] { display: none !important; }

            /* === Immersive Background - Living Gradient Mesh === */
            .gradient-mesh {
                background-color: var(--bg-void);
                background-image:
                    radial-gradient(ellipse 100% 80% at 10% -30%, rgba(139, 92, 246, 0.18) 0%, transparent 55%),
                    radial-gradient(ellipse 80% 60% at 90% -10%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                    radial-gradient(ellipse 70% 50% at 100% 70%, rgba(236, 72, 153, 0.1) 0%, transparent 45%),
                    radial-gradient(ellipse 60% 60% at 0% 100%, rgba(34, 211, 238, 0.08) 0%, transparent 40%),
                    radial-gradient(ellipse 50% 50% at 50% 50%, rgba(139, 92, 246, 0.05) 0%, transparent 60%),
                    radial-gradient(ellipse 120% 100% at 50% 100%, rgba(16, 185, 129, 0.04) 0%, transparent 50%);
                background-attachment: fixed;
                min-height: 100vh;
            }

            /* === LIQUID GLASS - 2026 Signature Effect === */
            .glass {
                --glass-opacity: 0.04;
                background: linear-gradient(
                    135deg,
                    rgba(255, 255, 255, var(--glass-opacity)) 0%,
                    rgba(255, 255, 255, calc(var(--glass-opacity) * 0.5)) 50%,
                    rgba(255, 255, 255, calc(var(--glass-opacity) * 0.25)) 100%
                );
                backdrop-filter: blur(var(--glass-blur)) saturate(var(--glass-saturation)) brightness(var(--glass-brightness));
                -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(var(--glass-saturation)) brightness(var(--glass-brightness));
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 20px;
                box-shadow:
                    var(--shadow-3d-md),
                    inset 0 1px 1px rgba(255, 255, 255, 0.1),
                    inset 0 -1px 1px rgba(0, 0, 0, 0.1);
                transition:
                    transform var(--duration-base) var(--ease-liquid),
                    box-shadow var(--duration-base) var(--ease-liquid),
                    background var(--duration-base) var(--ease-liquid),
                    border-color var(--duration-base) var(--ease-liquid);
                position: relative;
                overflow: hidden;
            }

            /* Liquid Glass Shimmer Effect */
            .glass::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    105deg,
                    transparent 40%,
                    rgba(255, 255, 255, 0.03) 45%,
                    rgba(255, 255, 255, 0.05) 50%,
                    rgba(255, 255, 255, 0.03) 55%,
                    transparent 60%
                );
                transform: translateX(-100%);
                transition: transform var(--duration-slower) var(--ease-liquid);
                pointer-events: none;
            }

            .glass:hover {
                --glass-opacity: 0.06;
                transform: translateY(-4px) scale(1.005);
                border-color: rgba(255, 255, 255, 0.15);
                box-shadow:
                    var(--shadow-3d-lg),
                    0 0 40px rgba(139, 92, 246, 0.1),
                    inset 0 1px 2px rgba(255, 255, 255, 0.15),
                    inset 0 -1px 1px rgba(0, 0, 0, 0.1);
            }

            .glass:hover::before {
                transform: translateX(100%);
            }

            /* Liquid Glass Darker Variant */
            .glass-darker {
                background: linear-gradient(
                    135deg,
                    rgba(0, 0, 0, 0.5) 0%,
                    rgba(0, 0, 0, 0.4) 50%,
                    rgba(0, 0, 0, 0.35) 100%
                );
                backdrop-filter: blur(var(--glass-blur)) saturate(200%) brightness(0.95);
                -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(200%) brightness(0.95);
                border: 1px solid rgba(255, 255, 255, 0.05);
                box-shadow: var(--shadow-3d-lg);
            }

            /* Liquid Glass Frosted Variant */
            .glass-frosted {
                background: linear-gradient(
                    135deg,
                    rgba(255, 255, 255, 0.08) 0%,
                    rgba(255, 255, 255, 0.04) 100%
                );
                backdrop-filter: blur(40px) saturate(150%);
                -webkit-backdrop-filter: blur(40px) saturate(150%);
                border: 1px solid rgba(255, 255, 255, 0.12);
            }

            /* === 3D DEPTH CARDS === */
            .card-3d {
                transform-style: preserve-3d;
                perspective: 1000px;
                transition: transform var(--duration-base) var(--ease-liquid);
            }

            .card-3d:hover {
                transform: rotateX(2deg) rotateY(-2deg) translateZ(10px);
            }

            /* Neomorphism - Soft 3D */
            .neo {
                background: linear-gradient(145deg, var(--bg-elevated), var(--bg-primary));
                box-shadow:
                    8px 8px 24px rgba(0, 0, 0, 0.4),
                    -4px -4px 16px rgba(255, 255, 255, 0.02);
                border: none;
                border-radius: 16px;
            }

            .neo-inset {
                background: var(--bg-primary);
                box-shadow:
                    inset 4px 4px 12px rgba(0, 0, 0, 0.4),
                    inset -2px -2px 8px rgba(255, 255, 255, 0.02);
            }

            /* === KINETIC TYPOGRAPHY === */
            .text-gradient {
                background: linear-gradient(
                    135deg,
                    #a78bfa 0%,
                    #818cf8 25%,
                    #c084fc 50%,
                    #a78bfa 75%,
                    #818cf8 100%
                );
                background-size: 200% auto;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: gradient-shift 8s linear infinite;
            }

            .text-gradient-cyan {
                background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 50%, #67e8f9 100%);
                background-size: 200% auto;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: gradient-shift 6s linear infinite;
            }

            .text-gradient-emerald {
                background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #6ee7b7 100%);
                background-size: 200% auto;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            @keyframes gradient-shift {
                0% { background-position: 0% center; }
                100% { background-position: 200% center; }
            }

            /* Kinetic Text - Hover Weight Change */
            .text-kinetic {
                transition: font-weight var(--duration-base) var(--ease-liquid);
            }

            .text-kinetic:hover {
                font-weight: var(--font-weight-bold);
            }

            /* === 2026 MICRO-INTERACTIONS === */
            @keyframes liquid-float {
                0%, 100% {
                    transform: translateY(0) rotate(0deg);
                }
                25% {
                    transform: translateY(-6px) rotate(0.5deg);
                }
                75% {
                    transform: translateY(-3px) rotate(-0.5deg);
                }
            }

            @keyframes pulse-glow {
                0%, 100% {
                    box-shadow: 0 0 20px var(--glow-violet), 0 0 40px rgba(139, 92, 246, 0.1);
                }
                50% {
                    box-shadow: 0 0 40px var(--glow-violet), 0 0 80px rgba(139, 92, 246, 0.2);
                }
            }

            @keyframes shimmer {
                0% { background-position: -200% center; }
                100% { background-position: 200% center; }
            }

            @keyframes ripple {
                0% {
                    transform: scale(0);
                    opacity: 0.5;
                }
                100% {
                    transform: scale(4);
                    opacity: 0;
                }
            }

            @keyframes breathe {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.02); opacity: 0.9; }
            }

            .animate-liquid-float {
                animation: liquid-float 6s var(--ease-smooth) infinite;
            }

            .animate-pulse-glow {
                animation: pulse-glow 4s var(--ease-smooth) infinite;
            }

            .animate-shimmer {
                background: linear-gradient(
                    90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.05) 50%,
                    transparent 100%
                );
                background-size: 200% 100%;
                animation: shimmer 3s infinite;
            }

            .animate-breathe {
                animation: breathe 4s var(--ease-smooth) infinite;
            }

            /* === INTERACTIVE ELEMENTS === */
            .interactive-card {
                transition:
                    transform var(--duration-base) var(--ease-bounce),
                    box-shadow var(--duration-base) var(--ease-liquid);
                cursor: pointer;
            }

            .interactive-card:hover {
                transform: translateY(-6px) scale(1.01);
            }

            .interactive-card:active {
                transform: translateY(-2px) scale(0.99);
                transition: transform var(--duration-instant) var(--ease-liquid);
            }

            /* Button with Liquid Effect */
            .btn-liquid {
                position: relative;
                overflow: hidden;
                background: linear-gradient(135deg, var(--liquid-primary), var(--liquid-secondary));
                border: none;
                border-radius: 12px;
                padding: 12px 24px;
                color: white;
                font-weight: var(--font-weight-semibold);
                transition: all var(--duration-base) var(--ease-liquid);
                box-shadow: var(--shadow-3d-sm), 0 0 20px rgba(139, 92, 246, 0.2);
            }

            .btn-liquid::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                transform: translate(-50%, -50%);
                transition: width var(--duration-slow) var(--ease-liquid), height var(--duration-slow) var(--ease-liquid);
            }

            .btn-liquid:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-3d-md), 0 0 40px rgba(139, 92, 246, 0.3);
            }

            .btn-liquid:hover::before {
                width: 300px;
                height: 300px;
            }

            .btn-liquid:active {
                transform: translateY(0) scale(0.98);
            }

            /* Glow Button */
            .btn-glow {
                position: relative;
                overflow: hidden;
                transition: all var(--duration-base) var(--ease-liquid);
            }

            .btn-glow::after {
                content: '';
                position: absolute;
                inset: -2px;
                background: linear-gradient(135deg, var(--liquid-primary), var(--liquid-cyan), var(--liquid-accent));
                border-radius: inherit;
                z-index: -1;
                opacity: 0;
                filter: blur(12px);
                transition: opacity var(--duration-base) var(--ease-liquid);
            }

            .btn-glow:hover::after {
                opacity: 0.6;
            }

            /* === STATUS INDICATORS WITH 3D GLOW === */
            .status-online {
                box-shadow:
                    0 0 8px rgba(16, 185, 129, 0.6),
                    0 0 20px rgba(16, 185, 129, 0.3),
                    0 0 40px rgba(16, 185, 129, 0.1);
            }

            .status-offline {
                box-shadow:
                    0 0 8px rgba(239, 68, 68, 0.6),
                    0 0 20px rgba(239, 68, 68, 0.3),
                    0 0 40px rgba(239, 68, 68, 0.1);
            }

            .status-degraded {
                box-shadow:
                    0 0 8px rgba(245, 158, 11, 0.6),
                    0 0 20px rgba(245, 158, 11, 0.3),
                    0 0 40px rgba(245, 158, 11, 0.1);
            }

            /* === BENTO GRID - 2026 Spatial Layout === */
            .bento-grid {
                display: grid;
                gap: 1.25rem;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            }

            .bento-item {
                transition: all var(--duration-base) var(--ease-bounce);
            }

            .bento-item:hover {
                z-index: 10;
            }

            .bento-span-2 {
                grid-column: span 2;
            }

            .bento-span-row {
                grid-row: span 2;
            }

            /* === IMMERSIVE SCROLLBAR === */
            ::-webkit-scrollbar {
                width: 10px;
                height: 10px;
            }

            ::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.02);
                border-radius: 5px;
            }

            ::-webkit-scrollbar-thumb {
                background: linear-gradient(180deg, rgba(139, 92, 246, 0.3), rgba(99, 102, 241, 0.2));
                border-radius: 5px;
                border: 2px solid transparent;
                background-clip: padding-box;
                transition: background var(--duration-base) var(--ease-liquid);
            }

            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(180deg, rgba(139, 92, 246, 0.5), rgba(99, 102, 241, 0.4));
            }

            /* === FOCUS STATES - Accessible & Beautiful === */
            *:focus-visible {
                outline: 2px solid var(--liquid-primary);
                outline-offset: 3px;
                border-radius: 4px;
                transition: outline-offset var(--duration-fast) var(--ease-liquid);
            }

            /* === LOADING SKELETON === */
            .skeleton {
                background: linear-gradient(
                    90deg,
                    rgba(255, 255, 255, 0.02) 0%,
                    rgba(255, 255, 255, 0.05) 50%,
                    rgba(255, 255, 255, 0.02) 100%
                );
                background-size: 200% 100%;
                animation: shimmer 1.5s infinite;
                border-radius: 8px;
            }

            /* === TOOLTIP === */
            .tooltip {
                position: relative;
            }

            .tooltip::after {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%) translateY(-8px);
                padding: 8px 12px;
                background: var(--bg-surface);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                font-size: 12px;
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: all var(--duration-fast) var(--ease-liquid);
                box-shadow: var(--shadow-3d-sm);
            }

            .tooltip:hover::after {
                opacity: 1;
                visibility: visible;
                transform: translateX(-50%) translateY(-4px);
            }

            /* === 2026 SCROLLYTELLING - Scroll-Based Animations === */
            @keyframes scroll-fade-up {
                from {
                    opacity: 0;
                    transform: translateY(60px) scale(0.95);
                    filter: blur(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                    filter: blur(0);
                }
            }

            @keyframes scroll-fade-left {
                from {
                    opacity: 0;
                    transform: translateX(-60px);
                    filter: blur(8px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                    filter: blur(0);
                }
            }

            @keyframes scroll-fade-right {
                from {
                    opacity: 0;
                    transform: translateX(60px);
                    filter: blur(8px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                    filter: blur(0);
                }
            }

            @keyframes scroll-scale-in {
                from {
                    opacity: 0;
                    transform: scale(0.8);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            @keyframes scroll-rotate-in {
                from {
                    opacity: 0;
                    transform: rotate(-5deg) scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: rotate(0) scale(1);
                }
            }

            /* Scroll-triggered animation classes */
            .scroll-reveal {
                animation: scroll-fade-up var(--duration-slow) var(--ease-liquid) forwards;
                animation-timeline: view();
                animation-range: entry 0% cover 40%;
            }

            .scroll-reveal-left {
                animation: scroll-fade-left var(--duration-slow) var(--ease-liquid) forwards;
                animation-timeline: view();
                animation-range: entry 0% cover 40%;
            }

            .scroll-reveal-right {
                animation: scroll-fade-right var(--duration-slow) var(--ease-liquid) forwards;
                animation-timeline: view();
                animation-range: entry 0% cover 40%;
            }

            .scroll-scale {
                animation: scroll-scale-in var(--duration-slow) var(--ease-bounce) forwards;
                animation-timeline: view();
                animation-range: entry 0% cover 35%;
            }

            /* Parallax scroll effect */
            .scroll-parallax {
                animation: parallax-shift linear forwards;
                animation-timeline: scroll();
            }

            @keyframes parallax-shift {
                from { transform: translateY(0); }
                to { transform: translateY(-100px); }
            }

            /* === 2026 VARIABLE FONT TRANSITIONS === */
            @supports (font-variation-settings: normal) {
                .text-fluid {
                    font-variation-settings: 'wght' 400;
                    transition: font-variation-settings var(--duration-base) var(--ease-liquid);
                }

                .text-fluid:hover {
                    font-variation-settings: 'wght' 700;
                }

                .text-fluid-light:hover {
                    font-variation-settings: 'wght' 300;
                }

                .text-fluid-black:hover {
                    font-variation-settings: 'wght' 900;
                }
            }

            /* Text morph on scroll */
            .text-morph {
                --morph-progress: 0;
                font-weight: calc(400 + (var(--morph-progress) * 400));
                letter-spacing: calc(0em + (var(--morph-progress) * 0.05em));
                transition: all var(--duration-base) var(--ease-liquid);
            }

            /* === 2026 CURSOR-TRACKING 3D TILT === */
            .tilt-3d {
                transform-style: preserve-3d;
                perspective: 1000px;
                transition: transform var(--duration-fast) var(--ease-liquid);
            }

            .tilt-3d-inner {
                transform: translateZ(30px);
                transition: transform var(--duration-fast) var(--ease-liquid);
            }

            /* === 2026 MAGNETIC BUTTONS === */
            .btn-magnetic {
                position: relative;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: transform var(--duration-fast) var(--ease-elastic);
                will-change: transform;
            }

            .btn-magnetic-inner {
                transition: transform var(--duration-fast) var(--ease-elastic);
                will-change: transform;
            }

            /* Magnetic hover area */
            .btn-magnetic::before {
                content: '';
                position: absolute;
                inset: -20px;
                border-radius: inherit;
                pointer-events: none;
            }

            /* === 2026 STAGGERED ANIMATIONS === */
            .stagger-list > * {
                opacity: 0;
                animation: scroll-fade-up var(--duration-base) var(--ease-bounce) forwards;
            }

            .stagger-list > *:nth-child(1) { animation-delay: 0ms; }
            .stagger-list > *:nth-child(2) { animation-delay: 50ms; }
            .stagger-list > *:nth-child(3) { animation-delay: 100ms; }
            .stagger-list > *:nth-child(4) { animation-delay: 150ms; }
            .stagger-list > *:nth-child(5) { animation-delay: 200ms; }
            .stagger-list > *:nth-child(6) { animation-delay: 250ms; }
            .stagger-list > *:nth-child(7) { animation-delay: 300ms; }
            .stagger-list > *:nth-child(8) { animation-delay: 350ms; }
            .stagger-list > *:nth-child(9) { animation-delay: 400ms; }
            .stagger-list > *:nth-child(10) { animation-delay: 450ms; }
            .stagger-list > *:nth-child(n+11) { animation-delay: 500ms; }

            /* Stagger on scroll reveal */
            .stagger-reveal > * {
                animation: scroll-fade-up var(--duration-base) var(--ease-bounce) forwards;
                animation-timeline: view();
                animation-range: entry 0% cover 40%;
            }

            .stagger-reveal > *:nth-child(1) { animation-range: entry 0% cover 35%; }
            .stagger-reveal > *:nth-child(2) { animation-range: entry 5% cover 40%; }
            .stagger-reveal > *:nth-child(3) { animation-range: entry 10% cover 45%; }
            .stagger-reveal > *:nth-child(4) { animation-range: entry 15% cover 50%; }
            .stagger-reveal > *:nth-child(5) { animation-range: entry 20% cover 55%; }
            .stagger-reveal > *:nth-child(6) { animation-range: entry 25% cover 60%; }

            /* === 2026 TEXT REVEAL ANIMATIONS === */
            .text-reveal {
                overflow: hidden;
            }

            .text-reveal-inner {
                display: inline-block;
                animation: text-slide-up var(--duration-slow) var(--ease-bounce) forwards;
                animation-timeline: view();
                animation-range: entry 0% cover 30%;
            }

            @keyframes text-slide-up {
                from {
                    transform: translateY(100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            /* Split text character animation */
            .text-split-reveal {
                display: inline-flex;
                overflow: hidden;
            }

            .text-split-reveal span {
                display: inline-block;
                animation: char-reveal var(--duration-base) var(--ease-bounce) forwards;
                opacity: 0;
                transform: translateY(100%) rotateX(-90deg);
            }

            @keyframes char-reveal {
                to {
                    opacity: 1;
                    transform: translateY(0) rotateX(0deg);
                }
            }

            /* Typewriter effect */
            .text-typewriter {
                overflow: hidden;
                border-right: 2px solid var(--liquid-primary);
                white-space: nowrap;
                animation:
                    typewriter 3s steps(40) forwards,
                    blink-caret 0.75s step-end infinite;
            }

            @keyframes typewriter {
                from { width: 0; }
                to { width: 100%; }
            }

            @keyframes blink-caret {
                from, to { border-color: transparent; }
                50% { border-color: var(--liquid-primary); }
            }

            /* === 2026 ENHANCED PARALLAX DEPTH === */
            .parallax-container {
                perspective: 1px;
                overflow-x: hidden;
                overflow-y: auto;
                height: 100vh;
            }

            .parallax-layer-back {
                transform: translateZ(-2px) scale(3);
            }

            .parallax-layer-mid {
                transform: translateZ(-1px) scale(2);
            }

            .parallax-layer-front {
                transform: translateZ(0);
            }

            /* Floating elements with depth */
            .float-depth-1 {
                animation: float-depth-1 8s var(--ease-smooth) infinite;
            }

            .float-depth-2 {
                animation: float-depth-2 10s var(--ease-smooth) infinite;
            }

            .float-depth-3 {
                animation: float-depth-3 12s var(--ease-smooth) infinite;
            }

            @keyframes float-depth-1 {
                0%, 100% { transform: translateY(0) translateX(0) rotate(0deg); }
                25% { transform: translateY(-15px) translateX(10px) rotate(2deg); }
                50% { transform: translateY(-25px) translateX(-5px) rotate(-1deg); }
                75% { transform: translateY(-10px) translateX(-10px) rotate(1deg); }
            }

            @keyframes float-depth-2 {
                0%, 100% { transform: translateY(0) translateX(0) scale(1); }
                33% { transform: translateY(-20px) translateX(15px) scale(1.02); }
                66% { transform: translateY(-10px) translateX(-10px) scale(0.98); }
            }

            @keyframes float-depth-3 {
                0%, 100% { transform: translateY(0) rotate(0deg); }
                50% { transform: translateY(-30px) rotate(3deg); }
            }

            /* === 2026 ENHANCED GLASS MORPHISM === */
            .glass-liquid {
                --glass-opacity: 0.03;
                background: linear-gradient(
                    135deg,
                    rgba(255, 255, 255, var(--glass-opacity)) 0%,
                    rgba(255, 255, 255, calc(var(--glass-opacity) * 0.3)) 100%
                );
                backdrop-filter: blur(40px) saturate(200%) brightness(1.1);
                -webkit-backdrop-filter: blur(40px) saturate(200%) brightness(1.1);
                border: 1px solid rgba(255, 255, 255, 0.06);
                border-radius: 24px;
                box-shadow:
                    var(--shadow-3d-lg),
                    inset 0 1px 1px rgba(255, 255, 255, 0.1),
                    inset 0 -1px 1px rgba(0, 0, 0, 0.05),
                    0 0 60px rgba(139, 92, 246, 0.05);
                transition: all var(--duration-base) var(--ease-liquid);
                position: relative;
                overflow: hidden;
            }

            .glass-liquid::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(
                    ellipse 80% 50% at var(--mouse-x, 50%) var(--mouse-y, 50%),
                    rgba(139, 92, 246, 0.1) 0%,
                    transparent 50%
                );
                opacity: 0;
                transition: opacity var(--duration-base) var(--ease-liquid);
                pointer-events: none;
            }

            .glass-liquid:hover::before {
                opacity: 1;
            }

            .glass-liquid:hover {
                --glass-opacity: 0.05;
                border-color: rgba(255, 255, 255, 0.12);
                box-shadow:
                    var(--shadow-3d-xl),
                    inset 0 1px 2px rgba(255, 255, 255, 0.15),
                    0 0 80px rgba(139, 92, 246, 0.1);
                transform: translateY(-2px);
            }

            /* === 2026 AMBIENT CURSOR GLOW === */
            .cursor-glow {
                position: fixed;
                width: 400px;
                height: 400px;
                border-radius: 50%;
                background: radial-gradient(
                    circle,
                    rgba(139, 92, 246, 0.08) 0%,
                    rgba(99, 102, 241, 0.04) 30%,
                    transparent 70%
                );
                pointer-events: none;
                z-index: 9999;
                transform: translate(-50%, -50%);
                transition: opacity var(--duration-fast) var(--ease-liquid);
                mix-blend-mode: screen;
            }

            /* === 2026 INTERACTIVE HOVER STATES === */
            .hover-lift {
                transition: all var(--duration-base) var(--ease-bounce);
            }

            .hover-lift:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: var(--shadow-3d-xl), 0 0 60px rgba(139, 92, 246, 0.15);
            }

            .hover-glow {
                transition: all var(--duration-base) var(--ease-liquid);
            }

            .hover-glow:hover {
                box-shadow:
                    0 0 20px rgba(139, 92, 246, 0.4),
                    0 0 40px rgba(139, 92, 246, 0.2),
                    0 0 60px rgba(139, 92, 246, 0.1);
            }

            .hover-border-glow {
                position: relative;
            }

            .hover-border-glow::after {
                content: '';
                position: absolute;
                inset: -2px;
                background: linear-gradient(135deg, var(--liquid-primary), var(--liquid-cyan), var(--liquid-accent), var(--liquid-primary));
                background-size: 300% 300%;
                border-radius: inherit;
                z-index: -1;
                opacity: 0;
                transition: opacity var(--duration-base) var(--ease-liquid);
                animation: gradient-rotate 4s linear infinite paused;
            }

            .hover-border-glow:hover::after {
                opacity: 0.6;
                animation-play-state: running;
            }

            @keyframes gradient-rotate {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            /* === 2026 FORM INPUT ENHANCEMENTS === */
            .input-liquid {
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 12px;
                padding: 12px 16px;
                color: white;
                transition: all var(--duration-base) var(--ease-liquid);
                position: relative;
            }

            .input-liquid:focus {
                background: rgba(255, 255, 255, 0.05);
                border-color: var(--liquid-primary);
                box-shadow:
                    0 0 0 3px rgba(139, 92, 246, 0.1),
                    0 0 20px rgba(139, 92, 246, 0.1);
                outline: none;
            }

            .input-liquid::placeholder {
                color: rgba(255, 255, 255, 0.3);
                transition: all var(--duration-base) var(--ease-liquid);
            }

            .input-liquid:focus::placeholder {
                color: rgba(255, 255, 255, 0.5);
                transform: translateX(4px);
            }

            /* === 2026 CARD HOVER EFFECTS === */
            .card-hover-shine {
                position: relative;
                overflow: hidden;
            }

            .card-hover-shine::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(
                    45deg,
                    transparent 40%,
                    rgba(255, 255, 255, 0.03) 45%,
                    rgba(255, 255, 255, 0.05) 50%,
                    rgba(255, 255, 255, 0.03) 55%,
                    transparent 60%
                );
                transform: rotate(45deg) translateY(-100%);
                transition: transform var(--duration-slower) var(--ease-liquid);
                pointer-events: none;
            }

            .card-hover-shine:hover::before {
                transform: rotate(45deg) translateY(100%);
            }

            /* === 2026 RIPPLE EFFECT === */
            .ripple-effect {
                position: relative;
                overflow: hidden;
            }

            .ripple-effect .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }

            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }

            /* === 2026 PROGRESS INDICATORS === */
            .progress-liquid {
                height: 6px;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 3px;
                overflow: hidden;
                position: relative;
            }

            .progress-liquid-bar {
                height: 100%;
                background: linear-gradient(90deg, var(--liquid-primary), var(--liquid-cyan));
                border-radius: 3px;
                transition: width var(--duration-slow) var(--ease-bounce);
                position: relative;
            }

            .progress-liquid-bar::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.3) 50%,
                    transparent 100%
                );
                animation: shimmer 2s infinite;
            }

            /* === 2026 NOTIFICATION DOT === */
            .notification-dot {
                position: relative;
            }

            .notification-dot::after {
                content: '';
                position: absolute;
                top: -2px;
                right: -2px;
                width: 10px;
                height: 10px;
                background: var(--liquid-accent);
                border-radius: 50%;
                border: 2px solid var(--bg-primary);
                animation: pulse-dot 2s var(--ease-smooth) infinite;
            }

            @keyframes pulse-dot {
                0%, 100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(236, 72, 153, 0.4);
                }
                50% {
                    transform: scale(1.1);
                    box-shadow: 0 0 0 8px rgba(236, 72, 153, 0);
                }
            }

            /* === 2026 ACCESSIBILITY ENHANCEMENTS === */
            /* High contrast mode support */
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

            /* Focus-visible for keyboard navigation */
            .focus-ring:focus-visible {
                outline: 3px solid var(--liquid-primary);
                outline-offset: 4px;
                border-radius: 8px;
            }

            /* Skip to content link */
            .skip-link {
                position: absolute;
                top: -100px;
                left: 50%;
                transform: translateX(-50%);
                background: var(--liquid-primary);
                color: white;
                padding: 12px 24px;
                border-radius: 0 0 12px 12px;
                z-index: 10000;
                transition: top var(--duration-fast) var(--ease-liquid);
            }

            .skip-link:focus {
                top: 0;
            }

            /* === REDUCED MOTION === */
            @media (prefers-reduced-motion: reduce) {
                *,
                *::before,
                *::after {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                    scroll-behavior: auto !important;
                }

                .scroll-reveal,
                .scroll-reveal-left,
                .scroll-reveal-right,
                .scroll-scale,
                .stagger-list > *,
                .stagger-reveal > * {
                    opacity: 1;
                    transform: none;
                    animation: none;
                }

                .parallax-layer-back,
                .parallax-layer-mid {
                    transform: none;
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

        <!-- 2026 Interactive Effects -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // === CURSOR GLOW EFFECT ===
                const cursorGlow = document.createElement('div');
                cursorGlow.className = 'cursor-glow';
                cursorGlow.style.opacity = '0';
                document.body.appendChild(cursorGlow);

                let cursorX = 0, cursorY = 0;
                let glowX = 0, glowY = 0;

                document.addEventListener('mousemove', (e) => {
                    cursorX = e.clientX;
                    cursorY = e.clientY;
                    cursorGlow.style.opacity = '1';
                });

                document.addEventListener('mouseleave', () => {
                    cursorGlow.style.opacity = '0';
                });

                // Smooth cursor follow
                function animateCursor() {
                    glowX += (cursorX - glowX) * 0.1;
                    glowY += (cursorY - glowY) * 0.1;
                    cursorGlow.style.left = glowX + 'px';
                    cursorGlow.style.top = glowY + 'px';
                    requestAnimationFrame(animateCursor);
                }
                animateCursor();

                // === 3D TILT EFFECT ===
                document.querySelectorAll('.tilt-3d').forEach(card => {
                    card.addEventListener('mousemove', (e) => {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

                        const rect = card.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        const centerX = rect.width / 2;
                        const centerY = rect.height / 2;
                        const rotateX = (y - centerY) / 10;
                        const rotateY = (centerX - x) / 10;

                        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
                    });

                    card.addEventListener('mouseleave', () => {
                        card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
                    });
                });

                // === MAGNETIC BUTTONS ===
                document.querySelectorAll('.btn-magnetic').forEach(button => {
                    button.addEventListener('mousemove', (e) => {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

                        const rect = button.getBoundingClientRect();
                        const x = e.clientX - rect.left - rect.width / 2;
                        const y = e.clientY - rect.top - rect.height / 2;

                        button.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;

                        const inner = button.querySelector('.btn-magnetic-inner');
                        if (inner) {
                            inner.style.transform = `translate(${x * 0.1}px, ${y * 0.1}px)`;
                        }
                    });

                    button.addEventListener('mouseleave', () => {
                        button.style.transform = 'translate(0, 0)';
                        const inner = button.querySelector('.btn-magnetic-inner');
                        if (inner) {
                            inner.style.transform = 'translate(0, 0)';
                        }
                    });
                });

                // === RIPPLE EFFECT ===
                document.querySelectorAll('.ripple-effect').forEach(element => {
                    element.addEventListener('click', function(e) {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

                        const rect = this.getBoundingClientRect();
                        const ripple = document.createElement('span');
                        ripple.className = 'ripple';
                        ripple.style.left = (e.clientX - rect.left) + 'px';
                        ripple.style.top = (e.clientY - rect.top) + 'px';
                        this.appendChild(ripple);

                        setTimeout(() => ripple.remove(), 600);
                    });
                });

                // === GLASS LIQUID MOUSE TRACKING ===
                document.querySelectorAll('.glass-liquid').forEach(element => {
                    element.addEventListener('mousemove', (e) => {
                        const rect = element.getBoundingClientRect();
                        const x = ((e.clientX - rect.left) / rect.width) * 100;
                        const y = ((e.clientY - rect.top) / rect.height) * 100;
                        element.style.setProperty('--mouse-x', x + '%');
                        element.style.setProperty('--mouse-y', y + '%');
                    });
                });

                // === INTERSECTION OBSERVER FOR SCROLL ANIMATIONS ===
                // Fallback for browsers without animation-timeline support
                if (!CSS.supports('animation-timeline', 'view()')) {
                    const observerOptions = {
                        root: null,
                        rootMargin: '0px',
                        threshold: 0.1
                    };

                    const scrollObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('is-visible');
                                if (!entry.target.classList.contains('scroll-repeat')) {
                                    scrollObserver.unobserve(entry.target);
                                }
                            }
                        });
                    }, observerOptions);

                    document.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right, .scroll-scale').forEach(el => {
                        el.style.opacity = '0';
                        scrollObserver.observe(el);
                    });

                    // Add fallback CSS
                    const style = document.createElement('style');
                    style.textContent = `
                        .scroll-reveal.is-visible,
                        .scroll-reveal-left.is-visible,
                        .scroll-reveal-right.is-visible,
                        .scroll-scale.is-visible {
                            animation: scroll-fade-up 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards !important;
                            animation-timeline: auto !important;
                        }
                        .scroll-reveal-left.is-visible {
                            animation-name: scroll-fade-left !important;
                        }
                        .scroll-reveal-right.is-visible {
                            animation-name: scroll-fade-right !important;
                        }
                        .scroll-scale.is-visible {
                            animation-name: scroll-scale-in !important;
                        }
                    `;
                    document.head.appendChild(style);
                }

                // === STAGGER LIST OBSERVER ===
                const staggerObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('stagger-animate');
                            staggerObserver.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });

                document.querySelectorAll('.stagger-list').forEach(list => {
                    staggerObserver.observe(list);
                });

                // === TEXT SPLIT REVEAL ===
                document.querySelectorAll('.text-split-reveal').forEach(element => {
                    const text = element.textContent;
                    element.textContent = '';
                    text.split('').forEach((char, i) => {
                        const span = document.createElement('span');
                        span.textContent = char === ' ' ? '\u00A0' : char;
                        span.style.animationDelay = `${i * 30}ms`;
                        element.appendChild(span);
                    });
                });

                // === SCROLL PROGRESS INDICATOR ===
                const progressBar = document.querySelector('.scroll-progress-bar');
                if (progressBar) {
                    window.addEventListener('scroll', () => {
                        const scrollTop = window.scrollY;
                        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                        const scrollPercent = (scrollTop / docHeight) * 100;
                        progressBar.style.width = scrollPercent + '%';
                    });
                }

                // === PARALLAX ON MOUSE MOVE ===
                document.querySelectorAll('[data-parallax-mouse]').forEach(element => {
                    const depth = parseFloat(element.dataset.parallaxMouse) || 0.05;

                    document.addEventListener('mousemove', (e) => {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

                        const moveX = (e.clientX - window.innerWidth / 2) * depth;
                        const moveY = (e.clientY - window.innerHeight / 2) * depth;
                        element.style.transform = `translate(${moveX}px, ${moveY}px)`;
                    });
                });

                // === SMOOTH SCROLL FOR ANCHOR LINKS ===
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

            // === LIVEWIRE INTEGRATION - Re-init effects after updates ===
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('morph.updated', ({ el }) => {
                    // Re-initialize tilt effects on new elements
                    el.querySelectorAll('.tilt-3d').forEach(card => {
                        if (!card.dataset.tiltInit) {
                            card.dataset.tiltInit = 'true';
                            card.addEventListener('mousemove', (e) => {
                                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                                const rect = card.getBoundingClientRect();
                                const x = e.clientX - rect.left;
                                const y = e.clientY - rect.top;
                                const centerX = rect.width / 2;
                                const centerY = rect.height / 2;
                                const rotateX = (y - centerY) / 10;
                                const rotateY = (centerX - x) / 10;
                                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
                            });
                            card.addEventListener('mouseleave', () => {
                                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
                            });
                        }
                    });

                    // Re-initialize magnetic buttons
                    el.querySelectorAll('.btn-magnetic').forEach(button => {
                        if (!button.dataset.magneticInit) {
                            button.dataset.magneticInit = 'true';
                            button.addEventListener('mousemove', (e) => {
                                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                                const rect = button.getBoundingClientRect();
                                const x = e.clientX - rect.left - rect.width / 2;
                                const y = e.clientY - rect.top - rect.height / 2;
                                button.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
                            });
                            button.addEventListener('mouseleave', () => {
                                button.style.transform = 'translate(0, 0)';
                            });
                        }
                    });

                    // Re-initialize glass liquid
                    el.querySelectorAll('.glass-liquid').forEach(element => {
                        if (!element.dataset.glassInit) {
                            element.dataset.glassInit = 'true';
                            element.addEventListener('mousemove', (e) => {
                                const rect = element.getBoundingClientRect();
                                const x = ((e.clientX - rect.left) / rect.width) * 100;
                                const y = ((e.clientY - rect.top) / rect.height) * 100;
                                element.style.setProperty('--mouse-x', x + '%');
                                element.style.setProperty('--mouse-y', y + '%');
                            });
                        }
                    });
                });
            }
        </script>
        @stack('scripts')
    </body>
</html>
