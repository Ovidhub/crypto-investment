@php
    if ($settings->redirect_url != null or !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')
@section('title', 'Home')

@section('content')

<section class="relative min-h-screen flex items-center pt-20 pb-12 overflow-hidden bg-[#010522]">
    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-primary/20 rounded-full blur-[120px]"></div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="w-full lg:w-1/2 text-center lg:text-center space-y-8 mx-auto">
                <div class="inline-flex items-center justify-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-medium mx-auto">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    Live Trading Active
                </div>
            
                <h1 class="text-4xl md:text-7xl font-bold text-white leading-tight tracking-wider">
                    Empower Your Wealth <br>
                    <span class="text-primary tracking-widest"> & Master the Markets </span>
                </h1>

            
                <p class="text-gray-400 text-lg md:text-xl max-w-xl mx-auto">
                    Invest, Trade, Stake & Grow Your Wealth with the world's most intuitive financial ecosystem.
                </p>
            
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold transition-all transform hover:-translate-y-1 shadow-lg shadow-primary/25">
                        Start Investing
                    </a>
                    <a href="services" class="px-8 py-4 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-xl font-bold transition-all">
                        View Services
                    </a>
                </div>
            </div>

            <div class="w-full lg:w-1/2 relative">
                <img src="{{ asset('temp/assets/images/heros.png') }}" alt="Trading Platform" class="relative z-10 w-full h-auto drop-shadow-2xl">
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-white relative overflow-hidden">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
            <div class="space-y-2">
                <h4 class="text-primary font-bold tracking-widest text-sm uppercase">Market Overview</h4>
                <h2 class="text-3xl md:text-5xl font-bold text-[#010522]">Buy, sell & manage <br><span class="text-gray-400 font-light text-2xl md:text-4xl">over 100 crypto assets</span></h2>
            </div>
            <a href="#" class="text-primary font-bold flex items-center gap-2 hover:gap-4 transition-all group">
                Explore all assets <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php 
                $assets = [
                    ['name' => 'Bitcoin', 'sym' => 'BTC', 'id' => 'bitcoin', 'color' => 'bg-orange-500', 'icon' => 'fa-bitcoin'],
                    ['name' => 'Ethereum', 'sym' => 'ETH', 'id' => 'ethereum', 'color' => 'bg-indigo-600', 'icon' => 'fa-ethereum'],
                    ['name' => 'Solana', 'sym' => 'SOL', 'id' => 'solana', 'color' => 'bg-purple-500', 'icon' => 'fa-bolt'],
                    ['name' => 'XRP', 'sym' => 'XRP', 'id' => 'xrp', 'color' => 'bg-gray-800', 'icon' => 'fa-x']
                ];
            @endphp

            @foreach($assets as $asset)
            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 hover:border-primary/30 transition-all hover:bg-white hover:shadow-xl group">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 {{ $asset['color'] }} rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fab {{ $asset['icon'] }} text-2xl"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-[#010522]">{{ $asset['name'] }}</h5>
                            <span class="text-xs text-gray-500 font-medium">{{ $asset['sym'] }}</span>
                        </div>
                    </div>
                    <div class="h-8 w-20 bg-primary/5 rounded-md flex items-center justify-center">
                        <span class="text-[10px] font-bold text-primary uppercase">Trending</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-2xl font-bold text-[#010522]">$<span id="{{ $asset['id'] }}-price">---</span></div>
                    <div class="flex items-center gap-2 text-red-500 text-sm font-bold">
                        <i class="fa-solid fa-caret-down"></i>
                        <span>Live Data</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-24 bg-[#f8faff]">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-16 space-y-4">
            <h2 class="text-primary font-bold tracking-widest text-sm uppercase">Comprehensive Platform</h2>
            <h3 class="text-4xl md:text-5xl font-bold text-[#010522] leading-tight">
                All-in-one investment and trading solution for modern investors
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-chart-line text-primary text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-[#010522] mb-3">Investment Plans</h4>
                <p class="text-gray-600 leading-relaxed text-sm">
                    Flexible investment packages with competitive daily ROI. Choose from multiple tiers designed for every portfolio size.
                </p>
            </div>
            
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-robot text-primary text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-[#010522] mb-3">Automated Bot Trading</h4>
                <p class="text-gray-600 leading-relaxed text-sm">
                    AI-powered trading bots executing strategies 24/7. Choose from scalping or swing trading algorithms.
                </p>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-shield-alt text-primary text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-[#010522] mb-3">Bank-Grade Security</h4>
                <p class="text-gray-600 leading-relaxed text-sm">
                    Protected with military-grade encryption, two-factor authentication, and cold storage for digital assets.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-primary font-bold mb-4 uppercase tracking-widest text-sm">Investment Options</h2>
            <h3 class="text-4xl font-bold text-[#010522]">Tailored Plans for Every Strategy</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
            <div class="relative p-1 rounded-[2rem] bg-gradient-to-b from-gray-100 to-transparent hover:from-primary/40 transition-all group">
                <div class="bg-white p-8 rounded-[1.9rem] h-full flex flex-col">
                    <h4 class="text-xl font-bold text-gray-500 mb-2">{{ $plan->name }}</h4>
                    <div class="text-3xl font-bold text-[#010522] mb-6">
                        ${{ number_format($plan->min_price) }} <span class="text-sm font-medium text-gray-400">/min</span>
                    </div>
                    
                    <ul class="space-y-4 mb-8 flex-grow">
                        <li class="flex items-center gap-3 text-sm font-medium text-gray-600">
                            <i class="fa-solid fa-circle-check text-primary"></i>
                            Daily ROI: {{ $plan->increment_amount }}%
                        </li>
                        <li class="flex items-center gap-3 text-sm font-medium text-gray-600">
                            <i class="fa-solid fa-circle-check text-primary"></i>
                            Return: {{ $plan->expected_return }}%
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="w-full py-4 text-center rounded-xl bg-[#010522] text-white font-bold group-hover:bg-primary transition-colors">
                        Choose This Plan
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="px-6 py-12">
    <div class="container mx-auto bg-[#010522] rounded-[3rem] overflow-hidden relative">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-transparent"></div>
        <div class="flex flex-col lg:flex-row items-center px-8 lg:px-20 py-16 relative z-10">
            <div class="w-full lg:w-1/2 space-y-6 text-center lg:text-left">
                <h2 class="text-4xl md:text-6xl font-bold text-white leading-tight">
                    Start earning today <br> <span class="text-primary font-light">with {{ $settings->site_name }}</span>
                </h2>
                <p class="text-gray-400 text-lg">Join thousands of smart investors worldwide.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-3 text-white text-2xl font-bold hover:text-primary transition-all">
                    Create Free Account <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div class="w-full lg:w-1/2 mt-12 lg:mt-0 flex justify-center">
                 <img src="{{ asset('temp/assets/images/man-shades.webp') }}" class="w-full max-w-md object-contain" alt="Investor">
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-white border-y border-gray-100">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-12">
            <div class="text-center">
                <div class="text-4xl font-black text-[#010522] mb-1 counter" data-count="9430">0</div>
                <div class="text-primary font-bold text-xs uppercase tracking-widest">Satisfied Investors</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-[#010522] mb-1 counter" data-count="15">0</div>
                <div class="text-primary font-bold text-xs uppercase tracking-widest">Years Exp</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-[#010522] mb-1 counter" data-count="197">0</div>
                <div class="text-primary font-bold text-xs uppercase tracking-widest">Countries</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-[#010522] mb-1 counter" data-count="30467">0</div>
                <div class="text-primary font-bold text-xs uppercase tracking-widest">Transactions</div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Crypto Price Fetching
    async function fetchCryptoPrices() {
        try {
            const coinIds = 'bitcoin,ethereum,solana,ripple,avalanche,shiba-inu,tether,aave';
            const response = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${coinIds}&vs_currencies=usd`);
            const data = await response.json();

            if (Object.keys(data).length === 0) {
                console.error("API returned no data. Check coin IDs or API status.");
                return;
            }

            const formatPrice = (price) => {
                if (price === undefined) return 'N/A';
                // Handle small numbers like Shiba Inu correctly
                if (price < 1) {
                    return price.toFixed(8); // Use more decimal places for very small values
                }
                return price.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            };

            document.getElementById('bitcoin-price').textContent = formatPrice(data.bitcoin?.usd);
            document.getElementById('ethereum-price').textContent = formatPrice(data.ethereum?.usd);
            document.getElementById('solana-price').textContent = formatPrice(data.solana?.usd);
            document.getElementById('xrp-price').textContent = formatPrice(data.ripple?.usd);
            document.getElementById('avalanche-price').textContent = formatPrice(data.avalanche?.usd);
            document.getElementById('shiba-inu-price').textContent = formatPrice(data['shiba-inu']?.usd);
            document.getElementById('tether-price').textContent = formatPrice(data.tether?.usd);
            document.getElementById('aave-price').textContent = formatPrice(data.aave?.usd);

        } catch (error) {
            console.error('Error fetching crypto prices:', error);
        }
    }

    fetchCryptoPrices();
    setInterval(fetchCryptoPrices, 60000);

    // Counter Animation
    function animateCounters() {
        $('.counter').each(function() {
            var $this = $(this);
            var countTo = parseInt($this.attr('data-count'));

            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum).toLocaleString());
                },
                complete: function() {
                    $this.text(countTo.toLocaleString());
                }
            });
        });
    }

    function checkCounters() {
        const counterSection = $('.counter').first().closest('section');
        if (counterSection.length) {
            const sectionTop = counterSection.offset().top;
            const windowTop = $(window).scrollTop();
            const windowHeight = $(window).height();

            if (windowTop + windowHeight > sectionTop + 100) {
                animateCounters();
                $(window).off('scroll', checkCounters);
            }
        }
    }

    $(window).on('scroll', checkCounters);
});


document.getElementById('menu-toggle').onclick = function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.remove('-translate-y-full', 'opacity-0', 'pointer-events-none');
};

document.getElementById('close-menu').onclick = function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.add('-translate-y-full', 'opacity-0', 'pointer-events-none');
};
</script>

<!-- Alpine.js for potential interactive elements -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script src="//code.tidio.co/6jsrtbd0baan9h0fzzbbesslvf1wqrnx.js" async></script>
@endpush
