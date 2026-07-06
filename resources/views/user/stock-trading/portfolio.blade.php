@extends('layouts.dash')

@section('title', 'My Portfolio')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div class="min-w-0 flex-1">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">My Portfolio</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">View all your stock holdings and performance</p>
        </div>
        <a href="{{ route('stock-trading.index') }}" class="bg-primary-500 hover:bg-primary-500 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm sm:text-base flex items-center space-x-2 whitespace-nowrap flex-shrink-0">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Market</span>
        </a>
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

    <!-- Portfolio Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
            <p class="text-gray-400 text-sm mb-1">Total Invested</p>
            <p class="text-2xl font-bold text-white">${{ number_format($totalInvested, 2) }}</p>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
            <p class="text-gray-400 text-sm mb-1">Current Value</p>
            <p class="text-2xl font-bold text-white">${{ number_format($totalValue, 2) }}</p>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
            <p class="text-gray-400 text-sm mb-1">Total P/L</p>
            <p class="text-2xl font-bold {{ $totalProfitLoss >= 0 ? 'text-green-400' : 'text-red-400' }}">
                {{ $totalProfitLoss >= 0 ? '+' : '' }}${{ number_format($totalProfitLoss, 2) }}
            </p>
            <p class="text-sm {{ $totalProfitLossPercent >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                {{ $totalProfitLossPercent >= 0 ? '+' : '' }}{{ number_format($totalProfitLossPercent, 2) }}%
            </p>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
            <p class="text-gray-400 text-sm mb-1">Available Cash</p>
            <p class="text-2xl font-bold text-white">${{ number_format($availableBalance, 2) }}</p>
        </div>
    </div>

    <!-- Holdings Table -->
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-white">Holdings ({{ count($assets) }})</h2>
        </div>

        @if(count($assets) > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left text-gray-400 font-medium py-3 px-2">Symbol</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-2">Quantity</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-2">Avg Cost</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-2">Current Price</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-2">Total Cost</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-2">Current Value</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-2">P/L</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-2">Today</th>
                        <th class="text-center text-gray-400 font-medium py-3 px-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                    <tr class="border-b border-gray-800 hover:bg-gray-900/30">
                        <td class="py-4 px-2">
                            <div class="flex items-center space-x-3">
                                @if($asset['logo_url'])
                                <img src="{{ $asset['logo_url'] }}" alt="{{ $asset['symbol'] }}" class="w-16 h-16 rounded-full" onerror="this.style.display='none'">
                                @else
                                <div class="w-16 h-16 rounded-full bg-gray-700 flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ substr($asset['symbol'], 0, 2) }}</span>
                                </div>
                                @endif
                                <div>
                                    <a href="{{ route('stock-trading.show', $asset['symbol']) }}" class="text-white font-semibold hover:text-primary-400">
                                        {{ $asset['symbol'] }}
                                    </a>
                                    <p class="text-gray-400 text-xs">{{ $asset['name'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-2 text-right text-white">{{ number_format($asset['quantity'], 4) }}</td>
                        <td class="py-4 px-2 text-right text-white">${{ number_format($asset['avg_cost'], 2) }}</td>
                        <td class="py-4 px-2 text-right text-white">${{ number_format($asset['current_price'], 2) }}</td>
                        <td class="py-4 px-2 text-right text-white">${{ number_format($asset['total_cost'], 2) }}</td>
                        <td class="py-4 px-2 text-right text-white font-semibold">${{ number_format($asset['current_value'], 2) }}</td>
                        <td class="py-4 px-2 text-right">
                            <span class="font-semibold {{ $asset['profit_loss'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $asset['profit_loss'] >= 0 ? '+' : '' }}${{ number_format($asset['profit_loss'], 2) }}
                            </span>
                            <div class="text-xs {{ $asset['profit_loss_percent'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $asset['profit_loss_percent'] >= 0 ? '+' : '' }}{{ number_format($asset['profit_loss_percent'], 2) }}%
                            </div>
                        </td>
                        <td class="py-4 px-2 text-right">
                            <span class="text-sm {{ $asset['percent_change'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $asset['percent_change'] >= 0 ? '+' : '' }}{{ number_format($asset['percent_change'], 2) }}%
                            </span>
                            <div class="text-xs text-gray-400">
                                {{ $asset['price_change'] >= 0 ? '+' : '' }}${{ number_format($asset['price_change'], 2) }}
                            </div>
                        </td>
                        <td class="py-4 px-2 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('stock-trading.show', $asset['symbol']) }}" 
                                   class="bg-primary-500 hover:bg-primary-700 text-white px-3 py-1 rounded text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('stock-trading.show', $asset['symbol']) }}#sell" 
                                   class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium">
                                    Sell
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Portfolio Summary -->
        <div class="mt-6 pt-6 border-t border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-400 text-sm">Number of Holdings</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ count($assets) }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Trades</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ array_sum(array_column($assets, 'trades_count')) }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Portfolio Value</p>
                    <p class="text-2xl font-bold text-white mt-1">${{ number_format($totalValue + $availableBalance, 2) }}</p>
                </div>
            </div>
        </div>

        @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-400 mb-2">No Holdings Yet</h3>
            <p class="text-gray-500 mb-4">Start building your portfolio by buying stocks</p>
            <a href="{{ route('stock-trading.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Browse Stocks
            </a>
        </div>
        @endif
    </div>
</div>

<script>
// Auto-switch to sell tab when clicking sell button
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#sell') {
        setTimeout(function() {
            if (typeof switchTradeType === 'function') {
                switchTradeType('sell');
            }
        }, 100);
    }
});
</script>
@endsection
