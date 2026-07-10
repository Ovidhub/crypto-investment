@php
    if ($settings->redirect_url != null or !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')
@inject('content', 'App\Http\Controllers\FrontController')
@section('title', 'About Us')


@section('content')

    <main class="flex-grow font-outfit">
        <div class="animate-fade-in">
            
            <section class="relative bg-[#0f172a] py-24 md:py-32 overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-orange-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

                <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="max-w-4xl mx-auto text-center">
                        <span class="inline-block py-1 px-3 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-sm font-semibold mb-6 tracking-wider uppercase">
                            Premium Financial Services
                        </span>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                            Trusted Investment Platform with <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600">{{$settings->site_name}}</span>
                        </h1>
                        
                        <div class="mb-10 max-w-2xl mx-auto">
                            <h2 class="text-xl md:text-2xl font-semibold text-slate-300 mb-4">About Us</h2>
                            <p class="text-lg text-slate-400">COMPREHENSIVE TRADING & WEALTH MANAGEMENT SOLUTIONS</p>
                        </div>

                        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-8 md:p-10 text-left shadow-2xl">
                            <h3 class="text-xl md:text-2xl font-bold text-white mb-4 flex items-center gap-3">
                                <div class="w-1 h-8 bg-orange-500 rounded-full"></div>
                                <div>
                                    {{$settings->site_name}} Ecosystem
                                    <span class="block text-sm font-normal text-orange-400 mt-1">DIVERSIFIED TRADING & WEALTH MANAGEMENT</span>
                                </div>
                            </h3>
                            <p class="text-slate-300 leading-relaxed mb-6">
                                {{$settings->site_name}} is a comprehensive financial platform offering multiple investment avenues: flexible investment plans with competitive daily ROI, cryptocurrency trading across 100+ digital assets, stock market investments in global companies, automated AI-powered bot trading, professional copy trading systems, and lucrative crypto staking programs.
                            </p>
                            <p class="text-slate-300 leading-relaxed">
                                Whether you're a beginner seeking passive income through investment plans or an experienced trader looking for advanced tools in cryptocurrency, stocks, and automated strategies, our platform provides the technology, security, and support to achieve your financial goals.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-20 bg-white relative">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-7xl mx-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                            
                            <div class="space-y-8">
                                <div class="group bg-white rounded-2xl p-8 border border-slate-100 shadow-xl shadow-slate-200/50 hover:border-orange-500/30 transition-all duration-300">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-14 h-14 bg-orange-50 rounded-xl flex items-center justify-center group-hover:bg-orange-500 transition-colors duration-300">
                                            <i class="fas fa-robot text-orange-600 text-2xl group-hover:text-white transition-colors"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-bold text-slate-900">Automated Bot Trading</h4>
                                            <p class="text-orange-600 font-medium">AI-Powered Trading Systems</p>
                                        </div>
                                    </div>
                                    <ul class="space-y-3">
                                        <li class="flex items-start text-slate-600">
                                            <i class="fas fa-check-circle text-orange-500 mt-1 mr-3"></i>
                                            <span>24/7 automated trading execution without human intervention</span>
                                        </li>
                                        <li class="flex items-start text-slate-600">
                                            <i class="fas fa-check-circle text-orange-500 mt-1 mr-3"></i>
                                            <span>Multiple AI algorithms: Scalping, Day Trading, Swing Trading</span>
                                        </li>
                                        <li class="flex items-start text-slate-600">
                                            <i class="fas fa-check-circle text-orange-500 mt-1 mr-3"></i>
                                            <span>Consistent passive income generation</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="group bg-white rounded-2xl p-8 border border-slate-100 shadow-xl shadow-slate-200/50 hover:border-orange-500/30 transition-all duration-300">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-14 h-14 bg-orange-50 rounded-xl flex items-center justify-center group-hover:bg-orange-500 transition-colors duration-300">
                                            <i class="fas fa-users text-orange-600 text-2xl group-hover:text-white transition-colors"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-bold text-slate-900">Copy Trading System</h4>
                                            <p class="text-orange-600 font-medium">Follow Expert Traders</p>
                                        </div>
                                    </div>
                                    <ul class="space-y-3">
                                        <li class="flex items-start text-slate-600">
                                            <i class="fas fa-check-circle text-orange-500 mt-1 mr-3"></i>
                                            <span>Automatically replicate trades from professional traders</span>
                                        </li>
                                        <li class="flex items-start text-slate-600">
                                            <i class="fas fa-check-circle text-orange-500 mt-1 mr-3"></i>
                                            <span>Choose from top-performing experts with verified track records</span>
                                        </li>
                                        <li class="flex items-start text-slate-600">
                                            <i class="fas fa-check-circle text-orange-500 mt-1 mr-3"></i>
                                            <span>No trading experience required - let experts work for you</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-3xl font-bold text-slate-900 mb-6">
                                    Everything In <br>
                                    <span class="text-orange-600">One Platform</span>
                                </h3>

                                <p class="text-slate-600 leading-relaxed mb-8 text-lg">
                                    {{$settings->site_name}} brings investment tools, cryptocurrency and stock trading, automated and copy trading, and staking together in one place. Cryptocurrency and trading are high-risk activities and are not covered by deposit-protection or investor-compensation schemes. Please review our Terms and Risk Disclosure before using any service.
                                </p>

                                <div class="bg-slate-50 rounded-2xl p-8 border border-slate-100">
                                    <h4 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-200 pb-2">Full Platform Access</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex items-center text-slate-700">
                                            <i class="fas fa-check text-orange-500 mr-2"></i> Investment Plans
                                        </div>
                                        <div class="flex items-center text-slate-700">
                                            <i class="fas fa-check text-orange-500 mr-2"></i> Crypto Trading
                                        </div>
                                        <div class="flex items-center text-slate-700">
                                            <i class="fas fa-check text-orange-500 mr-2"></i> Stock Market
                                        </div>
                                        <div class="flex items-center text-slate-700">
                                            <i class="fas fa-check text-orange-500 mr-2"></i> AI Bot Trading
                                        </div>
                                        <div class="flex items-center text-slate-700">
                                            <i class="fas fa-check text-orange-500 mr-2"></i> Copy Trading
                                        </div>
                                        <div class="flex items-center text-slate-700">
                                            <i class="fas fa-check text-orange-500 mr-2"></i> Staking Programs
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <section class="py-16 bg-slate-900 text-center">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-4xl mx-auto">
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                            Multi-Asset <span class="text-orange-500">Trading Platform</span>
                        </h2>
                        <p class="text-lg text-slate-400 leading-relaxed">
                            {{$settings->site_name}} provides comprehensive access to global stock markets, cryptocurrency exchanges, and staking programs. Trade top stocks like Apple, Tesla, Amazon, and Microsoft with real-time TradingView integration. Access 100+ cryptocurrencies including Bitcoin, Ethereum, and emerging altcoins with advanced charting tools.
                        </p>
                    </div>
                </div>
            </section>

            <section class="py-20 bg-slate-50">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                            Comprehensive <span class="text-orange-600">Investment Solutions</span>
                        </h2>
                        <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                            {{$settings->site_name}} offers distinct investment and trading services designed to maximize returns while providing flexibility.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-2xl p-8 shadow-lg hover:-translate-y-2 transition-transform duration-300 border-b-4 border-transparent hover:border-orange-500">
                            <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6">
                                <i class="fas fa-chart-line text-orange-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Investment Plans</h3>
                            <p class="text-slate-600 mb-4 text-sm leading-relaxed">Flexible investment packages offering competitive daily ROI. Multiple tiers from starter to premium, designed for every portfolio size.</p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Daily ROI</span>
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Automated</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-8 shadow-lg hover:-translate-y-2 transition-transform duration-300 border-b-4 border-transparent hover:border-orange-500">
                            <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6">
                                <i class="fab fa-bitcoin text-orange-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Crypto Trading</h3>
                            <p class="text-slate-600 mb-4 text-sm leading-relaxed">Trade 100+ cryptocurrencies including Bitcoin & Ethereum. Access spot, margin, and futures markets with real-time data.</p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Spot/Margin</span>
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">100+ Assets</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-8 shadow-lg hover:-translate-y-2 transition-transform duration-300 border-b-4 border-transparent hover:border-orange-500">
                            <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6">
                                <i class="fas fa-chart-candlestick text-orange-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Stock Market</h3>
                            <p class="text-slate-600 mb-4 text-sm leading-relaxed">Invest in top global stocks like Apple, Tesla, and Amazon. Live TradingView integration and real-time quotes.</p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Global Stocks</span>
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">TradingView</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-8 shadow-lg hover:-translate-y-2 transition-transform duration-300 border-b-4 border-transparent hover:border-orange-500">
                            <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6">
                                <i class="fas fa-users text-orange-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Copy Trading</h3>
                            <p class="text-slate-600 mb-4 text-sm leading-relaxed">Follow and automatically replicate trades from expert traders. Benefit from professional strategies without deep market knowledge.</p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Auto-Replicate</span>
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Experts</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-8 shadow-lg hover:-translate-y-2 transition-transform duration-300 border-b-4 border-transparent hover:border-orange-500">
                            <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6">
                                <i class="fas fa-robot text-orange-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">AI Bot Trading</h3>
                            <p class="text-slate-600 mb-4 text-sm leading-relaxed">AI-powered trading bots executing strategies 24/7. Choose from scalping or swing trading algorithms.</p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">24/7 Uptime</span>
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">AI Powered</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-8 shadow-lg hover:-translate-y-2 transition-transform duration-300 border-b-4 border-transparent hover:border-orange-500">
                            <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6">
                                <i class="fas fa-coins text-orange-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Crypto Staking</h3>
                            <p class="text-slate-600 mb-4 text-sm leading-relaxed">Earn passive rewards by staking popular cryptocurrencies. Flexible lock periods with competitive APY rates.</p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">High APY</span>
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Daily Rewards</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-16 text-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center px-10 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold rounded-full shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:-translate-y-1 transition-all duration-300">
                            Start Investing Today
                            <i class="fas fa-arrow-right ml-3"></i>
                        </a>
                    </div>
                </div>
            </section>

            <section class="py-20 bg-white">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-7xl mx-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                            
                            <div>
                                <span class="text-orange-600 font-semibold uppercase tracking-wider text-sm">Affiliate System</span>
                                <h3 class="text-3xl md:text-4xl font-bold text-slate-900 mt-2 mb-6">
                                    Referral <span class="text-orange-600">Program</span>
                                </h3>
                                <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                                    Earn generous rewards by inviting new investors to {{$settings->site_name}}. Our multi-tier referral system rewards you for building a network of successful traders.
                                </p>

                                <div class="relative overflow-hidden bg-slate-900 rounded-2xl p-8 text-white shadow-2xl">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/20 rounded-full blur-2xl"></div>
                                    <h4 class="text-xl font-bold mb-2 text-white">4-Tier Commission Structure</h4>
                                    <p class="mb-6 text-slate-400 text-sm">
                                        Unlock earning potential on multiple levels.
                                    </p>
                                    
                                    <div class="flex items-center gap-6">
                                        <div class="bg-gradient-to-br from-orange-500 to-red-600 p-4 rounded-xl text-center min-w-[100px]">
                                            <span class="block text-3xl font-bold">7%</span>
                                            <span class="text-xs uppercase opacity-80">Level 1</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-3">
                                                <div class="h-2 w-2 rounded-full bg-orange-400"></div>
                                                <span class="text-lg font-bold">3% Level 2</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="h-2 w-2 rounded-full bg-orange-500"></div>
                                                <span class="text-lg font-bold">2% Level 3</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="h-2 w-2 rounded-full bg-orange-600"></div>
                                                <span class="text-lg font-bold">1% Level 4</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 bg-orange-500 rounded-2xl rotate-3 opacity-10 scale-95"></div>
                                <img src="temp/custom/assets/images/investment/notebook.png" alt="Investment Platform" class="relative z-10 w-full h-auto rounded-2xl shadow-2xl border border-slate-100">
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20">
                                    <button class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-orange-600 shadow-xl hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-play ml-1"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <section class="py-20 bg-slate-50 border-t border-slate-200">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-slate-900 mb-4">Platform Advantages</h2>
                        <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                            Why leading investors choose {{$settings->site_name}} for their investment and trading needs.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white rounded-xl p-8 text-center shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-100">
                            <div class="w-14 h-14 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-5 text-orange-600">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-slate-900 mb-2">24/7 Trading</h4>
                            <p class="text-slate-600 text-sm">AI-powered bots trade continuously without breaks or holidays.</p>
                        </div>

                        <div class="bg-white rounded-xl p-8 text-center shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-100">
                            <div class="w-14 h-14 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-5 text-orange-600">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-slate-900 mb-2">Instant Withdrawals</h4>
                            <p class="text-slate-600 text-sm">Access your funds anytime with automated instant processing.</p>
                        </div>

                        <div class="bg-white rounded-xl p-8 text-center shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-100">
                            <div class="w-14 h-14 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-5 text-orange-600">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-slate-900 mb-2">Flexible Payments</h4>
                            <p class="text-slate-600 text-sm">Support for various cryptocurrencies and payment methods.</p>
                        </div>

                        <div class="bg-white rounded-xl p-8 text-center shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-100">
                            <div class="w-14 h-14 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-5 text-orange-600">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-slate-900 mb-2">Secure & Transparent</h4>
                            <p class="text-slate-600 text-sm">Bank-grade security with full transaction transparency.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-20 bg-white">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="order-2 lg:order-1">
                            <img src="temp/custom/assets/images/payments/payment.png" alt="Payment Systems" class="w-full h-auto drop-shadow-xl">
                        </div>
                        <div class="order-1 lg:order-2">
                            <h3 class="text-3xl font-bold text-slate-900 mb-6">
                                Supported <span class="text-orange-600">Payment Methods</span>
                            </h3>
                            <p class="text-lg text-slate-600 mb-6">
                                {{$settings->site_name}} supports multiple payment systems for your convenience. We believe in financial freedom without barriers.
                            </p>
                            <p class="text-slate-600 mb-8 font-medium">
                                <i class="fas fa-check-circle text-orange-500 mr-2"></i> 0% Fee on Deposits<br>
                                <i class="fas fa-check-circle text-orange-500 mr-2"></i> 0% Fee on Withdrawals
                            </p>
                            
                            <a href="{{ route('register') }}" class="group inline-flex items-center px-8 py-3.5 border-2 border-orange-500 text-orange-600 font-bold rounded-lg hover:bg-orange-500 hover:text-white transition-all duration-300">
                                <span>INVEST NOW</span>
                                <svg class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <div id="button-up" class="hidden fixed right-6 bottom-16 bg-orange-600 text-white rounded-full shadow-lg shadow-orange-500/40 p-4 cursor-pointer hover:bg-orange-700 hover:-translate-y-1 transition-all duration-300 z-50">
                <i class="fa fa-chevron-up text-lg"></i>
            </div>

            <script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
            <script>
                $(document).ready(function() {
                    // Back to top button logic
                    var button = $('#button-up');
                    $(window).scroll(function() {
                        if ($(this).scrollTop() > 300) {
                            button.fadeIn();
                        } else {
                            button.fadeOut();
                        }
                    });

                    button.on('click', function(){
                        $('html, body').animate({
                            scrollTop: 0
                        }, 800);
                        return false;
                    });

                    // Counter Logic (Preserved from original)
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
                            const sectionHeight = counterSection.outerHeight();
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
        </div>
    </main>

@endsection