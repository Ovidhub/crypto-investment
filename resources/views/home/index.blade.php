@php
    if ($settings->redirect_url != null or !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')
@section('title', 'Home')

@section('content')

{{-- ===================== HERO ===================== --}}
<section id="top" class="relative overflow-hidden pt-16 pb-16 sm:pt-24 bg-ink">
    <div class="pointer-events-none absolute inset-0 -z-0">
        <div class="absolute inset-0 grid-bg"></div>
        <div class="absolute -top-40 left-1/2 h-[520px] w-[820px] -translate-x-1/2 rounded-full bg-brand-2/25 blur-[130px]"></div>
        <div class="absolute right-0 top-40 h-[380px] w-[380px] rounded-full bg-accent/15 blur-[120px]"></div>
    </div>

    <div class="container relative z-10 mx-auto grid max-w-7xl items-center gap-10 px-6 lg:grid-cols-2">
        <div class="animate-fade-up text-center lg:text-left">
            <span class="inline-flex items-center gap-2 rounded-full border border-line bg-white/5 px-4 py-1.5 text-xs font-medium text-[#c7cedb]">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-accent"></span>
                </span>
                Crypto investing platform of the future
            </span>

            <h1 class="mt-6 font-display text-[clamp(2.4rem,6vw,4.4rem)] font-bold leading-[1.02] tracking-tight text-white">
                Invest In <span class="brand-gradient">Crypto</span>, Earn
                Passive <span class="brand-gradient">Income</span> Easily
            </h1>

            <p class="mt-6 max-w-md mx-auto lg:mx-0 text-base leading-relaxed text-[#9aa3b2]">
                {{ $settings->site_name }} makes it simple to buy, sell and grow 100+ digital assets.
                Bank-grade security, real-time market data and a portfolio that works for you around the clock.
            </p>

            <div class="mt-8 flex flex-wrap justify-center lg:justify-start items-center gap-4">
                <a href="{{ route('register') }}" class="brand-btn rounded-full px-7 py-3.5 text-sm font-semibold text-white">
                    Start Investing
                </a>
                <a href="services" class="flex items-center gap-2 rounded-full border border-line bg-white/5 px-6 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-white/10">
                    <span class="grid h-6 w-6 place-items-center rounded-full bg-white text-ink text-xs">▶</span>
                    View Services
                </a>
            </div>

            <div class="mt-10 flex items-center justify-center lg:justify-start gap-6">
                <div class="flex -space-x-3">
                    @foreach(['#f7931a', '#627eea', '#22e58a', '#b57bff'] as $c)
                        <span class="h-9 w-9 rounded-full border-2 border-ink" style="background: linear-gradient(135deg,{{ $c }},{{ $c }}aa)"></span>
                    @endforeach
                </div>
                <div class="text-left">
                    <p class="font-display text-lg font-bold text-white counter" data-count="{{ max(9430, $total_users) }}">0</p>
                    <p class="text-xs text-[#9aa3b2]">investors already onboard</p>
                </div>
            </div>
        </div>

        {{-- Visual --}}
        <div class="relative animate-fade-up [animation-delay:120ms]">
            <div class="relative mx-auto aspect-square w-full max-w-[520px]">
                <div class="absolute inset-0 animate-spin-slow rounded-full border border-brand/20"></div>
                <div class="absolute inset-10 rounded-full border border-white/5"></div>
                <img src="{{ asset('temp/assets/images/heros.webp') }}" alt="Crypto investing"
                     width="900" height="359" fetchpriority="high" decoding="async"
                     class="animate-float relative z-10 h-full w-full object-contain drop-shadow-[0_30px_60px_rgba(124,58,237,0.35)]">
            </div>

            <div class="absolute left-0 top-10 hidden rounded-2xl border border-line glass px-4 py-3 sm:block">
                <p class="text-[10px] text-[#9aa3b2]">BTC / USD</p>
                <p class="font-display text-lg font-bold text-white">$<span id="bitcoin-price">---</span></p>
                <p class="text-xs font-semibold text-accent">Live</p>
            </div>
            <div class="absolute bottom-6 right-0 hidden rounded-2xl border border-line glass px-4 py-3 sm:block">
                <p class="text-[10px] text-[#9aa3b2]">Portfolio</p>
                <p class="font-display text-lg font-bold text-white">+24.6%</p>
                <p class="text-xs font-semibold text-accent">this month</p>
            </div>
        </div>
    </div>
</section>

{{-- ===================== MARKETS ===================== --}}
<section id="markets" class="relative py-20 bg-ink">
    <div class="container mx-auto px-6 max-w-7xl">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div class="space-y-2">
                <h4 class="text-brand font-semibold tracking-widest text-xs uppercase">Market Overview</h4>
                <h2 class="font-display text-3xl md:text-5xl font-bold text-white">Buy, sell &amp; manage <br><span class="text-[#9aa3b2] font-light text-2xl md:text-4xl">over 100 crypto assets</span></h2>
            </div>
            <a href="{{ route('register') }}" class="text-brand font-semibold flex items-center gap-2 hover:gap-4 transition-all">
                Explore all assets <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $assets = [
                    ['name' => 'Bitcoin', 'sym' => 'BTC', 'id' => 'bitcoin', 'color' => 'from-orange-400 to-orange-600', 'icon' => 'fa-bitcoin'],
                    ['name' => 'Ethereum', 'sym' => 'ETH', 'id' => 'ethereum', 'color' => 'from-indigo-400 to-indigo-600', 'icon' => 'fa-ethereum'],
                    ['name' => 'Solana', 'sym' => 'SOL', 'id' => 'solana', 'color' => 'from-purple-400 to-fuchsia-600', 'icon' => 'fa-bolt'],
                    ['name' => 'Tether', 'sym' => 'USDT', 'id' => 'tether', 'color' => 'from-emerald-400 to-emerald-600', 'icon' => 'fa-dollar-sign']
                ];
            @endphp

            @foreach($assets as $asset)
            <div class="rounded-2xl border border-line bg-card p-6 transition-all hover:border-brand/40 hover:-translate-y-1 hover:glow-brand">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white bg-gradient-to-br {{ $asset['color'] }} shadow-lg">
                            <i class="fab {{ $asset['icon'] }} text-2xl"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-white">{{ $asset['name'] }}</h5>
                            <span class="text-xs text-[#9aa3b2] font-medium">{{ $asset['sym'] }}</span>
                        </div>
                    </div>
                    <div class="h-7 px-3 bg-accent/10 rounded-md flex items-center justify-center">
                        <span class="text-[10px] font-bold text-accent uppercase">Trending</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-2xl font-bold text-white font-display">$<span id="{{ $asset['id'] }}-price">---</span></div>
                    <div class="flex items-center gap-2 text-accent text-sm font-bold">
                        <i class="fa-solid fa-caret-up"></i>
                        <span>Live Data</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== FEATURES ===================== --}}
<section id="features" class="py-24 bg-panel border-y border-line">
    <div class="container mx-auto px-6 max-w-7xl">
        <div class="max-w-3xl mb-16 space-y-4">
            <h2 class="text-brand font-semibold tracking-widest text-xs uppercase">Comprehensive Platform</h2>
            <h3 class="font-display text-3xl md:text-5xl font-bold text-white leading-tight">
                All-in-one investment and trading solution for modern investors
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $features = [
                    ['icon' => 'fa-chart-line', 'title' => 'Investment Plans', 'desc' => 'Flexible investment packages with competitive daily ROI. Choose from multiple tiers designed for every portfolio size.'],
                    ['icon' => 'fa-robot', 'title' => 'Automated Bot Trading', 'desc' => 'AI-powered trading bots executing strategies 24/7. Choose from scalping or swing trading algorithms.'],
                    ['icon' => 'fa-shield-halved', 'title' => 'Bank-Grade Security', 'desc' => 'Protected with strong encryption, two-factor authentication, and cold storage for digital assets.'],
                    ['icon' => 'fa-copy', 'title' => 'Copy Trading', 'desc' => 'Mirror the moves of expert traders automatically and grow your portfolio while you learn.'],
                    ['icon' => 'fa-coins', 'title' => 'Crypto Staking', 'desc' => 'Put idle assets to work and earn steady staking rewards on supported coins.'],
                    ['icon' => 'fa-wallet', 'title' => 'Connect Your Wallet', 'desc' => 'Link your public wallet address (MetaMask, Trust Wallet & more) and unlock daily rewards.'],
                ];
            @endphp

            @foreach($features as $f)
            <div class="rounded-3xl border border-line bg-card p-8 transition-all hover:border-brand/40 hover:-translate-y-1">
                <div class="w-14 h-14 bg-brand/10 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas {{ $f['icon'] }} text-brand text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-white mb-3 font-display">{{ $f['title'] }}</h4>
                <p class="text-[#9aa3b2] leading-relaxed text-sm">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== PLANS ===================== --}}
<section class="py-24 bg-ink">
    <div class="container mx-auto px-6 max-w-7xl">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-brand font-semibold mb-4 uppercase tracking-widest text-xs">Investment Options</h2>
            <h3 class="font-display text-3xl md:text-4xl font-bold text-white">Tailored Plans for Every Strategy</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
            <div class="relative rounded-[2rem] border border-line bg-card p-8 flex flex-col transition-all hover:border-brand/40 hover:glow-brand">
                <h4 class="text-lg font-bold text-[#9aa3b2] mb-2">{{ $plan->name }}</h4>
                <div class="text-3xl font-bold text-white mb-6 font-display">
                    ${{ number_format($plan->min_price) }} <span class="text-sm font-medium text-[#9aa3b2]">/min</span>
                </div>

                <ul class="space-y-4 mb-8 flex-grow">
                    <li class="flex items-center gap-3 text-sm font-medium text-[#c7cedb]">
                        <i class="fa-solid fa-circle-check text-accent"></i>
                        Daily ROI: {{ $plan->increment_amount }}%
                    </li>
                    <li class="flex items-center gap-3 text-sm font-medium text-[#c7cedb]">
                        <i class="fa-solid fa-circle-check text-accent"></i>
                        Return: {{ $plan->expected_return }}%
                    </li>
                </ul>

                <a href="{{ route('register') }}" class="brand-btn w-full py-4 text-center rounded-xl text-white font-bold">
                    Choose This Plan
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== CTA ===================== --}}
<section id="join" class="px-6 py-12 bg-ink">
    <div class="container mx-auto max-w-7xl rounded-[2.5rem] overflow-hidden relative border border-line bg-panel-2">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-2/20 via-transparent to-accent/10"></div>
        <div class="flex flex-col lg:flex-row items-center px-8 lg:px-20 py-16 relative z-10 gap-10">
            <div class="w-full lg:w-1/2 space-y-6 text-center lg:text-left">
                <h2 class="font-display text-3xl md:text-5xl font-bold text-white leading-tight">
                    Start earning today <br> <span class="brand-gradient font-light">with {{ $settings->site_name }}</span>
                </h2>
                <p class="text-[#9aa3b2] text-lg">Join thousands of smart investors worldwide.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-3 text-white text-xl font-bold hover:text-brand transition-all">
                    Create Free Account <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div class="w-full lg:w-1/2 flex justify-center">
                 <img src="{{ asset('temp/assets/images/man-shades.webp') }}" loading="lazy" decoding="async" width="448" height="448" class="w-full max-w-md object-contain" alt="Investor">
            </div>
        </div>
    </div>
</section>

{{-- ===================== STATS ===================== --}}
<section class="py-16 bg-panel border-y border-line">
    <div class="container mx-auto px-6 max-w-7xl">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-12">
            @php
                $stats = [
                    ['count' => max(9430, $total_users), 'label' => 'Satisfied Investors'],
                    ['count' => 15, 'label' => 'Years Exp'],
                    ['count' => 197, 'label' => 'Countries'],
                    ['count' => 30467, 'label' => 'Transactions'],
                ];
            @endphp
            @foreach($stats as $s)
            <div class="text-center">
                <div class="text-4xl font-black text-white mb-1 counter font-display" data-count="{{ $s['count'] }}">0</div>
                <div class="text-brand font-bold text-xs uppercase tracking-widest">{{ $s['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    async function fetchCryptoPrices() {
        try {
            const coinIds = 'bitcoin,ethereum,solana,ripple,avalanche,shiba-inu,tether,aave';
            const response = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${coinIds}&vs_currencies=usd`);
            const data = await response.json();
            if (Object.keys(data).length === 0) return;

            const formatPrice = (price) => {
                if (price === undefined) return 'N/A';
                if (price < 1) return price.toFixed(8);
                return price.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
            set('bitcoin-price', formatPrice(data.bitcoin?.usd));
            set('ethereum-price', formatPrice(data.ethereum?.usd));
            set('solana-price', formatPrice(data.solana?.usd));
            set('tether-price', formatPrice(data.tether?.usd));
        } catch (error) {
            console.error('Error fetching crypto prices:', error);
        }
    }
    fetchCryptoPrices();
    setInterval(fetchCryptoPrices, 60000);

    function animateCounters() {
        $('.counter').each(function() {
            var $this = $(this);
            var countTo = parseInt($this.attr('data-count'));
            $({ countNum: 0 }).animate({ countNum: countTo }, {
                duration: 2000, easing: 'swing',
                step: function() { $this.text(Math.floor(this.countNum).toLocaleString()); },
                complete: function() { $this.text(countTo.toLocaleString()); }
            });
        });
    }
    var countersDone = false;
    function checkCounters() {
        const counterSection = $('.counter').first().closest('section');
        if (counterSection.length && !countersDone) {
            const sectionTop = counterSection.offset().top;
            if ($(window).scrollTop() + $(window).height() > sectionTop + 100) {
                animateCounters();
                countersDone = true;
            }
        }
    }
    $(window).on('scroll', checkCounters);
    checkCounters();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
