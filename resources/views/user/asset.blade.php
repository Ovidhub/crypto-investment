@extends('layouts.dash')
@section('title', $title)
@section('content')

<!-- Make sure jQuery is loaded -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- CSRF Token for AJAX Requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>

<!-- Exchange Header with Gradient -->
<div class="relative bg-gradient-to-r from-primary/5 to-primary/20 dark:from-primary/10 dark:to-primary/30 rounded-xl p-6 mb-6 overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full filter blur-3xl -mr-32 -mt-32"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-primary/10 rounded-full filter blur-3xl -ml-32 -mb-32"></div>
    
    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-xl bg-primary/20 dark:bg-primary/30 backdrop-blur-sm flex items-center justify-center mr-4 shadow-lg">
                <svg class="w-6 h-6 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M17.7513 7.04996C18.9963 8.29496 19.7613 10.025 19.7613 12C19.7613 16.42 16.1813 20 11.7613 20C7.34125 20 3.76125 16.42 3.76125 12C3.76125 7.58 7.34125 4 11.7613 4C13.7363 4 15.5013 4.75 16.7413 6.00" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.24 2.48001L19.76 6.23001L16.01 5.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9.25 11.5L10.75 13L14.25 9.5" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-dark dark:text-white">Swap Crypto</h1>
                <p class="text-dark-500 dark:text-light-300 text-sm">Exchange your assets instantly at competitive rates</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('swaphistory') }}" class="px-4 py-2.5 text-sm font-medium rounded-lg bg-white dark:bg-dark-100 text-primary border border-light-200 dark:border-dark-200/50 hover:bg-light-100 dark:hover:bg-dark-200 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none">
                    <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.71 15.18L12.61 13.33C12.07 13.01 11.63 12.24 11.63 11.61V7.51001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                History
            </a>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<x-danger-alert />
<x-success-alert />

<!-- Exchange Info Card -->
<div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-4 mb-6">
    <div class="flex items-center mb-2">
        <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center mr-3">
            <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none">
                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                <path d="M12 16V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-medium text-dark dark:text-white">Important Information</h3>
            <p class="text-dark-500 dark:text-light-400 text-xs">Earn even more when you swap your Account balance to and from crypto.</p>
        </div>
    </div>
</div>

<!-- Crypto Balances Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Account Balance - Highlighted Card -->
    <div class="bg-gradient-to-br from-primary/5 to-primary/20 dark:from-primary/10 dark:to-primary/30 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-primary/10 rounded-full filter blur-xl -mr-10 -mt-10"></div>
        <div class="flex items-center justify-between relative">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Account Balance</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2, '.', ',') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white dark:bg-dark-100 shadow-md flex items-center justify-center">
                <svg class="w-6 h-6 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M18.04 13.55C17.62 13.96 17.38 14.55 17.44 15.18C17.53 16.26 18.52 17.05 19.6 17.05H21.5V18.24C21.5 20.31 19.81 22 17.74 22H6.26C4.19 22 2.5 20.31 2.5 18.24V11.51C2.5 9.44 4.19 7.75 6.26 7.75H17.74C19.81 7.75 21.5 9.44 21.5 11.51V12.95H19.48C18.92 12.95 18.41 13.17 18.04 13.55Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M7 12H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Cryptocurrency Cards -->
    @if ($moresettings->btc == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Bitcoin</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->btc, 8) }} BTC</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="btc"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://s2.coinmarketcap.com/static/img/coins/64x64/1.png" alt="BTC" />
            </div>
        </div>
    </div>
    @endif
    
    @if ($moresettings->eth == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Ethereum</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->eth, 8) }} ETH</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="eth"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://img.icons8.com/fluency/48/000000/ethereum.png" alt="ETH" />
            </div>
        </div>
    </div>
    @endif
    
    @if ($moresettings->ltc == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Litecoin</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->ltc, 8) }} LTC</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="ltc"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://s2.coinmarketcap.com/static/img/coins/64x64/2.png" alt="LTC" />
            </div>
        </div>
    </div>
    @endif
    
    @if ($moresettings->link == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Chainlink</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->link, 8) }} LINK</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="link"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://img.icons8.com/cotton/64/000000/chainlink.png" alt="LINK" />
            </div>
        </div>
    </div>
    @endif
    
    @if ($moresettings->bnb == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Binance Coin</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->bnb, 8) }} BNB</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="bnb"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://s2.coinmarketcap.com/static/img/coins/64x64/1839.png" alt="BNB" />
            </div>
        </div>
    </div>
    @endif

    @if ($moresettings->ada == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Cardano</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->ada, 8) }} ADA</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="ada"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://s2.coinmarketcap.com/static/img/coins/64x64/2010.png" alt="ADA" />
            </div>
        </div>
    </div>
    @endif

    @if ($moresettings->aave == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Aave</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->aave, 8) }} AAVE</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="aave"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://s2.coinmarketcap.com/static/img/coins/64x64/7278.png" alt="AAVE" />
            </div>
        </div>
    </div>
    @endif

    @if ($moresettings->usdt == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Tether</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->usdt, 8) }} USDT</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1">{{ $settings->currency }}{{ number_format(round($cbalance->usdt)) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://img.icons8.com/color/48/000000/tether--v2.png" alt="USDT" />
            </div>
        </div>
    </div>
    @endif

    @if ($moresettings->bch == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Bitcoin Cash</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->bch, 8) }} BCH</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="bch"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://s2.coinmarketcap.com/static/img/coins/64x64/1831.png" alt="BCH" />
            </div>
        </div>
    </div>
    @endif

    @if ($moresettings->xrp == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Ripple</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->xrp, 8) }} XRP</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="xrp"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://img.icons8.com/fluency/48/000000/ripple.png" alt="XRP" />
            </div>
        </div>
    </div>
    @endif

    @if ($moresettings->xlm == 'enabled')
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5 transition-transform hover:translate-y-[-3px]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-medium text-dark-500 dark:text-light-300">Stellar</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">{{ round($cbalance->xlm, 8) }} XLM</p>
                <p class="text-xs text-dark-300 dark:text-light-300 mt-1 usdelement" id="xlm"></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700/30 flex items-center justify-center">
                <img class="w-7 h-7" src="https://img.icons8.com/ios/50/000000/stellar.png" alt="XLM" />
            </div>
        </div>
    </div>
    @endif
</div>

<!-- TradingView and Swap Form -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Modern Market Chart Container -->
    <div class="md:col-span-2">
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
            <div class="p-4 border-b border-light-200 dark:border-dark-200/50 flex justify-between items-center">
                <h2 class="text-base font-bold text-dark dark:text-white flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mr-2">
                        <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none">
                            <path d="M22 8.27V4.23C22 2.64 21.36 2 19.77 2H15.73C14.14 2 13.5 2.64 13.5 4.23V8.27C13.5 9.86 14.14 10.5 15.73 10.5H19.77C21.36 10.5 22 9.86 22 8.27Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M10.5 8.52V3.98C10.5 2.57 9.86 2 8.27 2H4.23C2.64 2 2 2.57 2 3.98V8.51C2 9.93 2.64 10.49 4.23 10.49H8.27C9.86 10.5 10.5 9.93 10.5 8.52Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M10.5 19.77V15.73C10.5 14.14 9.86 13.5 8.27 13.5H4.23C2.64 13.5 2 14.14 2 15.73V19.77C2 21.36 2.64 22 4.23 22H8.27C9.86 22 10.5 21.36 10.5 19.77Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M22 19.77V15.73C22 14.14 21.36 13.5 19.77 13.5H15.73C14.14 13.5 13.5 14.14 13.5 15.73V19.77C13.5 21.36 14.14 22 15.73 22H19.77C21.36 22 22 21.36 22 19.77Z" fill="currentColor" fill-opacity="0.2"/>
                        </svg>
                    </div>
                    <span>Crypto Market</span>
                </h2>
                
                <!-- Timeframe Switcher -->
                <div class="flex space-x-1 text-xs">
                    <button class="px-3 py-1.5 rounded-lg bg-primary text-white" id="time-1h">1H</button>
                    <button class="px-3 py-1.5 rounded-lg bg-light-100 dark:bg-dark-200 text-dark-300 dark:text-light-300 hover:bg-light-200 dark:hover:bg-dark-300 transition-colors" id="time-1d">1D</button>
                    <button class="px-3 py-1.5 rounded-lg bg-light-100 dark:bg-dark-200 text-dark-300 dark:text-light-300 hover:bg-light-200 dark:hover:bg-dark-300 transition-colors" id="time-1w">1W</button>
                    <button class="px-3 py-1.5 rounded-lg bg-light-100 dark:bg-dark-200 text-dark-300 dark:text-light-300 hover:bg-light-200 dark:hover:bg-dark-300 transition-colors" id="time-1m">1M</button>
                </div>
            </div>
            
            <div class="p-4">
                <!-- Market Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="p-3 bg-light-50 dark:bg-dark-200/50 rounded-lg border border-light-200 dark:border-dark-300/30 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24">
                                <path d="M9 12H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 9L12 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-dark-300 dark:text-light-300">BTC Dominance</p>
                            <p class="text-sm font-bold text-dark dark:text-white">~42.3%</p>
                        </div>
                    </div>
                    <div class="p-3 bg-light-50 dark:bg-dark-200/50 rounded-lg border border-light-200 dark:border-dark-300/30 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24">
                                <path d="M12 18V6M12 6L7 11M12 6L17 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-dark-300 dark:text-light-300">Global Market Cap</p>
                            <p class="text-sm font-bold text-dark dark:text-white">$2.58T</p>
                        </div>
                    </div>
                    <div class="p-3 bg-light-50 dark:bg-dark-200/50 rounded-lg border border-light-200 dark:border-dark-300/30 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
                                <path d="M7.37145 10.2017V17.0618" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12.0382 6.91919V17.0619" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16.6285 13.8269V17.0619" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-dark-300 dark:text-light-300">24h Volume</p>
                            <p class="text-sm font-bold text-dark dark:text-white">$102.7B</p>
                        </div>
                    </div>
                </div>
                
                <!-- Interactive Chart -->
                <div class="relative h-[350px] mb-4 market-chart bg-light-50 dark:bg-dark-200/30 rounded-lg overflow-hidden">
                    <!-- Chart Overlay Elements -->
                    <div class="absolute left-0 top-0 p-3 z-10 bg-white/80 dark:bg-dark-800/80 backdrop-blur-sm rounded-br-lg">
                        <div>
                            <span class="text-xs text-dark-300 dark:text-light-300">BTC/USD</span>
                            <h3 class="text-lg font-bold text-dark dark:text-white flex items-center gap-2">
                                $63,482.12
                                <span class="text-xs text-green-500 font-medium flex items-center bg-green-50 dark:bg-green-900/30 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3 mr-0.5" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 18V6M12 6L7 11M12 6L17 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    +1.24%
                                </span>
                            </h3>
                        </div>
                    </div>
                    
                    <!-- Fallback for TradingView -->
                    <div id="chart-container" class="h-full w-full">
                        <div id="tradingview_f933e" class="h-full"></div>
                    </div>
                </div>
                
                <!-- Price Movement Indicators -->
                <div class="flex flex-wrap gap-2 justify-between text-xs mb-2">
                    <div class="px-3 py-1.5 rounded-full bg-light-100 dark:bg-dark-200 text-dark-500 dark:text-light-300 flex items-center">
                        <span class="w-2 h-2 bg-amber-400 rounded-full mr-1.5"></span>
                        ATH: $69,045
                    </div>
                    <div class="px-3 py-1.5 rounded-full bg-light-100 dark:bg-dark-200 text-dark-500 dark:text-light-300 flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1.5"></span>
                        24h High: $64,120
                    </div>
                    <div class="px-3 py-1.5 rounded-full bg-light-100 dark:bg-dark-200 text-dark-500 dark:text-light-300 flex items-center">
                        <span class="w-2 h-2 bg-red-400 rounded-full mr-1.5"></span>
                        24h Low: $62,890
                    </div>
                    <div class="px-3 py-1.5 rounded-full bg-light-100 dark:bg-dark-200 text-dark-500 dark:text-light-300 flex items-center">
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-1.5"></span>
                        Vol: $32.1B
                    </div>
                </div>
            </div>
            
            <!-- Market Info Footer -->
            <div class="bg-light-50 dark:bg-dark-200/50 p-3 border-t border-light-200 dark:border-dark-300/30 text-xs text-dark-400 dark:text-light-400 text-center flex items-center justify-center">
                <svg class="w-4 h-4 mr-1.5 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.71 15.18L12.61 13.33C12.07 13.01 11.63 12.24 11.63 11.61V7.51001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Data refreshes automatically every minute • Last updated: <span id="last-updated" class="font-medium">Just now</span>
            </div>
        </div>
    </div>

    <!-- Swap Form -->
    <div class="md:col-span-1">
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
            <div class="bg-gradient-to-r from-primary/5 to-primary/20 dark:from-primary/10 dark:to-primary/30 p-4 border-b border-light-200 dark:border-dark-200/50 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-white dark:bg-dark-100 shadow-sm flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none">
                        <path d="M19.32 10.7488L20.7066 9.36225C22.2881 7.78108 22.2881 5.21892 20.7066 3.63775C19.125 2.05592 16.5625 2.05592 14.9809 3.63775L13.5943 5.02434" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.67188 13.2488L3.28545 14.6362C1.70395 16.2174 1.70395 18.7795 3.28545 20.3607C4.86695 21.9427 7.42947 21.9427 9.01097 20.3607L10.3974 18.9741" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10.5574 13.4425L13.4425 10.5574" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7.40723 10.292L13.707 16.5917" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="text-base font-bold text-dark dark:text-white">Swap Crypto</h2>
            </div>
            <div class="p-5">
                <form method="POST" action="javascript:void(0)" id="exchnageform" class="space-y-5">
                    @csrf
                    <!-- Source Account -->
                    <div class="space-y-2">
                        <label for="sourceasset" class="text-sm font-medium text-dark dark:text-white flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-primary" viewBox="0 0 24 24" fill="none">
                                <path d="M9.5 13.75C9.5 14.72 10.25 15.5 11.17 15.5H13.05C13.85 15.5 14.5 14.82 14.5 13.97C14.5 13.06 14.1 12.73 13.51 12.52L10.5 11.47C9.91 11.26 9.51001 10.94 9.51001 10.02C9.51001 9.18 10.16 8.49001 10.96 8.49001H12.84C13.76 8.49001 14.51 9.27001 14.51 10.24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Source Account
                        </label>
                        <div class="relative">
                            <select class="appearance-none block w-full pl-4 pr-10 py-3 text-sm rounded-lg bg-light-50 dark:bg-dark-200/80 border border-light-200 dark:border-dark-200/80 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all" 
                                   name="source" 
                                   id="sourceasset">
                                @if ($moresettings->btc == 'enabled')
                                    <option value="btc">BTC</option>
                                @endif
                                @if ($moresettings->link == 'enabled')
                                    <option value="link">LINK</option>
                                @endif
                                @if ($moresettings->bnb == 'enabled')
                                    <option value="bnb">BNB</option>
                                @endif
                                @if ($moresettings->ada == 'enabled')
                                    <option value="ada">ADA</option>
                                @endif
                                @if ($moresettings->aave == 'enabled')
                                    <option value="aave">AAVE</option>
                                @endif
                                @if ($moresettings->xlm == 'enabled')
                                    <option value="xlm">XLM</option>
                                @endif
                                @if ($moresettings->xrp == 'enabled')
                                    <option value="xrp">XRP</option>
                                @endif
                                @if ($moresettings->ltc == 'enabled')
                                    <option value="ltc">LTC</option>
                                @endif
                                @if ($moresettings->bch == 'enabled')
                                    <option value="bch">BCH</option>
                                @endif
                                @if ($moresettings->eth == 'enabled')
                                    <option value="eth">ETH</option>
                                @endif
                                @if ($moresettings->usdt == 'enabled')
                                    <option value="usdt">USDT</option>
                                @endif
                                <option value="usd">USD</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-dark-300 dark:text-light-300" viewBox="0 0 24 24" fill="none">
                                    <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Swap Direction Indicator -->
                    <div class="flex justify-center">
                        <div class="w-8 h-8 bg-light-100 dark:bg-dark-300 rounded-full flex items-center justify-center transform rotate-90 md:rotate-0">
                            <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none">
                                <path d="M17.28 10.45L21 6.72998L17.28 3.01001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 6.72998H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.72 13.55L3 17.27L6.72 20.99" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21 17.27H3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Destination Account -->
                    <div class="space-y-2">
                        <label for="destinationasset" class="text-sm font-medium text-dark dark:text-white flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-primary" viewBox="0 0 24 24" fill="none">
                                <path d="M9.5 13.75C9.5 14.72 10.25 15.5 11.17 15.5H13.05C13.85 15.5 14.5 14.82 14.5 13.97C14.5 13.06 14.1 12.73 13.51 12.52L10.5 11.47C9.91 11.26 9.51001 10.94 9.51001 10.02C9.51001 9.18 10.16 8.49001 10.96 8.49001H12.84C13.76 8.49001 14.51 9.27001 14.51 10.24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Destination Account
                        </label>
                        <div class="relative">
                            <select class="appearance-none block w-full pl-4 pr-10 py-3 text-sm rounded-lg bg-light-50 dark:bg-dark-200/80 border border-light-200 dark:border-dark-200/80 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all" 
                                   name="destination" 
                                   id="destinationasset">
                                <option value="usd">USD</option>
                                @if ($moresettings->btc == 'enabled')
                                    <option value="btc">BTC</option>
                                @endif
                                @if ($moresettings->link == 'enabled')
                                    <option value="link">LINK</option>
                                @endif
                                @if ($moresettings->bnb == 'enabled')
                                    <option value="bnb">BNB</option>
                                @endif
                                @if ($moresettings->ada == 'enabled')
                                    <option value="ada">ADA</option>
                                @endif
                                @if ($moresettings->aave == 'enabled')
                                    <option value="aave">AAVE</option>
                                @endif
                                @if ($moresettings->xlm == 'enabled')
                                    <option value="xlm">XLM</option>
                                @endif
                                @if ($moresettings->xrp == 'enabled')
                                    <option value="xrp">XRP</option>
                                @endif
                                @if ($moresettings->ltc == 'enabled')
                                    <option value="ltc">LTC</option>
                                @endif
                                @if ($moresettings->bch == 'enabled')
                                    <option value="bch">BCH</option>
                                @endif
                                @if ($moresettings->eth == 'enabled')
                                    <option value="eth">ETH</option>
                                @endif
                                @if ($moresettings->usdt == 'enabled')
                                    <option value="usdt">USDT</option>
                                @endif
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-dark-300 dark:text-light-300" viewBox="0 0 24 24" fill="none">
                                    <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-dark-300 dark:text-light-300 flex items-center">
                            <svg class="w-3 h-3 mr-1" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M12 16V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            NOTE: USD is your account balance.
                        </p>
                    </div>

                    <!-- Amount -->
                    <div class="space-y-2">
                        <label for="amount" class="text-sm font-medium text-dark dark:text-white flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-primary" viewBox="0 0 24 24" fill="none">
                                <path d="M22 6V8.42C22 10 21 11 19.42 11H16V4.01C16 2.9 16.91 2 18.02 2C19.11 2.01 20.11 2.45 20.83 3.17C21.55 3.9 22 4.9 22 6Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M2 7V21C2 21.83 2.94 22.3 3.6 21.8L5.31 20.52C5.71 20.22 6.27 20.26 6.63 20.62L8.29 22.29C8.68 22.68 9.32 22.68 9.71 22.29L11.39 20.61C11.74 20.26 12.3 20.22 12.69 20.52L14.4 21.8C15.06 22.29 16 21.82 16 21V4C16 2.9 16.9 2 18 2H7H6C3 2 2 3.79 2 6V7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 13.01H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 9.01001H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5.99561 13H6.00459" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5.99561 9H6.00459" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Amount
                        </label>
                        <div class="relative">
                            <input type="text" name="amount" id="amount"
                                   class="block w-full pl-4 pr-10 py-3 text-sm rounded-lg bg-light-50 dark:bg-dark-200/80 border border-light-200 dark:border-dark-200/80 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all"
                                   placeholder="Enter amount of btc">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-dark-300 dark:text-light-300 text-xs pointer-events-none">
                                <span id="source-currency">BTC</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- You will get -->
                    <div class="space-y-2">
                        <label for="quantity" class="text-sm font-medium text-dark dark:text-white flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-primary" viewBox="0 0 24 24" fill="none">
                                <path d="M8 2V5" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 2V5" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.5 9.08997H20.5" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 13.01V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11.995 13.7H12.005" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.294 13.7H8.304" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.294 16.7H8.304" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            You will get
                        </label>
                        <div class="relative">
                            <input type="text" id="quantity" readonly
                                   class="block w-full pl-4 pr-10 py-3 text-sm rounded-lg bg-light-50 dark:bg-dark-200/80 border border-light-200 dark:border-dark-200/80 text-dark dark:text-white cursor-not-allowed"
                                   placeholder="Quantity of usd">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-dark-300 dark:text-light-300 text-xs pointer-events-none">
                                <span id="dest-currency">USD</span>
                            </div>
                            <input type="hidden" id="realquantity" name="quantity">
                        </div>
                    </div>
                    
                    <!-- Fees -->
                    <div class="flex items-center p-3 rounded-lg bg-light-50 dark:bg-dark-200/80 border border-light-200 dark:border-dark-300/50">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none">
                                <path d="M12 7V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 17.01L12.01 16.9989" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm text-dark dark:text-white font-medium">
                                Transaction Fee
                            </span>
                            <p class="text-xs text-dark-400 dark:text-light-400">
                                {{ $moresettings->fee }}% of transaction amount
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-lg bg-gradient-to-r from-primary to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-medium flex items-center justify-center gap-2 transition-all shadow-sm">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path d="M9.5 13.75C9.5 14.72 10.25 15.5 11.17 15.5H13.05C13.85 15.5 14.5 14.82 14.5 13.97C14.5 13.06 14.1 12.73 13.51 12.52L10.5 11.47C9.91 11.26 9.51001 10.94 9.51001 10.02C9.51001 9.18 10.16 8.49001 10.96 8.49001H12.84C13.76 8.49001 14.51 9.27001 14.51 10.24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Swap Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modern styles with animations -->
<style>
    /* Card hover effects */
    .crypto-card {
        transition: all 0.3s ease;
    }
    .crypto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Select styles */
    select.appearance-none {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
    }
    
    /* Modern Toast Notifications */
    .toast {
        min-width: 300px;
        max-width: 400px;
        border-radius: 10px;
        margin-bottom: 1rem;
        padding: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateX(100%);
        animation: slideIn 0.3s forwards, fadeOut 0.5s 4.5s forwards;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        z-index: 10000;
    }
    
    .toast.success {
        background-color: #10b981;
        color: white;
    }
    
    .toast.danger {
        background-color: #ef4444;
        color: white;
    }
    
    .toast.dark-success {
        background-color: #065f46;
        color: white;
    }
    
    .toast.dark-danger {
        background-color: #b91c1c;
        color: white;
    }
    
    .toast .icon {
        margin-right: 12px;
        font-size: 1.5rem;
    }
    
    .toast .content {
        flex: 1;
    }
    
    .toast .title {
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .toast .message {
        font-size: 0.75rem;
        opacity: 0.9;
    }
    
    .toast .close {
        cursor: pointer;
        font-size: 0.75rem;
        padding: 4px;
    }
    
    .toast .progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
    }
    
    .toast .progress-bar {
        height: 100%;
        background-color: rgba(255, 255, 255, 0.3);
        width: 100%;
        animation: progress 5s linear forwards;
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
    
    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
    
    /* Shimmer animation for loading states */
    .shimmer {
        background: linear-gradient(90deg, 
            rgba(255, 255, 255, 0) 0%, 
            rgba(255, 255, 255, 0.2) 50%, 
            rgba(255, 255, 255, 0) 100%);
        background-size: 200% 100%;
        animation: shimmerEffect 1.5s infinite;
    }
    
    @keyframes shimmerEffect {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    /* Pulse animation for price changes */
    .pulse-green {
        animation: pulseGreen 2s ease-in-out 1;
    }
    
    .pulse-red {
        animation: pulseRed 2s ease-in-out 1;
    }
    
    @keyframes pulseGreen {
        0% { background-color: transparent; }
        30% { background-color: rgba(16, 185, 129, 0.15); }
        100% { background-color: transparent; }
    }
    
    @keyframes pulseRed {
        0% { background-color: transparent; }
        30% { background-color: rgba(239, 68, 68, 0.15); }
        100% { background-color: transparent; }
    }
</style>

<!-- TradingView Widget Script -->
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script type="text/javascript">
    // Initialize TradingView widget with advanced chart options
    let widget;
    let currentTimeframe = '60'; // Default to 1H
    let darkMode = document.documentElement.classList.contains('dark') || 
                 window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    function initTradingViewWidget() {
        if (widget) {
            widget.remove();
        }
        
        widget = new TradingView.widget({
            "width": "100%",
            "height": "100%",
            "symbol": "COINBASE:BTCUSD",
            "interval": currentTimeframe,
            "timezone": "Etc/UTC",
            "theme": darkMode ? "dark" : "light",
            "style": "1", // Use candlesticks for better visualization
            "locale": "en",
            "toolbar_bg": darkMode ? "#2a2f3c" : "#f1f3f6",
            "enable_publishing": false,
            "hide_top_toolbar": true,
            "hide_legend": false,
            "hide_side_toolbar": true,
            "allow_symbol_change": true,
            "save_image": false,
            "backgroundColor": darkMode ? "rgba(26, 30, 41, 0.5)" : "rgba(255, 255, 255, 0.5)",
            "gridColor": darkMode ? "rgba(255, 255, 255, 0.06)" : "rgba(0, 0, 0, 0.06)",
            "studies": [
                "MAExp@tv-basicstudies",
                "RSI@tv-basicstudies"
            ],
            "container_id": "tradingview_f933e",
            "overrides": {
                "mainSeriesProperties.candleStyle.upColor": "#10b981",
                "mainSeriesProperties.candleStyle.downColor": "#ef4444",
                "mainSeriesProperties.candleStyle.borderUpColor": "#10b981",
                "mainSeriesProperties.candleStyle.borderDownColor": "#ef4444",
                "mainSeriesProperties.candleStyle.wickUpColor": "#10b981",
                "mainSeriesProperties.candleStyle.wickDownColor": "#ef4444"
            }
        });
    }
    
    // Initialize the widget
    document.addEventListener('DOMContentLoaded', function() {
        initTradingViewWidget();
        updateLastUpdatedTime();
        
        // Setup timeframe buttons
        document.getElementById('time-1h').addEventListener('click', function() {
            setTimeframe('60', this);
        });
        document.getElementById('time-1d').addEventListener('click', function() {
            setTimeframe('D', this);
        });
        document.getElementById('time-1w').addEventListener('click', function() {
            setTimeframe('W', this);
        });
        document.getElementById('time-1m').addEventListener('click', function() {
            setTimeframe('M', this);
        });
        
        // Update currency display when selecting source/destination
        const sourceasset = document.getElementById('sourceasset');
        const destinationasset = document.getElementById('destinationasset');
        const sourceCurrency = document.getElementById('source-currency');
        const destCurrency = document.getElementById('dest-currency');
        
        if (sourceasset && sourceCurrency) {
            sourceasset.addEventListener('change', function() {
                sourceCurrency.textContent = this.value.toUpperCase();
            });
            // Initial value
            sourceCurrency.textContent = sourceasset.value.toUpperCase();
        }
        
        if (destinationasset && destCurrency) {
            destinationasset.addEventListener('change', function() {
                destCurrency.textContent = this.value.toUpperCase();
            });
            // Initial value
            destCurrency.textContent = destinationasset.value.toUpperCase();
        }
    });
    
    // Function to update the timeframe
    function setTimeframe(interval, button) {
        currentTimeframe = interval;
        
        // Update button states
        document.querySelectorAll('#time-1h, #time-1d, #time-1w, #time-1m').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white');
            btn.classList.add('bg-light-100', 'dark:bg-dark-200', 'text-dark-300', 'dark:text-light-300', 'hover:bg-light-200', 'dark:hover:bg-dark-300');
        });
        
        button.classList.remove('bg-light-100', 'dark:bg-dark-200', 'text-dark-300', 'dark:text-light-300', 'hover:bg-light-200', 'dark:hover:bg-dark-300');
        button.classList.add('bg-primary', 'text-white');
        
        // Update the widget with new timeframe
        if (widget) {
            widget.setSymbol('COINBASE:BTCUSD', interval);
        }
    }
    
    // Function to update last updated time
    function updateLastUpdatedTime() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        document.getElementById('last-updated').textContent = `${hours}:${minutes}`;
    }
    
    // Check for theme changes
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', (e) => {
        darkMode = e.matches;
        initTradingViewWidget(); // Reinitialize with new theme
    });
    
    // Toggle theme when user manually changes theme
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark !== darkMode) {
                    darkMode = isDark;
                    initTradingViewWidget();
                }
            }
        });
    });
    
    observer.observe(document.documentElement, { attributes: true });
    
    // Update market data every minute
    setInterval(function() {
        updateLastUpdatedTime();
        // In a real implementation, you would fetch updated market data here
    }, 60000);
</script>

<!-- Modern Toast Notification System -->
<script>
    function showToast(type, title, message) {
        const toastContainer = document.getElementById('toast-container');
        const isDarkMode = document.documentElement.classList.contains('dark') || 
                          window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // Determine toast class based on type and dark mode
        let toastClass = type;
        if (isDarkMode) {
            toastClass = 'dark-' + type;
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${toastClass}`;
        
        // Set icon based on type
        let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        // Build toast HTML
        toast.innerHTML = `
            <div class="icon">
                <i class="fas ${icon}"></i>
            </div>
            <div class="content">
                <div class="title">${title || 'Notification'}</div>
                <div class="message">${message}</div>
            </div>
            <div class="close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </div>
            <div class="progress">
                <div class="progress-bar"></div>
            </div>
        `;
        
        // Add to container
        toastContainer.appendChild(toast);
        
        // Remove after animation completes
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 5000);
        
        // Allow click to dismiss
        toast.addEventListener('click', function(e) {
            if (!e.target.classList.contains('close') && !e.target.parentElement.classList.contains('close')) {
                this.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => {
                    if (this.parentNode) {
                        this.remove();
                    }
                }, 300);
            }
        });
    }

    // Make sure notify function is available globally
    window.notify = function(options, settings) {
        const type = (settings && settings.type) || 'info';
        const title = (options && options.title) || '';
        const message = (options && options.message) || '';
        showToast(type, title, message);
    };

    // If jQuery is available, also define $.notify
    if (typeof jQuery !== 'undefined') {
        jQuery.notify = window.notify;
    }
</script>

<!-- Include the original exchange script with modern adaptations -->
@include('user.exchangescript')
@endsection