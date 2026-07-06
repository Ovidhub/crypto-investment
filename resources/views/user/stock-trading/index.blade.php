@extends('layouts.dash')

@section('title', 'Stock Trading')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div class="min-w-0 flex-1">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">Stock Trading</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">Trade stocks from major exchanges worldwide</p>
        </div>
        <a href="{{ route('stock-trading.portfolio') }}" class="bg-primary-500 hover:bg-primary-500 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm sm:text-base flex items-center space-x-2 whitespace-nowrap flex-shrink-0">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span>My Portfolio</span>
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

    @if($errors->any())
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Portfolio Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Available Balance</p>
                    <p class="text-2xl font-bold text-white mt-1">${{ number_format($availableBalance, 2) }}</p>
                </div>
                <div class="bg-primary-500/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Portfolio Value</p>
                    <p class="text-2xl font-bold text-white mt-1">${{ number_format($portfolioValue, 2) }}</p>
                </div>
                <div class="bg-primary-500/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Invested</p>
                    <p class="text-2xl font-bold text-white mt-1">${{ number_format($totalInvested, 2) }}</p>
                </div>
                <div class="bg-blue-500/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total P/L</p>
                    <p class="text-2xl font-bold {{ $totalProfitLoss >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                        {{ $totalProfitLoss >= 0 ? '+' : '' }}${{ number_format($totalProfitLoss, 2) }}
                    </p>
                    <p class="text-sm {{ $portfolioROI >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                        ROI: {{ $portfolioROI >= 0 ? '+' : '' }}{{ number_format($portfolioROI, 2) }}%
                    </p>
                </div>
                <div class="bg-{{ $totalProfitLoss >= 0 ? 'green' : 'red' }}-500/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-{{ $totalProfitLoss >= 0 ? 'green' : 'red' }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $totalProfitLoss >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Watchlist & Search -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Search Stocks -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-white mb-4">Search Stocks</h2>
                
                <div class="relative mb-4">
                    <input type="text" id="stockSearch" placeholder="Search by symbol or name..." 
                        class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-primary-500">
                    <div class="absolute right-3 top-2.5">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div id="searchResults" class="hidden space-y-2 max-h-60 overflow-y-auto"></div>
            </div>

            <!-- Watchlist -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">My Watchlist</h2>
                    <span class="text-sm text-gray-400">{{ count($watchlist) }}/{{ $maxWatchlistItems }}</span>
                </div>

                <div class="space-y-2">
                    @forelse($watchlist as $item)
                    @php
                        $stock = $item->stockPrice;
                    @endphp
                    @if($stock)
                    <div class="bg-gray-900/50 border border-gray-700/50 rounded-lg p-3 hover:border-primary-500/50 transition-colors">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('stock-trading.show', $stock->symbol) }}" class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $stock->logo_url }}" alt="{{ $stock->symbol }}" class="w-8 h-8 rounded-full" onerror="this.src='data:image/svg+xml;base64,{{ base64_encode($stock->getPlaceholderSvg()) }}'">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-white font-semibold">{{ $stock->symbol }}</p>
                                                <p class="text-xs text-gray-400">{{ $stock->name }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-white font-semibold">${{ number_format($stock->price, 2) }}</p>
                                                <p class="text-xs {{ $stock->percent_change >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ $stock->percent_change >= 0 ? '+' : '' }}{{ number_format($stock->percent_change, 2) }}%
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <button onclick="removeFromWatchlist('{{ $stock->symbol }}')" class="text-red-400 hover:text-red-300 p-1 ml-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endif
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p>No stocks in watchlist</p>
                        <p class="text-sm mt-1">Search and add stocks to start tracking</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Chart & Trade -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Available Stocks -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-white mb-4">Available Stocks</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                    @forelse($allStocks as $stock)
                    <div class="bg-gray-900/50 border border-gray-700/50 rounded-lg p-3 hover:border-primary-500/50 transition-colors cursor-pointer" onclick="updateChart('{{ $stock->exchange }}', '{{ $stock->symbol }}', '{{ $stock->name }}')">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <img src="{{ $stock->logo_url }}" alt="{{ $stock->symbol }}" class="w-10 h-10 rounded-full flex-shrink-0" onerror="this.src='data:image/svg+xml;base64,{{ base64_encode($stock->getPlaceholderSvg()) }}'">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-semibold">{{ $stock->symbol }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $stock->name }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-white font-semibold">${{ number_format($stock->price, 2) }}</p>
                                <p class="text-xs {{ $stock->percent_change >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $stock->percent_change >= 0 ? '+' : '' }}{{ number_format($stock->percent_change, 2) }}%
                                </p>
                            </div>
                            <div class="flex space-x-1 flex-shrink-0">
                                <a href="{{ route('stock-trading.show', $stock->symbol) }}" class="bg-primary-500 hover:bg-primary-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap">
                                    Trade
                                </a>
                                <button onclick="addToWatchlist('{{ $stock->symbol }}')" class="bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p>No stocks available</p>
                        <p class="text-sm mt-1">Admin needs to import stocks</p>
                    </div>
                    @endforelse
                </div>
                
                @if($allStocks->hasPages())
                <div class="mt-4">
                    {{ $allStocks->links() }}
                </div>
                @endif
            </div>

            <!-- TradingView Chart -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-white mb-4">Market Overview</h2>
                
                <!-- TradingView Widget -->
                <div class="tradingview-widget-container" style="height:1400px; width:100%;">
                    <div id="tradingview_widget" style="height:100%; width:100%;"></div>
                </div>
            </div>

            <!-- Recent Trades -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-white mb-4">Recent Trades</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Date</th>
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Symbol</th>
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Type</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Quantity</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Price</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Total</th>
                                <th class="text-center text-gray-400 font-medium py-2 px-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTrades as $trade)
                            <tr class="border-b border-gray-800 hover:bg-gray-900/30">
                                <td class="py-3 px-2 text-gray-300 text-sm">{{ $trade->created_at->format('M d, Y') }}</td>
                                <td class="py-3 px-2">
                                    <a href="{{ route('stock-trading.show', $trade->stock_symbol) }}" class="text-white hover:text-primary-500 font-semibold">
                                        {{ $trade->stock_symbol }}
                                    </a>
                                </td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $trade->trade_type === 'buy' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                        {{ strtoupper($trade->trade_type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right text-white">{{ $trade->quantity }}</td>
                                <td class="py-3 px-2 text-right text-white">${{ number_format($trade->price, 2) }}</td>
                                <td class="py-3 px-2 text-right text-white">${{ number_format($trade->net_amount, 2) }}</td>
                                <td class="py-3 px-2 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $trade->status === 'completed' ? 'bg-blue-500/10 text-blue-400' : 'bg-yellow-500/10 text-yellow-400' }}">
                                        {{ ucfirst($trade->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p>No trades yet</p>
                                        <p class="text-sm mt-1">Start trading stocks to see your history here</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TradingView Widget Script -->
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script type="text/javascript">
// Store widget instance globally
let tradingViewWidget = null;

// Update chart with new symbol
function updateChart(exchange, symbol, name) {
    const container = document.getElementById('tradingview_widget');
    
    // Clear existing widget
    container.innerHTML = '';
    
    // Create new widget with selected symbol
    tradingViewWidget = new TradingView.widget({
        "autosize": true,
        "symbol": exchange + ":" + symbol,
        "interval": "D",
        "timezone": "America/New_York",
        "theme": "dark",
        "style": "1",
        "locale": "en",
        "toolbar_bg": "#f1f3f6",
        "enable_publishing": false,
        "allow_symbol_change": true,
        "container_id": "tradingview_widget",
        "height": 1400,
        "hide_top_toolbar": false,
        "hide_side_toolbar": false,
        "save_image": false
    });
    
    // Update chart title
    document.querySelector('.lg\\:col-span-2 h2').textContent = name + ' (' + symbol + ')' + ' Chart';
}

// Add to Watchlist - Global function
function addToWatchlist(symbol) {
    fetch('{{ route('stock-trading.watchlist.add') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ symbol: symbol })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to add stock to watchlist');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add stock to watchlist');
    });
}

// Remove from Watchlist - Global function
function removeFromWatchlist(symbol) {
    if (!confirm('Remove this stock from your watchlist?')) {
        return;
    }
    
    fetch(`{{ route('stock-trading.watchlist.remove', ':symbol') }}`.replace(':symbol', symbol), {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to remove stock from watchlist');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to remove stock from watchlist');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize TradingView Widget
    tradingViewWidget = new TradingView.widget({
        "autosize": true,
        "symbol": "NASDAQ:AAPL",
        "interval": "D",
        "timezone": "America/New_York",
        "theme": "dark",
        "style": "1",
        "locale": "en",
        "toolbar_bg": "#f1f3f6",
        "enable_publishing": false,
        "allow_symbol_change": true,
        "container_id": "tradingview_widget",
        "height": 1400,
        "hide_top_toolbar": false,
        "hide_side_toolbar": false,
        "save_image": false
    });

    // Stock Search
    let searchTimeout;
    const searchInput = document.getElementById('stockSearch');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 1) {
            searchResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('stock-trading.api.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.results.length > 0) {
                        searchResults.innerHTML = data.results.map(stock => `
                            <div class="bg-gray-900/50 border border-gray-700/50 rounded-lg p-3 hover:border-primary-500/50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-white font-semibold">${stock.symbol}</p>
                                        <p class="text-xs text-gray-400">${stock.name}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="/user/stock-trading/${stock.symbol}" class="bg-primary-500 hover:bg-primary-600 text-white px-3 py-1 rounded text-sm">
                                            View
                                        </a>
                                        <button onclick="addToWatchlist('${stock.symbol}')" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                        searchResults.classList.remove('hidden');
                    } else {
                        searchResults.innerHTML = '<div class="text-center py-4 text-gray-400">No results found</div>';
                        searchResults.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="text-center py-4 text-red-400">Search failed</div>';
                    searchResults.classList.remove('hidden');
                });
        }, 500);
    });
});
</script>
@endsection
