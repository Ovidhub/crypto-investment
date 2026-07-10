<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{$settings->site_name}} - Powerful, fully-featured investment and trading platform. Access investment plans, cryptocurrency trading, stock trading, copy trading, automated bot trading, staking, and stock investment. Professional trading solutions for everyone.">
    <link rel="icon" type="image/png" href="{{ asset('storage/app/public/' . $settings->favicon) }}">
    <title>{{$settings->site_name}} • @yield('title', 'Crypto Trading Platform')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    @include('layouts.partials.newtheme')

    <style>
        .poppins-light { font-family: "DM Sans", sans-serif; font-weight: 300; }
        .poppins-regular { font-family: "DM Sans", sans-serif; font-weight: 400; }
        .poppins-medium { font-family: "DM Sans", sans-serif; font-weight: 500; }
        .poppins-semibold { font-family: "DM Sans", sans-serif; font-weight: 600; }
        .poppins-bold { font-family: "DM Sans", sans-serif; font-weight: 700; }
        .manrope { font-family: "DM Sans", sans-serif; }

        body { overflow-x: hidden; background-color: #05060a; }

        .owl-item { display: flex; align-items: center; justify-content: center; }
        .owl-carousel .item img { height: 100px; object-fit: contain; max-width: 100%; }

        /* Custom scrollbar (dark) */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #0a0d14; }
        ::-webkit-scrollbar-thumb { background: #232a36; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: #333c4c; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"DM Sans"', 'sans-serif'],
                        sans: ['"DM Sans"', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#7c3aed',
                        'primary-dark': '#6b28d9',
                        'primary-light': '#b57bff',
                        'brand': '#b57bff',
                        'brand-2': '#7c3aed',
                        'accent': '#22e58a',
                        'ink': '#05060a',
                        'panel': '#0b0e15',
                        'panel-2': '#10141d',
                        'card': '#121722',
                        'line': '#1e2530',
                    }
                }
            }
        }
    </script>
</head>
<body class="overflow-x-hidden w-screen">
    <!-- WhatsApp Button -->
    @if(!empty($settings->whatsapp))
    <a href="https://wa.me/{{ $settings->whatsapp }}" target="_blank" rel="noopener noreferrer" class="fixed bottom-4 left-4 bg-green-500 text-white p-3 rounded-full shadow-lg hover:bg-green-600 transition-colors duration-300 z-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
    @endif

    <nav class="sticky top-0 z-50 bg-[#020617]/95 backdrop-blur-sm border-b border-white/5 font-outfit py-4 px-4 md:px-8">
        <div class="container mx-auto flex justify-between items-center">
            
            <div class="flex items-center gap-6">
                <button id="menu-toggle" class="md:hidden text-slate-300 hover:text-white transition-colors">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>

                <a href="index" class="flex items-center">
                    <img src="{{ asset('storage/app/public/' . $settings->logo) }}" alt="{{$settings->site_name}}" class="h-8 md:h-9">
                </a>
            </div>

            <div class="hidden md:flex items-center gap-10">
                <a href="index" class="text-white text-sm font-medium hover:text-brand transition-colors">Home</a>
                <a href="about" class="text-slate-400 text-sm font-medium hover:text-brand transition-colors">About</a>
                <a href="services" class="text-slate-400 text-sm font-medium hover:text-brand transition-colors">Services</a>
                <a href="contact" class="text-slate-400 text-sm font-medium hover:text-brand transition-colors">Contact</a>
            </div>

            <div class="flex items-center gap-3">
                <a href="login" class="hidden md:block text-slate-400 hover:text-white text-sm font-medium px-3 py-2 transition-colors">
                    Login
                </a>
                <a href="register" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all shadow-lg shadow-brand-2/30">
                    Sign Up
                </a>
            </div>
        </div>
    </nav>

    <div id="mobile-menu" class="hidden fixed inset-0 z-[60] bg-[#020617] transition-transform duration-300 transform -translate-x-full font-outfit flex flex-col h-full">
        <div class="flex justify-between items-center p-6 border-b border-white/5">
            <img src="{{ asset('storage/app/public/' . $settings->logo) }}" alt="Logo" class="h-7">
            <button id="close-menu" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">
            <a href="index" class="text-xl font-medium text-white hover:text-brand transition-colors block">Home</a>
            <a href="about" class="text-xl font-medium text-slate-300 hover:text-brand transition-colors block">About</a>
            <a href="services" class="text-xl font-medium text-slate-300 hover:text-brand transition-colors block">Services</a>
            <a href="contact" class="text-xl font-medium text-slate-300 hover:text-brand transition-colors block">Contact</a>
        </div>

        <div class="p-6 border-t border-white/5 grid gap-3">
            <a href="login" class="flex items-center justify-center py-3 rounded-lg bg-white/5 text-white font-medium hover:bg-white/10 transition-colors">
                Log In
            </a>
            <a href="register" class="flex items-center justify-center py-3 rounded-lg bg-primary text-white font-medium hover:bg-primary-dark transition-colors">
                Create Account
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="hidden fixed bottom-10 right-10 z-50 p-4 bg-white text-black rounded-lg shadow-lg border border-primary max-w-md"></div>

    <div class="bg-[#020617] font-outfit text-slate-400 relative">
    
        <div class="h-px w-full bg-gradient-to-r from-transparent via-brand/40 to-transparent"></div>

        <footer class="pt-16 pb-8 px-4 md:px-8 container mx-auto relative z-10 font-outfit">
        
            <div class="relative mb-20">
                <div class="bg-[#1e293b]/30 rounded-2xl p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8 border border-white/5">
                    <div class="flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
                        <div class="relative">
                            <img src="{{ asset('storage/app/public/' . $settings->logo) }}" alt="{{$settings->site_name}}" class="h-10 md:h-12">
                        </div>
                        <div>
                            <h2 class="text-2xl md:text-3xl font-bold text-white">
                                Pro Trading, For Everyone.
                            </h2>
                            <p class="text-slate-400 mt-2 text-sm">Institutional grade tools for the modern retail investor.</p>
                        </div>
                    </div>
                    
                    <a href="register" class="inline-flex items-center px-8 py-3.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors shadow-lg shadow-brand-2/20">
                        Get Started Now
                        <i class="fa-solid fa-arrow-right ml-3 text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Owl Carousel with overflow-x-hidden -->
            <div id="investor-slides" class="overflow-x-hidden mb-16">
                <div class="owl-carousel owl-theme">
                    @php $partners = [1,2,3,4,5,6,7,8,9]; @endphp
                    @foreach($partners as $p)
                    <div class="item px-4">
                        <img src="{{ asset('temp/assets/images/brands/'.$p.'.png') }}" alt="Partner {{$p}}" 
                             class="h-8 md:h-10 w-auto max-w-full object-contain filter grayscale opacity-30 hover:opacity-100 hover:grayscale-0 transition-all duration-300">
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-x-8 gap-y-10 mb-16 border-b border-white/5 pb-12">
                <div>
                    <h3 class="text-white font-semibold mb-4 text-sm">Company</h3>
                    <ul class="space-y-3">
                        <li><a href="about" class="text-slate-400 hover:text-brand text-sm transition-colors">About Us</a></li>
                        <li><a href="services" class="text-slate-400 hover:text-brand text-sm transition-colors">Services</a></li>
                        <li><a href="contact" class="text-slate-400 hover:text-brand text-sm transition-colors">Contact</a></li>
                        <li><a href="terms" class="text-slate-400 hover:text-brand text-sm transition-colors">Terms</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-white font-semibold mb-4 text-sm">Products</h3>
                    <ul class="space-y-3">
                        <li><a href="services" class="text-slate-400 hover:text-brand text-sm transition-colors">Trading</a></li>
                        <li><a href="services" class="text-slate-400 hover:text-brand text-sm transition-colors">Investment</a></li>
                        <li><a href="services" class="text-slate-400 hover:text-brand text-sm transition-colors">Crypto</a></li>
                        <li><a href="services" class="text-slate-400 hover:text-brand text-sm transition-colors">Stocks</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4 text-sm">Support</h3>
                    <ul class="space-y-3">
                        <li><a href="contact" class="text-slate-400 hover:text-brand text-sm transition-colors">Help Center</a></li>
                        <li><a href="contact" class="text-slate-400 hover:text-brand text-sm transition-colors">FAQ</a></li>
                        <li><a href="contact" class="text-slate-400 hover:text-brand text-sm transition-colors">Support</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4 text-sm">Legal</h3>
                    <ul class="space-y-3">
                        <li><a href="terms" class="text-slate-400 hover:text-brand text-sm transition-colors">Privacy Policy</a></li>
                        <li><a href="terms" class="text-slate-400 hover:text-brand text-sm transition-colors">Terms of Service</a></li>
                        <li><a href="terms" class="text-slate-400 hover:text-brand text-sm transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>

                <div class="col-span-2">
                    <h3 class="text-white font-semibold mb-4 text-sm">Connect With Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-brand text-sm"></i>
                            <span class="text-slate-400 text-sm hover:text-white transition-colors cursor-pointer">{{$settings->contact_email}}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-brand text-sm"></i>
                            <span class="text-slate-400 text-sm hover:text-white transition-colors">{{ $settings->address_o }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mb-6 rounded-xl border border-white/5 bg-white/[0.02] p-4">
                <p class="text-[11px] leading-relaxed text-slate-500">
                    <strong class="text-slate-400">Risk disclaimer:</strong> {{$settings->site_name}} provides access to cryptocurrency markets and related services. Trading and investing in digital assets involves substantial risk, including the possible loss of your entire capital. Any rates or targets shown are not guaranteed and past performance does not indicate future results. Nothing on this site is financial advice. Only invest funds you can afford to lose, and comply with the laws of your jurisdiction.
                </p>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex flex-col md:flex-row items-center gap-4 text-center md:text-left">
                    <p class="text-xs text-slate-500">
                        &copy; {{ date('Y') }} {{$settings->site_name}}. All rights reserved.
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="https://facebook.com" target="_blank" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-400 hover:bg-brand-2 hover:text-white transition-all text-sm">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-400 hover:bg-sky-500 hover:text-white transition-all text-sm">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://linkedin.com" target="_blank" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-400 hover:bg-primary-dark hover:text-white transition-all text-sm">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-400 hover:bg-pink-600 hover:text-white transition-all text-sm">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- Footer background, now responsive -->
            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[200vw] max-w-full h-[300px] bg-brand-2/10 blur-[120px] -z-10 rounded-full"></div>
        </footer>
    </div>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-6 right-6 p-3 rounded-full bg-primary text-white shadow-lg transform transition-transform duration-300 hover:scale-110 focus:outline-none hidden z-40">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <script>
        $(document).ready(function(){
            // Owl Carousel
            $(".owl-carousel").owlCarousel({
                autoplay: true,
                autoplayTimeout: 2000,
                loop: true,
                nav: false,
                dots: false,
                responsive: {
                    0: { items: 2 },
                    600: { items: 3 },
                    1000: { items: 6 }
                }
            });

            // Mobile Menu
            const menuToggle = document.getElementById('menu-toggle');
            const closeMenu = document.getElementById('close-menu');
            const mobileMenu = document.getElementById('mobile-menu');

            menuToggle.addEventListener('click', () => {
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.remove('-translate-x-full');
                mobileMenu.classList.add('translate-x-0');
                document.body.style.overflow = 'hidden';
            });

            closeMenu.addEventListener('click', () => {
                mobileMenu.classList.remove('translate-x-0');
                mobileMenu.classList.add('-translate-x-full');
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                }, 300);
                document.body.style.overflow = 'auto';
            });

            // Back to Top
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').fadeIn();
                } else {
                    $('#back-to-top').fadeOut();
                }
            });

            $('#back-to-top').click(function() {
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });

            // Toast Notification
            window.showToast = function(message, duration = 3000) {
                const toast = $('#toast');
                toast.text(message);
                toast.removeClass('hidden').addClass('block');
                setTimeout(function() {
                    toast.removeClass('block').addClass('hidden');
                }, duration);
            };

            // (Removed: fabricated "someone just invested/withdrew $X" popups.)
        });
    </script>

    @stack('scripts')
    @include('layouts.livechat')
</body>
</html>
