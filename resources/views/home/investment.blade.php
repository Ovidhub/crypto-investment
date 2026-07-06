@extends('layouts.base')

@section('title', 'Investment Plans')

@inject('content', 'App\Http\Controllers\FrontController')
@section('content')

<section class="relative min-h-screen pb-10 w-full overflow-x-hidden bg-white">
    <div class="absolute inset-0 bg-gradient-to-b from-[#FFF3E0] via-white to-white opacity-60 -z-10"></div>
    <div class="absolute top-0 right-0 w-1/2 h-full bg-[#FF6B00] opacity-[0.03] skew-x-12 -z-10"></div>
    
    <div class="container mx-auto py-12 md:py-24 flex flex-col gap-6 px-4">
        <div class="mx-auto bg-[#FF6B00]/10 border border-[#FF6B00]/20 rounded-full px-4 py-1.5 mb-2">
            <span class="text-[#FF6B00] text-xs md:text-sm font-bold tracking-wider uppercase">Next Gen Trading</span>
        </div>

        <h1 class="text-center text-4xl md:text-7xl poppins-medium max-w-5xl mx-auto leading-[1.1] text-[#010522]">
            Complete Investment & Trading <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF6B00] to-[#FF9E40]">Ecosystem</span>
        </h1>
        
        <h3 class="text-center text-lg md:text-2xl text-gray-600 poppins-light max-w-4xl mx-auto leading-relaxed">
            Investment Plans • Copy Trading • Bot Trading • Stock Trading • Crypto Trading • Staking
        </h3>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-4">
            <a href="{{ route('register') }}" class="w-full sm:w-auto text-center rounded-full px-8 py-4 bg-[#FF6B00] text-white font-semibold shadow-lg shadow-[#FF6B00]/30 hover:bg-[#E65100] hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                Start Investing
            </a>
            <a href="{{ url('about') }}" class="w-full sm:w-auto text-center rounded-full px-8 py-4 bg-white text-[#010522] border border-gray-200 font-semibold hover:border-[#FF6B00] hover:text-[#FF6B00] transition-colors duration-300">
                Explore Features
            </a>
        </div>

        <div class="mt-8 mb-4 flex justify-center">
             
        </div>

        <div class="mt-8 relative group perspective-1000">
            <div class="absolute -inset-1 bg-gradient-to-r from-[#FF6B00] to-[#FF9E40] rounded-[2rem] blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <img src="{{ asset('temp/assets/images/heros.png') }}" alt="Investment Platform" class="relative mx-auto max-w-full h-auto rounded-[1.5rem] shadow-2xl border-4 border-white/50 backdrop-blur-sm transform group-hover:scale-[1.01] transition-transform duration-500">
        </div>
    </div>
</section>

<section class="manrope text-sm relative py-16 md:py-28 w-full h-fit overflow-hidden bg-[#FAFAFA]">
    <img src="{{ asset('temp/assets/images/assets-blob-1.svg') }}" alt="" class="absolute top-0 left-0 w-[400px] md:w-[500px] opacity-10 hue-rotate-[160deg] saturate-200"> 
    <img src="{{ asset('temp/assets/images/assets-blob-2.svg') }}" alt="" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] md:w-[600px] -z-10 opacity-10 hue-rotate-[160deg] saturate-200">
    
    <div class="container mx-auto px-4 md:px-8 lg:px-32 relative z-10">
        <div class="mb-16">
            <h2 class="text-sm font-bold text-[#FF6B00] mb-3 tracking-widest uppercase">ALL-IN-ONE PLATFORM</h2>
            <h1 class="text-4xl md:text-5xl poppins-light text-[#010522] leading-tight">Seven powerful ways to</h1>
            <h1 class="text-4xl md:text-5xl poppins-medium text-[#010522] leading-tight"><span class="border-b-4 border-[#FF6B00]">grow your wealth</span></h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-14">
            <div class="flex flex-col md:flex-row gap-5 items-start group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-[#FFF3E0] rounded-2xl flex items-center justify-center group-hover:bg-[#FF6B00] transition-colors duration-300">
                        <i class="fas fa-chart-line text-[#FF6B00] text-2xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Investment Plans</h3>
                    <p class="text-gray-500 leading-relaxed">Flexible plans with daily/weekly ROI distribution. Choose from starter to premium tiers with automated profit calculations and instant withdrawals.</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-5 items-start group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-[#FFF3E0] rounded-2xl flex items-center justify-center group-hover:bg-[#FF6B00] transition-colors duration-300">
                        <i class="fas fa-users text-[#FF6B00] text-2xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Copy Trading</h3>
                    <p class="text-gray-500 leading-relaxed">Follow expert traders and automatically replicate their strategies. Track performance, win rates, and ROI before subscribing to top performers.</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-5 items-start group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-[#FFF3E0] rounded-2xl flex items-center justify-center group-hover:bg-[#FF6B00] transition-colors duration-300">
                        <i class="fas fa-robot text-[#FF6B00] text-2xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Bot Trading</h3>
                    <p class="text-gray-500 leading-relaxed">Automated trading bots execute strategies 24/7. Configure duration, monitor real-time trades, and earn consistent returns without manual intervention.</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-5 items-start group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-[#FFF3E0] rounded-2xl flex items-center justify-center group-hover:bg-[#FF6B00] transition-colors duration-300">
                        <i class="fas fa-chart-bar text-[#FF6B00] text-2xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Stock Trading</h3>
                    <p class="text-gray-500 leading-relaxed">Trade global stocks with real-time quotes. Execute market, limit, and stop orders with advanced portfolio management and watchlist features.</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-5 items-start group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-[#FFF3E0] rounded-2xl flex items-center justify-center group-hover:bg-[#FF6B00] transition-colors duration-300">
                        <i class="fab fa-bitcoin text-[#FF6B00] text-2xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Cryptocurrency Trading</h3>
                    <p class="text-gray-500 leading-relaxed">Trade 100+ cryptocurrencies with instant execution. Advanced order types, custom watchlists, and comprehensive portfolio tracking included.</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-5 items-start group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-[#FFF3E0] rounded-2xl flex items-center justify-center group-hover:bg-[#FF6B00] transition-colors duration-300">
                        <i class="fas fa-coins text-[#FF6B00] text-2xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Crypto Staking</h3>
                    <p class="text-gray-500 leading-relaxed">Earn passive rewards by staking cryptocurrencies. Flexible and locked plans with competitive APR, compound earnings, and hourly/daily rewards.</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-5 items-start group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-[#FFF3E0] rounded-2xl flex items-center justify-center group-hover:bg-[#FF6B00] transition-colors duration-300">
                        <i class="fas fa-building text-[#FF6B00] text-2xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Stock Investment</h3>
                    <p class="text-gray-500 leading-relaxed">Long-term stock investment plans targeting major corporations. Systematic ROI distribution with transparent performance tracking and low fees.</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-5 items-start group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-[#FFF3E0] rounded-2xl flex items-center justify-center group-hover:bg-[#FF6B00] transition-colors duration-300">
                        <i class="fas fa-shield-alt text-[#FF6B00] text-2xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Bank-Grade Security</h3>
                    <p class="text-gray-500 leading-relaxed">Multi-layer security with 2FA authentication, encrypted transactions, cold storage, and KYC verification protecting every investment.</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-5 items-start group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-[#FFF3E0] rounded-2xl flex items-center justify-center group-hover:bg-[#FF6B00] transition-colors duration-300">
                        <i class="fas fa-cogs text-[#FF6B00] text-2xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Full Automation</h3>
                    <p class="text-gray-500 leading-relaxed">Automated profit distribution, trade execution, and reward calculations. Set your strategy once and let the system work for you.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h4 class="text-[#FF6B00] text-sm font-bold mb-3 tracking-[0.2em] uppercase">PLATFORM CAPABILITIES</h4>
            <h2 class="text-3xl md:text-5xl poppins-light text-[#010522]">Everything You Need</h2>
            <h2 class="text-3xl md:text-5xl poppins-medium text-[#010522] mb-4">in One <span class="text-[#FF6B00]">Ecosystem</span></h2>
            <p class="text-gray-500 max-w-2xl mx-auto mt-4 text-lg">Comprehensive investment and trading tools designed for both beginners and professional investors seeking consistent returns.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
            <div class="group bg-gradient-to-br from-[#FFF3E0] to-white rounded-3xl p-8 border border-[#FF6B00]/10 hover:border-[#FF6B00] shadow-sm hover:shadow-2xl hover:shadow-[#FF6B00]/10 transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-16 h-16 bg-[#FF6B00] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#FF6B00]/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-chart-line text-white text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl poppins-medium text-[#010522] mb-1">Investment Plans</h3>
                        <p class="text-[#FF6B00] font-semibold text-sm tracking-wide">Daily/Weekly ROI • Multiple Tiers</p>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">Subscribe to flexible investment plans with configurable durations and returns. Our automated system calculates and distributes profits daily or weekly based on your plan's ROI percentage.</p>
                <div class="border-t border-[#FF6B00]/10 pt-4">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Min/Max investment ranges</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Expected return tracking</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Transparent profit history</li>
                    </ul>
                </div>
            </div>

            <div class="group bg-gradient-to-br from-[#FFF3E0] to-white rounded-3xl p-8 border border-[#FF6B00]/10 hover:border-[#FF6B00] shadow-sm hover:shadow-2xl hover:shadow-[#FF6B00]/10 transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-16 h-16 bg-[#FF6B00] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#FF6B00]/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-white text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl poppins-medium text-[#010522] mb-1">Copy Trading</h3>
                        <p class="text-[#FF6B00] font-semibold text-sm tracking-wide">Expert Traders • Win Rate Tracking</p>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">Follow expert traders and automatically mirror their strategies. Browse trader profiles with performance metrics including ROI percentage, win rate, and active trades. Set your capital limits.</p>
                <div class="border-t border-[#FF6B00]/10 pt-4">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Verified expert traders</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Performance leaderboards</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Capacity management system</li>
                    </ul>
                </div>
            </div>

            <div class="group bg-gradient-to-br from-[#FFF3E0] to-white rounded-3xl p-8 border border-[#FF6B00]/10 hover:border-[#FF6B00] shadow-sm hover:shadow-2xl hover:shadow-[#FF6B00]/10 transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-16 h-16 bg-[#FF6B00] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#FF6B00]/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-robot text-white text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl poppins-medium text-[#010522] mb-1">Bot Trading</h3>
                        <p class="text-[#FF6B00] font-semibold text-sm tracking-wide">24/7 Automation • Multiple Strategies</p>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">Deploy automated trading bots that execute strategies around the clock. Configure duration from 1-90 days, monitor real-time trades, and earn returns without manual intervention.</p>
                <div class="border-t border-[#FF6B00]/10 pt-4">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Pre-configured strategies</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Trade history tracking</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Flexible duration options</li>
                    </ul>
                </div>
            </div>

            <div class="group bg-gradient-to-br from-[#FFF3E0] to-white rounded-3xl p-8 border border-[#FF6B00]/10 hover:border-[#FF6B00] shadow-sm hover:shadow-2xl hover:shadow-[#FF6B00]/10 transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-16 h-16 bg-[#FF6B00] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#FF6B00]/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-chart-bar text-white text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl poppins-medium text-[#010522] mb-1">Stock Trading</h3>
                        <p class="text-[#FF6B00] font-semibold text-sm tracking-wide">Real-Time Quotes • Advanced Orders</p>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">Trade global stocks with real-time price data integration. Execute market, limit, and stop orders with custom expiry options. Manage watchlists and track portfolio performance.</p>
                <div class="border-t border-[#FF6B00]/10 pt-4">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> TwelveData API integration</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Multiple order types</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Holdings & P/L tracking</li>
                    </ul>
                </div>
            </div>

            <div class="group bg-gradient-to-br from-[#FFF3E0] to-white rounded-3xl p-8 border border-[#FF6B00]/10 hover:border-[#FF6B00] shadow-sm hover:shadow-2xl hover:shadow-[#FF6B00]/10 transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-16 h-16 bg-[#FF6B00] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#FF6B00]/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-bitcoin text-white text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl poppins-medium text-[#010522] mb-1">Crypto Trading</h3>
                        <p class="text-[#FF6B00] font-semibold text-sm tracking-wide">100+ Coins • Instant Execution</p>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">Trade a wide range of cryptocurrencies with instant order execution. Create custom watchlists, monitor price movements, and execute trades with market, limit, or stop orders.</p>
                <div class="border-t border-[#FF6B00]/10 pt-4">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Major crypto pairs</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Real-time price updates</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Transaction fee transparency</li>
                    </ul>
                </div>
            </div>

            <div class="group bg-gradient-to-br from-[#FFF3E0] to-white rounded-3xl p-8 border border-[#FF6B00]/10 hover:border-[#FF6B00] shadow-sm hover:shadow-2xl hover:shadow-[#FF6B00]/10 transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-16 h-16 bg-[#FF6B00] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#FF6B00]/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-coins text-white text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl poppins-medium text-[#010522] mb-1">Crypto Staking</h3>
                        <p class="text-[#FF6B00] font-semibold text-sm tracking-wide">Competitive APR • Compound Earnings</p>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">Earn passive rewards by staking cryptocurrencies. Choose flexible or locked staking plans with durations from 30 days to 1 year. Auto-compound earnings for maximum returns.</p>
                <div class="border-t border-[#FF6B00]/10 pt-4">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Multiple lock durations</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Compound interest option</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Early withdrawal penalties</li>
                    </ul>
                </div>
            </div>

            <div class="group bg-gradient-to-br from-[#FFF3E0] to-white rounded-3xl p-8 border border-[#FF6B00]/10 hover:border-[#FF6B00] shadow-sm hover:shadow-2xl hover:shadow-[#FF6B00]/10 transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-16 h-16 bg-[#FF6B00] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#FF6B00]/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-building text-white text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl poppins-medium text-[#010522] mb-1">Stock Investment</h3>
                        <p class="text-[#FF6B00] font-semibold text-sm tracking-wide">Blue Chip Stocks • Systematic ROI</p>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">Invest in curated stock plans targeting major corporations. Long-term investment approach with systematic ROI distribution. Transparent performance tracking with detailed company information.</p>
                <div class="border-t border-[#FF6B00]/10 pt-4">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Major corporation focus</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Systematic returns</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Low transaction fees</li>
                    </ul>
                </div>
            </div>

            <div class="group bg-gradient-to-br from-[#FFF3E0] to-white rounded-3xl p-8 border border-[#FF6B00]/10 hover:border-[#FF6B00] shadow-sm hover:shadow-2xl hover:shadow-[#FF6B00]/10 transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-16 h-16 bg-[#FF6B00] rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#FF6B00]/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-shield-check text-white text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl poppins-medium text-[#010522] mb-1">Security & Automation</h3>
                        <p class="text-[#FF6B00] font-semibold text-sm tracking-wide">2FA • KYC Verification • Automated Jobs</p>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">Enterprise-grade security protecting all investments. Two-factor authentication, KYC verification, encrypted transactions, and automated scheduled jobs for profit distribution.</p>
                <div class="border-t border-[#FF6B00]/10 pt-4">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Multi-layer security</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Background job processing</li>
                        <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-[#FF6B00]/10 flex items-center justify-center"><i class="fas fa-check text-[#FF6B00] text-xs"></i></div> Instant notifications</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-[#FFF3E0] rounded-bl-full -z-10 opacity-50"></div>

    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h4 class="text-[#FF6B00] text-sm font-bold mb-3 tracking-[0.2em] uppercase">INVESTMENT OPTIONS</h4>
            <h2 class="text-3xl md:text-5xl poppins-light text-[#010522]">Choose Your</h2>
            <h2 class="text-3xl md:text-5xl poppins-medium text-[#010522] mb-4">Investment Strategy</h2>
            <p class="text-gray-500 max-w-2xl mx-auto mt-4 text-lg">Start with any plan and diversify across multiple investment vehicles for maximum returns.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
            @foreach($plans as $plan)
                <div class="relative group bg-white rounded-3xl shadow-lg hover:shadow-2xl hover:shadow-[#FF6B00]/10 border border-gray-100 transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                    <div class="h-2 bg-gradient-to-r from-[#FF6B00] to-[#FF9E40]"></div>
                    
                    <div class="p-8 text-center">
                        <div class="mb-4 inline-block p-3 rounded-full bg-[#FFF3E0] text-[#FF6B00] group-hover:bg-[#FF6B00] group-hover:text-white transition-colors duration-300">
                             <i class="fas fa-layer-group text-2xl"></i>
                        </div>

                        <h3 class="text-2xl poppins-medium text-[#010522] mb-2">{{ $plan->name }}</h3>
                        
                        <div class="text-3xl font-bold text-[#FF6B00] mb-2 tracking-tight">
                            ${{ number_format($plan->min_price, 2) }} <span class="text-gray-400 text-lg font-normal">-</span> ${{ number_format($plan->max_price, 2) }}
                        </div>

                        <div class="text-gray-400 text-sm mb-8 font-medium tracking-wide uppercase">Min &mdash; Max Investment</div>

                        <div class="bg-[#FFF3E0]/50 border border-[#FF6B00]/10 rounded-2xl p-6 mb-8">
                            <ul class="text-gray-600 text-sm space-y-4">
                                <li class="flex items-center justify-between border-b border-[#FF6B00]/5 pb-3 last:border-0 last:pb-0">
                                    <span class="font-medium text-[#010522]">Daily ROI</span>
                                    <span class="text-[#FF6B00] font-bold bg-[#FF6B00]/10 px-2 py-1 rounded">{{ $plan->increment_amount }}%</span>
                                </li>
                                <li class="flex items-center justify-between">
                                    <span class="font-medium text-[#010522]">Expected Return</span>
                                    <span class="text-[#FF6B00] font-bold bg-[#FF6B00]/10 px-2 py-1 rounded">{{ $plan->expected_return }}%</span>
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('register') }}" class="inline-block w-full px-6 py-4 bg-[#FF6B00] text-white rounded-xl font-semibold hover:bg-[#E65100] transition-all shadow-lg shadow-[#FF6B00]/30 hover:shadow-xl hover:shadow-[#FF6B00]/40">
                            Get Started Now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="manrope text-sm relative py-20 w-full h-fit overflow-hidden bg-gradient-to-b from-white to-[#FFF3E0]/50">
    <img src="{{ asset('temp/assets/images/assets-blob-1.svg') }}" alt="" class="absolute bottom-0 right-0 w-[400px] md:w-[500px] opacity-10 hue-rotate-[160deg] saturate-200">
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-sm font-bold text-[#FF6B00] mb-3 tracking-widest uppercase">GETTING STARTED</h2>
                <h1 class="text-3xl md:text-5xl poppins-light text-[#010522]">How the Platform</h1>
                <h1 class="text-3xl md:text-5xl poppins-medium text-[#010522] mb-4">Works for You</h1>
                <p class="text-gray-500 max-w-2xl mx-auto text-lg">Start earning across multiple investment channels in four simple steps</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1 relative group">
                    <div class="absolute -inset-2 bg-[#FF6B00] rounded-2xl opacity-20 blur-lg group-hover:opacity-30 transition duration-500"></div>
                    <img src="assets/img/service/single-service-1.jpg" alt="Investment Platform" class="relative w-full h-auto rounded-2xl shadow-2xl border border-gray-100">
                </div>

                <div class="order-1 lg:order-2 space-y-8">
                    <div class="flex items-start gap-6 group">
                        <div class="flex-shrink-0 relative">
                            <div class="w-14 h-14 bg-[#FF6B00] rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-[#FF6B00]/30 group-hover:scale-110 transition-transform duration-300">1</div>
                            <div class="absolute h-full w-0.5 bg-[#FF6B00]/20 left-1/2 -translate-x-1/2 top-14 hidden md:block"></div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Create & Verify Account</h3>
                            <p class="text-gray-500 leading-relaxed">Sign up in minutes with email verification. Complete KYC for full access to all trading and investment features with enhanced security.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-6 group">
                        <div class="flex-shrink-0 relative">
                            <div class="w-14 h-14 bg-[#FFF3E0] text-[#FF6B00] rounded-2xl flex items-center justify-center font-bold text-xl border border-[#FF6B00]/20 group-hover:bg-[#FF6B00] group-hover:text-white transition-all duration-300">2</div>
                            <div class="absolute h-full w-0.5 bg-[#FF6B00]/20 left-1/2 -translate-x-1/2 top-14 hidden md:block"></div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Fund Your Account</h3>
                            <p class="text-gray-500 leading-relaxed">Deposit funds via cryptocurrency, bank transfer, or card payment. Multiple payment gateways ensure fast, secure transactions with instant confirmation.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-6 group">
                        <div class="flex-shrink-0 relative">
                            <div class="w-14 h-14 bg-[#FFF3E0] text-[#FF6B00] rounded-2xl flex items-center justify-center font-bold text-xl border border-[#FF6B00]/20 group-hover:bg-[#FF6B00] group-hover:text-white transition-all duration-300">3</div>
                            <div class="absolute h-full w-0.5 bg-[#FF6B00]/20 left-1/2 -translate-x-1/2 top-14 hidden md:block"></div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Choose Your Strategy</h3>
                            <p class="text-gray-500 leading-relaxed">Select from investment plans, copy top traders, deploy trading bots, trade stocks/crypto, stake assets, or combine multiple strategies for diversification.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-6 group">
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 bg-[#FFF3E0] text-[#FF6B00] rounded-2xl flex items-center justify-center font-bold text-xl border border-[#FF6B00]/20 group-hover:bg-[#FF6B00] group-hover:text-white transition-all duration-300">4</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#010522] mb-2 group-hover:text-[#FF6B00] transition-colors">Monitor & Withdraw Profits</h3>
                            <p class="text-gray-500 leading-relaxed">Track real-time performance across all investments. Withdraw profits anytime to your wallet, reinvest for compound growth, or reallocate to different strategies.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="overflow-hidden bg-white">
    <div class="p-5 md:p-20">
        <div class="relative px-5 md:px-10 lg:px-20 pb-10 md:pb-20 py-10 md:py-16 manrope text-sm text-white container mx-auto flex flex-col md:flex-row gap-8 md:gap-5 items-center md:items-end justify-between w-full rounded-3xl shadow-2xl overflow-hidden" style="background: linear-gradient(226deg, #010522 50.68%, #FF6B00 100%);">
            
            <div class="absolute top-0 right-0 w-full h-full bg-[url('{{ asset('temp/assets/images/pattern.svg') }}')] opacity-10 mix-blend-overlay"></div>
            
            <div class="w-full md:flex-1 space-y-6 z-10">
                <div class="text-3xl md:text-4xl lg:text-5xl xl:text-6xl poppins-medium text-left leading-tight">
                    Start your wealth journey 
                    <div class="poppins-light mt-2 text-[#FF9E40]">with {{$settings->site_name}}</div>
                </div>
                <p class="text-lg md:text-xl text-gray-300 max-w-xl">Join thousands of investors earning consistent returns through diversified investment strategies across seven powerful platforms.</p>
                <a href="{{ route('register') }}" class="group inline-flex items-center gap-3 py-3 text-white text-xl md:text-2xl font-semibold transition-all hover:text-[#FF9E40]">
                    Create Free Account 
                    <i class="fa-solid fa-arrow-right-long transition-transform group-hover:translate-x-2"></i>
                </a>
            </div>
            
            <div class="relative w-full md:flex-1 flex justify-center md:justify-end min-h-[300px] md:min-h-[400px]">
                <img src="{{ asset('temp/assets/images/man-shades.webp') }}" alt="Happy Investor" class="w-[280px] md:w-[400px] lg:w-[500px] z-20 relative object-contain drop-shadow-2xl">
                <img src="{{ asset('temp/assets/images/not.webp') }}" alt="Profit Notification" class="w-[120px] md:w-[200px] lg:w-[300px] absolute top-[20%] md:top-1/2 right-0 md:right-[-50px] lg:right-[-80px] -translate-y-1/2 z-[30] object-contain animate-pulse">
                <img src="{{ asset('temp/assets/images/smaller-not.webp') }}" alt="Investment Preview" class="w-[100px] md:w-[160px] lg:w-[250px] absolute top-[60%] md:top-1/2 right-[20px] md:right-[10px] translate-y-[-20%] md:translate-y-[20px] z-[22] object-contain">
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-[#FF6B00] text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-5xl poppins-medium mb-3">Platform Statistics</h2>
            <p class="text-white/80 text-lg">Real numbers from real investors</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 divide-x divide-white/20">
            <div class="text-center pl-4 first:pl-0">
                <div class="text-4xl md:text-6xl font-bold mb-2 tracking-tight">
                    <span class="counter" data-count="9430">0</span>+
                </div>
                <p class="text-sm md:text-lg font-medium text-white/90 uppercase tracking-wide">Active Investors</p>
            </div>

            <div class="text-center pl-4">
                <div class="text-4xl md:text-6xl font-bold mb-2 tracking-tight">
                    $<span class="counter" data-count="338">0</span>M+
                </div>
                <p class="text-sm md:text-lg font-medium text-white/90 uppercase tracking-wide">Total Invested</p>
            </div>

            <div class="text-center pl-4">
                <div class="text-4xl md:text-6xl font-bold mb-2 tracking-tight">
                    $<span class="counter" data-count="101">0</span>M+
                </div>
                <p class="text-sm md:text-lg font-medium text-white/90 uppercase tracking-wide">Total Withdrawn</p>
            </div>

            <div class="text-center pl-4">
                <div class="text-4xl md:text-6xl font-bold mb-2 tracking-tight">
                    <span class="counter" data-count="2145">0</span>+
                </div>
                <p class="text-sm md:text-lg font-medium text-white/90 uppercase tracking-wide">Days in Operation</p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
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
</script>
@endpush
