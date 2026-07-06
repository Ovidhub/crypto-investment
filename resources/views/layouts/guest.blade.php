<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$settings->site_name}} | @yield('title')</title>

    <link rel="icon" href="{{ asset('storage/app/public/'.$settings->favicon)}}" type="image/png"/>
    
    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                screens: {
                    'xs': '375px',
                },
                colors: {
                    // Primary: Amber (#F59E0B) - Professional, High-value, Gold-standard
                    primary: {
                        DEFAULT: '#F59E0B',
                        '50':  '#fffbeb',
                        '100': '#fef3c7',
                        '200': '#fde68a',
                        '300': '#fcd34d',
                        '400': '#fbbf24',
                        '500': '#F59E0B',
                        '600': '#d97706',
                        '700': '#b45309',
                        '800': '#92400e',
                        '900': '#78350f',
                    },
                    // Secondary: Orange (#FB923C) - Energy, Alerts, Call-to-Actions
                    secondary: {
                        DEFAULT: '#FB923C',
                        '50':  '#fff7ed',
                        '100': '#ffedd5',
                        '200': '#fed7aa',
                        '300': '#fdba74',
                        '400': '#FB923C',
                        '500': '#f97316',
                        '600': '#ea580c',
                        '700': '#c2410c',
                        '800': '#9a3412',
                        '900': '#7c2d12',
                    },
                    // Tertiary: Warm Sun - Used for softer highlights and subtle gradients
                    tertiary: {
                        DEFAULT: '#FBBF24',
                        '50':  '#fffdf5',
                        '100': '#fff8e6',
                        '200': '#ffecb3',
                        '300': '#ffe080',
                        '400': '#ffd44d',
                        '500': '#FBBF24',
                        '600': '#e6a800',
                        '700': '#b38200',
                        '800': '#805d00',
                        '900': '#4d3800',
                    },
                    // Dark: Keeping the deep Navy/Slate for the "Midnight Quantum" look
                    dark: {
                        DEFAULT: '#020617',
                        '50': '#0F172A',
                        '100': '#1E293B',
                        '200': '#334155',
                        '300': '#475569',
                    },
                    light: {
                        DEFAULT: '#F8FAFC',
                        '50': '#FFFFFF',
                        '100': '#F1F5F9',
                        '200': '#E2E8F0',
                        '300': '#CBD5E1',
                    },
                }
            }
        }
    }
</script>
     <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" integrity="sha512-fD9DI5bZwQxOi7MhYWnnNPlvXdp/2Pj3XSTRrFs5FQa4mizyGLnJcN6tuvUS6LbmgN1ut+XGSABKvjN0H6Aoow==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <!-- Google Recaptcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    @section('styles')
    <!-- Custom Styles -->
    <style>
        /* Simple dot pattern background */
        .bg-dot-pattern {
            background-image: radial-gradient(circle, rgba(0, 0, 0, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        .light .bg-dot-pattern {
            background-image: radial-gradient(circle, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
        }

        /* Responsive adjustments for split layout */
        @media (max-width: 1023px) {
            .split-layout {
                min-height: 100vh;
            }
        }
    </style>
    @show
</head>
<body class="dark:bg-dark bg-light-100 transition-colors duration-200">
    <!-- Theme Detection Script - Run Early -->
    <script>
        // Check for saved theme preference, default to dark mode
        const savedTheme = localStorage.getItem('darkMode');
        
        // Only switch to light mode if explicitly saved as false
        if (savedTheme === 'false') {
            document.documentElement.classList.remove('dark');
        }
        // Otherwise stay in dark mode (already set in HTML)
    </script>

    <!-- Theme Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button id="themeToggle" class="p-2 rounded-full dark:bg-dark-100 bg-light-200 shadow-sm">
            <i data-lucide="sun" class="h-5 w-5 dark:text-white text-dark hidden dark:block"></i>
            <i data-lucide="moon" class="h-5 w-5 dark:text-white text-dark block dark:hidden"></i>
        </button>
    </div>
    
    <!-- Main Content - Split Layout -->
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Column - Illustration (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 bg-dot-pattern dark:bg-dark-50 bg-light-200 relative overflow-hidden">
            <!-- Illustration Container -->
            <div class="flex flex-col justify-center items-center p-12 w-full max-w-2xl mx-auto">
                <!-- Logo -->
                <div class="absolute top-8 left-8">
                    <a href="/">
                        <img src="{{ asset('storage/app/public/'.$settings->logo) }}" alt="Logo" class="h-10">
                    </a>
                </div>
                
                <!-- Illustration from Open Source Library -->
                <div class="mb-8 w-4/5 max-w-md mx-auto">
                    <img src="https://raw.githubusercontent.com/cruip/open-react-template/refs/heads/master/public/images/features.png" 
                         alt="Illustration" class="w-full h-auto" id="illustrationImage">
                </div>
                
                <!-- Text Content -->
                <div class="text-center">
                    <h1 class="text-3xl font-bold dark:text-white text-dark mb-4" id="illustrationTitle">Welcome to {{ $settings->site_name }}</h1>
                    <p class="dark:text-gray-300 text-gray-700 max-w-md mx-auto" id="illustrationDescription">
                        Our platform offers secure trading, real-time market data, and expert insights to help you achieve your financial goals.
                    </p>
                </div>
                
                <!-- Optional Feature List -->
                <div class="mt-8 w-full max-w-md">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i data-lucide="check-circle" class="h-5 w-5 text-secondary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium dark:text-white text-dark">Secure Trading</h3>
                                <p class="text-sm dark:text-gray-400 text-gray-600">State-of-the-art security features to protect your investments</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i data-lucide="check-circle" class="h-5 w-5 text-secondary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium dark:text-white text-dark">Real-Time Analytics</h3>
                                <p class="text-sm dark:text-gray-400 text-gray-600">Up-to-the-minute market data to inform your decisions</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i data-lucide="check-circle" class="h-5 w-5 text-secondary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium dark:text-white text-dark">Expert Support</h3>
                                <p class="text-sm dark:text-gray-400 text-gray-600">24/7 customer support from our team of specialists</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Content Area -->
        <div class="flex-1 min-h-screen flex flex-col">
            <!-- Mobile Logo (Visible only on mobile) -->
            <div class="flex justify-center pt-24 lg:hidden">
                <a href="/">
                    <img src="{{ asset('storage/app/public/'.$settings->logo) }}" alt="Logo" class="h-10">
                </a>
            </div>
            
            <!-- Content Wrapper -->
            <div class="flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
                <div class="w-full max-w-md">
                    <!-- Content -->
                    @yield('content')
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="py-4">
                <div class="container mx-auto px-4">
                    <p class="text-center text-sm dark:text-gray-400 text-gray-600">
                        © {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </div>
<script>
        lucide.createIcons();
    </script>
    <!-- Core Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            lucide.createIcons();
            
            // Theme toggle functionality
            const themeToggle = document.getElementById('themeToggle');
            
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('darkMode', 'false');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('darkMode', 'true');
                    }
                });
            }
            
            // Random illustration and text based on page
            const pageType = document.title.toLowerCase();
            const illustrations = [
                {
                    image: "https://raw.githubusercontent.com/cruip/open-react-template/refs/heads/master/public/images/features.png",
                    title: "Welcome to {{ $settings->site_name }}",
                    description: "Our platform offers secure trading, real-time market data, and expert insights to help you achieve your financial goals."
                },
                {
                    image: "https://raw.githubusercontent.com/cruip/open-react-template/refs/heads/master/public/images/features.png",
                    title: "Invest with Confidence",
                    description: "Take control of your financial future with our transparent and secure investment platform."
                },
                {
                    image: "https://raw.githubusercontent.com/cruip/open-react-template/refs/heads/master/public/images/features.png",
                    title: "Simple, Secure, Reliable",
                    description: "Our advanced platform provides the tools and resources you need to succeed in today's market."
                }
            ];
            
            // Select random illustration or based on page type
            let selectedIllustration;
            if (pageType.includes('login')) {
                selectedIllustration = illustrations[0];
            } else if (pageType.includes('register') || pageType.includes('sign up')) {
                selectedIllustration = illustrations[1];
            } else {
                selectedIllustration = illustrations[2];
            }
            
            // Set illustration and text
            const illustrationImage = document.getElementById('illustrationImage');
            const illustrationTitle = document.getElementById('illustrationTitle');
            const illustrationDescription = document.getElementById('illustrationDescription');
            
            if (illustrationImage && illustrationTitle && illustrationDescription) {
                illustrationImage.src = selectedIllustration.image;
                illustrationTitle.textContent = selectedIllustration.title;
                illustrationDescription.textContent = selectedIllustration.description;
            }
        });
    </script>
    
    @section('scripts')
    @show
    
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.4/dist/livewire-turbolinks.js" data-turbolinks-eval="false" data-turbo-eval="false"></script>
</body>
</html>