@extends('layouts.dash')

@section('title', 'Crypto Trading')

@push('styles')
<style>
@keyframes ticker-scroll {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

.ticker-animation {
    display: flex;
    animation: ticker-scroll 60s linear infinite;
}

.ticker-animation:hover {
    animation-play-state: paused;
}
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div class="min-w-0 flex-1">
            <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-white">Crypto Trading</h1>
            <p class="text-gray-400 mt-1 text-xs sm:text-sm">Trade Bitcoin, Ethereum, and other cryptocurrencies</p>
        </div>
        <div class="flex space-x-2 sm:space-x-3 flex-shrink-0">
            <a href="{{ route('crypto-trading.portfolio') }}" class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 hover:border-primary-500 text-white px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl flex items-center space-x-1 sm:space-x-2 transition-all duration-300 shadow-xl hover:shadow-2xl">
                <i data-lucide="briefcase" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                <span class="text-xs sm:text-sm">Portfolio</span>
            </a>
            <a href="{{ route('crypto-trading.orders') }}" class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 hover:border-primary-500 text-white px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl flex items-center space-x-1 sm:space-x-2 transition-all duration-300 shadow-xl hover:shadow-2xl">
                <i data-lucide="list" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                <span class="text-xs sm:text-sm">Orders</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider">Portfolio Value</p>
                    <p class="text-2xl font-bold text-white mt-1">${{ number_format($portfolioValue, 2) }}</p>
                </div>
                <div class="bg-primary-500/10 p-3 rounded-lg">
                    <i data-lucide="trending-up" class="w-6 h-6 text-primary-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider">Total Invested</p>
                    <p class="text-2xl font-bold text-white mt-1">${{ number_format($totalInvested, 2) }}</p>
                </div>
                <div class="bg-blue-500/10 p-3 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider">Total P/L</p>
                    <p class="text-2xl font-bold {{ $totalProfitLoss >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                        {{ $totalProfitLoss >= 0 ? '+' : '' }}${{ number_format($totalProfitLoss, 2) }}
                    </p>
                    <p class="text-sm {{ $portfolioROI >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                        ROI: {{ number_format($portfolioROI, 2) }}%
                    </p>
                </div>
                <div class="bg-{{ $totalProfitLoss >= 0 ? 'green' : 'red' }}-500/10 p-3 rounded-lg">
                    <i data-lucide="{{ $totalProfitLoss >= 0 ? 'arrow-up' : 'arrow-down' }}" class="w-6 h-6 text-{{ $totalProfitLoss >= 0 ? 'green' : 'red' }}-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider">Pending Orders</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $pendingOrders ?? 0 }}</p>
                </div>
                <div class="bg-yellow-500/10 p-3 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Price Ticker -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-3 shadow-xl mb-6 overflow-hidden">
        <div class="flex items-center space-x-8 ticker-animation">
            @php
                $tickerCryptos = $cryptos->take(20);
            @endphp
            @foreach($tickerCryptos as $crypto)
            <a href="{{ route('crypto-trading.trade', $crypto->coin_symbol) }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity whitespace-nowrap">
                @if($crypto->logo)
                <img src="{{ $crypto->logo }}" alt="{{ $crypto->coin_symbol }}" class="w-5 h-5 rounded-full">
                @endif
                <span class="text-white font-semibold">{{ $crypto->coin_symbol }}</span>
                <span class="text-gray-400">${{ number_format($crypto->price_usd, 2) }}</span>
                <span class="text-sm {{ $crypto->percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $crypto->percent_change_24h >= 0 ? '+' : '' }}{{ number_format($crypto->percent_change_24h, 2) }}%
                </span>
            </a>
            @endforeach
            <!-- Duplicate for seamless loop -->
            @foreach($tickerCryptos as $crypto)
            <a href="{{ route('crypto-trading.trade', $crypto->coin_symbol) }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity whitespace-nowrap">
                @if($crypto->logo)
                <img src="{{ $crypto->logo }}" alt="{{ $crypto->coin_symbol }}" class="w-5 h-5 rounded-full">
                @endif
                <span class="text-white font-semibold">{{ $crypto->coin_symbol }}</span>
                <span class="text-gray-400">${{ number_format($crypto->price_usd, 2) }}</span>
                <span class="text-sm {{ $crypto->percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $crypto->percent_change_24h >= 0 ? '+' : '' }}{{ number_format($crypto->percent_change_24h, 2) }}%
                </span>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Market Overview Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Top Gainers -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                <i data-lucide="trending-up" class="w-5 h-5 mr-2 text-green-400"></i>
                Top Gainers (24h)
            </h3>
            @php
                $topGainers = $cryptos->sortByDesc('percent_change_24h')->take(5);
            @endphp
            <div class="space-y-3">
                @foreach($topGainers as $crypto)
                <a href="{{ route('crypto-trading.trade', $crypto->coin_symbol) }}" class="flex items-center justify-between p-2 bg-gray-800/50 rounded-lg hover:bg-gray-800 transition-colors">
                    <div class="flex items-center space-x-2">
                        @if($crypto->logo)
                        <img src="{{ $crypto->logo }}" alt="{{ $crypto->coin_symbol }}" class="w-6 h-6 rounded-full">
                        @endif
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $crypto->coin_symbol }}</p>
                            <p class="text-gray-400 text-xs">${{ number_format($crypto->price_usd, 2) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-green-400 font-bold text-sm">+{{ number_format($crypto->percent_change_24h, 2) }}%</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Top Losers -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                <i data-lucide="trending-down" class="w-5 h-5 mr-2 text-red-400"></i>
                Top Losers (24h)
            </h3>
            @php
                $topLosers = $cryptos->sortBy('percent_change_24h')->take(5);
            @endphp
            <div class="space-y-3">
                @foreach($topLosers as $crypto)
                <a href="{{ route('crypto-trading.trade', $crypto->coin_symbol) }}" class="flex items-center justify-between p-2 bg-gray-800/50 rounded-lg hover:bg-gray-800 transition-colors">
                    <div class="flex items-center space-x-2">
                        @if($crypto->logo)
                        <img src="{{ $crypto->logo }}" alt="{{ $crypto->coin_symbol }}" class="w-6 h-6 rounded-full">
                        @endif
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $crypto->coin_symbol }}</p>
                            <p class="text-gray-400 text-xs">${{ number_format($crypto->price_usd, 2) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-red-400 font-bold text-sm">{{ number_format($crypto->percent_change_24h, 2) }}%</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Market Sentiment -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                <i data-lucide="activity" class="w-5 h-5 mr-2 text-primary-500"></i>
                Market Sentiment
            </h3>
            @php
                $gainersCount = $cryptos->filter(fn($c) => $c->percent_change_24h > 0)->count();
                $losersCount = $cryptos->filter(fn($c) => $c->percent_change_24h < 0)->count();
                $totalCount = $cryptos->count();
                $bullishPercent = $totalCount > 0 ? ($gainersCount / $totalCount) * 100 : 50;
            @endphp
            
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-green-400 font-semibold">Bullish</span>
                    <span class="text-red-400 font-semibold">Bearish</span>
                </div>
                <div class="relative h-8 bg-gray-700 rounded-full overflow-hidden">
                    <div class="absolute h-full bg-gradient-to-r from-green-500 to-green-600 transition-all duration-500" style="width: {{ $bullishPercent }}%"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-white font-bold text-sm z-10">{{ number_format($bullishPercent, 1) }}%</span>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-500/10 border border-green-500/20 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="arrow-up" class="w-5 h-5 text-green-400"></i>
                        <span class="text-gray-300">Gainers</span>
                    </div>
                    <span class="text-green-400 font-bold">{{ $gainersCount }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="arrow-down" class="w-5 h-5 text-red-400"></i>
                        <span class="text-gray-300">Losers</span>
                    </div>
                    <span class="text-red-400 font-bold">{{ $losersCount }}</span>
                </div>
                @php
                    $avgChange = $cryptos->avg('percent_change_24h');
                @endphp
                <div class="flex items-center justify-between p-3 bg-primary-500/10 border border-primary-500/20 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 text-primary-400"></i>
                        <span class="text-gray-300">Avg Change</span>
                    </div>
                    <span class="text-primary-400 font-bold {{ $avgChange >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $avgChange >= 0 ? '+' : '' }}{{ number_format($avgChange, 2) }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- All Cryptocurrencies -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
        <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-white flex items-center">
                <i data-lucide="coins" class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-primary-500"></i>
                All Cryptocurrencies
            </h2>
            <div class="flex items-center space-x-2">
                <input type="text" id="cryptoSearch" placeholder="Search..." class="bg-gray-800 border border-gray-700 text-white px-2 sm:px-3 py-2 rounded-lg text-xs sm:text-sm focus:outline-none focus:border-primary-500 w-32 sm:w-auto">
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full" id="cryptoTable">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3 hidden md:table-cell">#</th>
                        <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Cryptocurrency</th>
                        <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3 hidden md:table-cell">Price</th>
                        <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3 hidden md:table-cell">24h Change</th>
                        <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3 hidden lg:table-cell">Market Cap</th>
                        <th class="text-center text-gray-400 text-sm font-semibold py-3 px-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cryptos as $index => $crypto)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/30 transition-colors duration-150">
                        <td class="py-3 px-3 text-gray-400 hidden md:table-cell">{{ $cryptos->firstItem() + $index }}</td>
                        <td class="py-3 px-2 md:px-3">
                            <div class="flex items-center space-x-2 md:space-x-3">
                                @if($crypto->logo)
                                <img src="{{ $crypto->logo }}" alt="{{ $crypto->name }}" class="w-8 h-8 rounded-full flex-shrink-0">
                                @else
                                <div class="w-8 h-8 rounded-full bg-primary-500/10 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="bitcoin" class="w-5 h-5 text-primary-500"></i>
                                </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="text-white font-semibold text-sm md:text-base truncate">{{ $crypto->coin_symbol }}</p>
                                    <p class="text-gray-400 text-xs md:text-sm">{{ $crypto->coin_symbol }}</p>
                                    <!-- Show price and change on mobile below name -->
                                    <div class="flex items-center space-x-2 mt-1 md:hidden">
                                        <span class="text-white text-xs font-semibold">${{ number_format($crypto->price_usd, 2) }}</span>
                                        <span class="text-xs {{ $crypto->percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                            {{ $crypto->percent_change_24h >= 0 ? '+' : '' }}{{ number_format($crypto->percent_change_24h, 2) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3 text-right text-white font-semibold hidden md:table-cell">${{ number_format($crypto->price_usd, 2) }}</td>
                        <td class="py-3 px-3 text-right hidden md:table-cell">
                            <span class="flex items-center justify-end {{ $crypto->percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                <i data-lucide="{{ $crypto->percent_change_24h >= 0 ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4 mr-1"></i>
                                {{ number_format(abs($crypto->percent_change_24h), 2) }}%
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right text-gray-400 hidden lg:table-cell">${{ number_format($crypto->market_cap, 0) }}</td>
                        <td class="py-3 px-2 md:px-3">
                            <div class="flex items-center justify-end md:justify-center space-x-1 md:space-x-2">
                                <a href="{{ route('crypto-trading.trade', $crypto->coin_symbol) }}" class="bg-primary-500 hover:bg-primary-600 text-white px-2 md:px-3 py-1 rounded-lg text-xs md:text-sm transition-colors duration-150 whitespace-nowrap">
                                    Trade
                                </a>
                                <button onclick="addToWatchlist('{{ $crypto->coin_symbol }}')" class="bg-gray-700 hover:bg-gray-600 text-white px-2 md:px-3 py-1 rounded-lg text-xs md:text-sm transition-colors duration-150">
                                    <i data-lucide="star" class="w-3 h-3 md:w-4 md:h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $cryptos->onEachSide(1)->links('components.crypto-pagination') }}
        </div>
    </div>

    <!-- Watchlist & Recent Trades -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Watchlist -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="star" class="w-5 h-5 mr-2 text-yellow-500"></i>
                        My Watchlist
                    </h2>
                </div>
                
                @if($watchlist->isEmpty())
                <p class="text-gray-400 text-center py-4">No cryptocurrencies in watchlist</p>
                @else
                <div class="space-y-3">
                    @foreach($watchlist as $item)
                    <a href="{{ route('crypto-trading.trade', $item->cryptoPrice->coin_symbol) }}" class="block bg-gray-800/50 border border-gray-700 hover:border-primary-500 rounded-lg p-3 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                @if($item->cryptoPrice->logo)
                                <img src="{{ $item->cryptoPrice->logo }}" alt="{{ $item->cryptoPrice->name }}" class="w-8 h-8 rounded-full">
                                @else
                                <div class="w-8 h-8 rounded-full bg-primary-500/10 flex items-center justify-center">
                                    <i data-lucide="bitcoin" class="w-5 h-5 text-primary-500"></i>
                                </div>
                                @endif
                                <div>
                                    <p class="text-white font-semibold">{{ $item->cryptoPrice->coin_symbol }}</p>
                                    <p class="text-gray-400 text-xs">{{ $item->cryptoPrice->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-semibold">${{ number_format($item->cryptoPrice->price_usd, 2) }}</p>
                                <p class="text-sm {{ $item->cryptoPrice->percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $item->cryptoPrice->percent_change_24h >= 0 ? '+' : '' }}{{ number_format($item->cryptoPrice->percent_change_24h, 2) }}%
                                </p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Trades -->
        <div class="lg:col-span-2">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <i data-lucide="activity" class="w-5 h-5 mr-2 text-primary-500"></i>
                    Recent Trades
                </h2>
                
                @if($recentTrades->isEmpty())
                <p class="text-gray-400 text-center py-4">No recent trades</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left text-gray-400 text-sm font-semibold py-2 px-3">Crypto</th>
                                <th class="text-left text-gray-400 text-sm font-semibold py-2 px-3">Type</th>
                                <th class="text-right text-gray-400 text-sm font-semibold py-2 px-3">Quantity</th>
                                <th class="text-right text-gray-400 text-sm font-semibold py-2 px-3">Price</th>
                                <th class="text-right text-gray-400 text-sm font-semibold py-2 px-3">Total</th>
                                <th class="text-center text-gray-400 text-sm font-semibold py-2 px-3">Status</th>
                                <th class="text-left text-gray-400 text-sm font-semibold py-2 px-3">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTrades as $trade)
                            <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                <td class="py-3 px-3">
                                    <div class="flex items-center space-x-2">
                                        @if($trade->cryptoPrice && $trade->cryptoPrice->logo)
                                        <img src="{{ $trade->cryptoPrice->logo }}" alt="{{ $trade->coin_symbol }}" class="w-6 h-6 rounded-full">
                                        @endif
                                        <span class="text-white font-semibold">{{ $trade->coin_symbol }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $trade->trade_type === 'buy' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                        {{ strtoupper($trade->trade_type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right text-white">{{ number_format($trade->quantity, 8) }}</td>
                                <td class="py-3 px-3 text-right text-white">${{ number_format($trade->price_usd, 2) }}</td>
                                <td class="py-3 px-3 text-right text-white">${{ number_format($trade->net_amount, 2) }}</td>
                                <td class="py-3 px-3 text-center">
                                    @if($trade->status === 'completed')
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-green-500/10 text-green-400">Completed</span>
                                    @elseif($trade->status === 'pending')
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-yellow-500/10 text-yellow-400">Pending</span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-500/10 text-gray-400">Cancelled</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-gray-400 text-sm">{{ $trade->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Crypto search filter
document.getElementById('cryptoSearch').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#cryptoTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Add to watchlist
function addToWatchlist(symbol) {
    fetch('{{ route("crypto-trading.watchlist.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ coin_symbol: symbol })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal('Success', data.message, 'success').then(() => location.reload());
        } else {
            swal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        swal('Error', 'Failed to add to watchlist', 'error');
    });
}

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Auto-refresh prices every 30 seconds
setInterval(function() {
    // Add a subtle pulse animation to indicate data refresh
    const priceElements = document.querySelectorAll('.crypto-price');
    priceElements.forEach(el => {
        el.style.opacity = '0.5';
        setTimeout(() => el.style.opacity = '1', 200);
    });
}, 30000);

// Animate numbers on page load
document.addEventListener('DOMContentLoaded', function() {
    const stats = document.querySelectorAll('[data-animate="number"]');
    stats.forEach(stat => {
        const target = parseFloat(stat.textContent.replace(/[^0-9.-]+/g, ''));
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                stat.textContent = stat.getAttribute('data-format') || target.toFixed(2);
                clearInterval(timer);
            }
        }, 20);
    });
});
</script>
@endpush
@endsection
