<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <script type="text/javascript" src="https://js.stripe.com/v3/"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->site_name }} @yield('title')</title>

    <link rel="icon" href="{{ asset('storage/app/public/' . $settings->favicon) }}" type="image/png" />

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
                    // Primary: Violet (new brand)
                    primary: {
                        DEFAULT: '#7c3aed',
                        '50':  '#f5f0ff',
                        '100': '#ede4ff',
                        '200': '#d9c4ff',
                        '300': '#c19dff',
                        '400': '#b57bff',
                        '500': '#9b5cff',
                        '600': '#7c3aed',
                        '700': '#6b28d9',
                        '800': '#571db0',
                        '900': '#45178c',
                    },
                    // Secondary: Green accent (new brand)
                    secondary: {
                        DEFAULT: '#22e58a',
                        '50':  '#e9fdf3',
                        '100': '#c9f9e0',
                        '200': '#93f2c2',
                        '300': '#5ceaa4',
                        '400': '#22e58a',
                        '500': '#12c973',
                        '600': '#0ea15c',
                        '700': '#0c7e49',
                        '800': '#0a5e37',
                        '900': '#073d24',
                    },
                    brand: '#b57bff',
                    'brand-2': '#7c3aed',
                    // Tertiary: Soft Gold - Used for background tints/subtle borders
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
                    // Accent: Keeping the Binance-style logic but unified with the palette
                    accent: {
                        DEFAULT: '#F0B90B',
                        '50': '#FEF9E6',
                        '100': '#FDF3CC',
                        '200': '#FAE699',
                        '300': '#F7DA66',
                        '400': '#F0B90B',
                        '500': '#C39508',
                        '600': '#967106',
                        '700': '#694D04',
                        '800': '#3C2A02',
                        '900': '#0F0A01',
                    },
                    danger: {
                        DEFAULT: '#FF6B6B',
                        '50': '#FFF0F0',
                        '100': '#FFE0E0',
                        '200': '#FFC2C2',
                        '300': '#FFA3A3',
                        '400': '#FF6B6B',
                        '500': '#FF3838',
                        '600': '#FF0505',
                        '700': '#D10000',
                        '800': '#9E0000',
                        '900': '#6B0000',
                    },
                    purple: {
                        DEFAULT: '#9164CC',
                        '50': '#F3EDFA',
                        '100': '#E7DCF5',
                        '200': '#CFB9EB',
                        '300': '#B796E0',
                        '400': '#9164CC',
                        '500': '#7642BE',
                        '600': '#5D33A0',
                        '700': '#44267C',
                        '800': '#2C1958',
                        '900': '#140B34',
                    },
                    dark: {
                        DEFAULT: '#020617', // Deepened for better contrast with Orange
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
                },
                animation: {
                    'in': 'fadeIn 0.3s ease-in-out',
                    'slide-in-top': 'slideInFromTop 0.3s ease-in-out',
                    'slide-in-bottom': 'slideInFromBottom 0.3s ease-in-out',
                },
                keyframes: {
                    fadeIn: {
                        'from': { opacity: '0' },
                        'to': { opacity: '1' }
                    },
                    slideInFromTop: {
                        'from': { transform: 'translateY(-20px)', opacity: '0' },
                        'to': { transform: 'translateY(0)', opacity: '1' }
                    },
                    slideInFromBottom: {
                        'from': { transform: 'translateY(20px)', opacity: '0' },
                        'to': { transform: 'translateY(0)', opacity: '1' }
                    }
                }
            }
        }
    }
</script>
 <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- Additional libraries that are still needed -->
    <link rel="stylesheet" href="{{ asset('dash2/libs/sweetalert2/dist/sweetalert2.min.css') }}">
    <script src="{{ asset('dash2/libs/sweetalert/sweetalert.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.21/af-2.3.5/b-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/r-2.2.5/datatables.min.css" />

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.1/dist/alpine.min.js" defer></script>

    <!-- Select2 for dropdowns -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <!--  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" integrity="sha512-fD9DI5bZwQxOi7MhYWnnNPlvXdp/2Pj3XSTRrFs5FQa4mizyGLnJcN6tuvUS6LbmgN1ut+XGSABKvjN0H6Aoow==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <link rel="stylesheet" href="https://jcsoci.github.io/FontAwesome6Pro/css/all.min.css" >

    <!-- Custom Styles -->
    <style>
        /* Alpine.js cloak to prevent flash before JS loads */
        [x-cloak] {
            display: none !important;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideInFromTop {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes slideInFromBottom {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        .slide-in-from-top {
            animation: slideInFromTop 0.3s ease-in-out;
        }
        .slide-in-from-bottom {
            animation: slideInFromBottom 0.3s ease-in-out;
        }

        /* DataTables responsive fixes */
        .dataTables_wrapper {
            overflow-x: auto;
            width: 100%;
        }

        /* Select2 dropdown fixes */
        .select2-container {
            width: 100% !important;
        }

        /* Light and Dark mode styles */
        .dark {
            color-scheme: dark;
        }

        /* Dark mode styles for DataTables */
        .dark .dataTables_wrapper .dataTable {
            background-color: #1E2430;
            color: #fff;
        }
        .dark .dataTables_wrapper .dataTable th,
        .dark .dataTables_wrapper .dataTable td {
            border-color: #2A303C;
        }
        .dark .dataTables_wrapper .dataTables_info,
        .dark .dataTables_wrapper .dataTables_paginate {
            color: #CBD5E1 !important;
        }

        /* Light mode styles for Select2 */
        .light .select2-container--default .select2-selection--single {
            background-color: #F8FAFC;
            border-color: #CBD5E1;
        }

        /* Dark mode styles for Select2 */
        .dark .select2-container--default .select2-selection--single {
            background-color: #1E2430;
            border-color: #2A303C;
            color: #F8FAFC;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #F8FAFC;
        }
    </style>
    @include('layouts.partials.newtheme')
    @livewireStyles
    @yield('styles')
    @stack('styles')
</head>

<body class="dark:bg-dark bg-light-100 dark:text-white text-dark transition-colors duration-200">
    <script>
        {!! $settings->tawk_to !!}
    </script>

    <!-- Application container -->
    <div class="flex flex-col md:flex-row min-h-screen relative">
        @include('user.sidebar')

        <!-- Main Content -->
    <div class="w-full flex flex-col">
    <!-- Top Menu -->
    @include('user.topmenu')

    <!-- Page content -->
    <div class="p-4 md:p-6 pb-20 md:pb-8 overflow-x-hidden flex-grow">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="py-4 border-t dark:border-dark-100 border-light-200 text-center md:text-left hidden md:flex md:justify-between md:items-center px-4 md:px-6">
        <div>
            <p class="text-sm dark:text-gray-400 text-gray-600">All Rights Reserved &copy; {{ $settings->site_name }} {{ date('Y') }}</p>
        </div>
    </div>
</div>
    </div>

    <!-- Mobile Navigation -->
    @include('user.mobile-navigation')

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ asset('dash2/libs/sweetalert/sweetalert.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/af-2.3.5/b-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/r-2.2.5/datatables.min.js"></script>
<script>
        lucide.createIcons();
    </script>
    <!-- Theme Management Script -->
    <script>
        // Theme initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Check for saved theme preference, default to dark mode
            const savedTheme = localStorage.getItem('darkMode');

            // Only switch to light mode if explicitly saved as false
            if (savedTheme === 'false') {
                document.documentElement.classList.remove('dark');
            } else {
                // Default to dark mode (already set in HTML but ensure it's there)
                document.documentElement.classList.add('dark');
            }

            // Update theme switcher icon
            updateThemeIcon();
        });

        // Function to toggle theme
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            }

            // Update the icon
            updateThemeIcon();
        }

        // Update theme icon based on current theme
        function updateThemeIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const sunIcon = document.querySelector('[data-lucide="sun"]');
            const moonIcon = document.querySelector('[data-lucide="moon"]');

            if (sunIcon && moonIcon) {
                if (isDark) {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                }
            }
        }
    </script>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            // Mobile menu toggle functionality
            const menuToggles = document.querySelectorAll('[data-action="sidenav-pin"]');
            const sidebar = document.getElementById('sidenav-main');

            if (menuToggles.length > 0 && sidebar) {
                menuToggles.forEach(toggle => {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        sidebar.classList.toggle('active');
                        sidebar.classList.toggle('hidden');
                        sidebar.classList.toggle('md:block');
                    });
                });

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(e) {
                    const isMobile = window.innerWidth < 768;
                    const clickedInside = sidebar.contains(e.target);
                    const clickedToggle = Array.from(menuToggles).some(toggle => toggle.contains(e.target));

                    if (isMobile && !clickedInside && !clickedToggle && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                        sidebar.classList.add('hidden');
                        sidebar.classList.add('md:block');
                    }
                });
            }

            // Initialize Select2
            if ($.fn.select2) {
                $('.select2').select2();
            }

            // Initialize DataTables responsively
            if ($.fn.DataTable) {
                $('.datatable').DataTable({
                    responsive: true,
                    dom: '<"top"fl>rt<"bottom"ip>',
                    language: {
                        search: "",
                        searchPlaceholder: "Search..."
                    }
                });
            }
        });
    </script>

   @include('layouts.livechat')

   

    @livewireScripts
    @yield('scripts')
    @stack('scripts')
</body>
</html>
