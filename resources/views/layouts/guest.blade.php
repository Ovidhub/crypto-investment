<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$settings->site_name}} | @yield('title')</title>

    <link rel="icon" href="{{ asset('storage/app/public/'.$settings->favicon)}}" type="image/png"/>

    <!-- Core CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                screens: { 'xs': '375px' },
                fontFamily: {
                    display: ['"DM Sans"', 'sans-serif'],
                    sans: ['"DM Sans"', 'sans-serif'],
                },
                colors: {
                    primary: {
                        DEFAULT: '#7c3aed',
                        '50': '#f5f0ff', '100': '#ede4ff', '200': '#d9c4ff', '300': '#c19dff',
                        '400': '#b57bff', '500': '#9b5cff', '600': '#7c3aed', '700': '#6b28d9',
                        '800': '#571db0', '900': '#45178c',
                    },
                    brand: '#b57bff',
                    'brand-2': '#7c3aed',
                    accent: '#22e58a',
                    dark: { DEFAULT: '#05060a', '50': '#0b0e15', '100': '#10141d', '200': '#1e2530', '300': '#2a303c' },
                    light: { DEFAULT: '#F8FAFC', '50': '#FFFFFF', '100': '#F1F5F9', '200': '#E2E8F0', '300': '#CBD5E1' },
                }
            }
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Google Recaptcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    @include('layouts.partials.newtheme')

    @section('styles')
    <style>
        body, h1, h2, h3 { font-family: "DM Sans", sans-serif; }
    </style>
    @show
</head>
<body class="bg-dark text-white">

    <!-- Main Content - Split Layout -->
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Column - Branded Showcase (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-dark-50">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute inset-0 grid-bg"></div>
                <div class="absolute -top-32 left-1/3 h-[440px] w-[440px] rounded-full bg-brand-2/30 blur-[120px]"></div>
                <div class="absolute bottom-0 right-0 h-[320px] w-[320px] rounded-full bg-accent/15 blur-[120px]"></div>
            </div>

            <div class="relative z-10 flex flex-col justify-center p-12 w-full max-w-2xl mx-auto">
                <a href="/" class="flex items-center gap-3 mb-12">
                    <img src="{{ asset('storage/app/public/'.$settings->logo) }}" alt="Logo" class="h-10">
                </a>

                <span class="inline-flex w-fit items-center gap-2 rounded-full border border-dark-200 bg-white/5 px-4 py-1.5 text-xs font-medium text-[#c7cedb]">
                    <span class="h-1.5 w-1.5 rounded-full bg-accent"></span>
                    Trusted by investors worldwide
                </span>

                <h1 class="mt-6 font-display text-4xl font-bold leading-tight text-white">
                    Invest smarter with <span class="brand-gradient">{{ $settings->site_name }}</span>
                </h1>
                <p class="mt-4 text-[#9aa3b2] max-w-md">
                    Secure trading, real-time market data and a portfolio that works for you around the clock.
                </p>

                <div class="mt-10 space-y-4 w-full max-w-md">
                    @php
                        $points = [
                            ['title' => 'Secure Trading', 'desc' => 'Strong security features to protect your investments'],
                            ['title' => 'Real-Time Analytics', 'desc' => 'Up-to-the-minute market data to inform your decisions'],
                            ['title' => 'Expert Support', 'desc' => '24/7 customer support from our team of specialists'],
                        ];
                    @endphp
                    @foreach($points as $p)
                    <div class="flex items-start gap-3 rounded-2xl border border-dark-200 bg-dark-100/60 p-4">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-brand/10 text-accent">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-white">{{ $p['title'] }}</h3>
                            <p class="text-sm text-[#9aa3b2]">{{ $p['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column - Content Area -->
        <div class="flex-1 min-h-screen flex flex-col bg-dark relative">
            <div class="pointer-events-none absolute inset-0 lg:hidden">
                <div class="absolute inset-0 grid-bg"></div>
                <div class="absolute -top-24 right-0 h-[280px] w-[280px] rounded-full bg-brand-2/25 blur-[100px]"></div>
            </div>

            <!-- Mobile Logo -->
            <div class="relative z-10 flex justify-center pt-12 lg:hidden">
                <a href="/">
                    <img src="{{ asset('storage/app/public/'.$settings->logo) }}" alt="Logo" class="h-10">
                </a>
            </div>

            <!-- Content Wrapper -->
            <div class="relative z-10 flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
                <div class="w-full max-w-md">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="relative z-10 py-4">
                <div class="container mx-auto px-4">
                    <p class="text-center text-sm text-[#9aa3b2]">
                        © {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script> if (window.lucide) lucide.createIcons(); </script>

    @section('scripts')
    @show

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.4/dist/livewire-turbolinks.js" data-turbolinks-eval="false" data-turbo-eval="false"></script>
</body>
</html>
