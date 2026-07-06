@extends('layouts.dash')
@section('title', 'Crypto Trading')

@section('styles')
<style>
    #tradingChart {
        height: 400px;
        width: 100%;
    }
    .timeframe-btn.active {
        background-color: theme('colors.primary.DEFAULT');
        color: white;
    }
    .order-tab.active {
        color: theme('colors.primary.DEFAULT');
        border-color: theme('colors.primary.DEFAULT');
    }
    .order-type-btn.active {
        background-color: theme('colors.dark.100');
    }
    .market-positive {
        color: #4A9D7F;
    }
    .market-negative {
        color: #FF6B6B;
    }
    .buy-btn {
        background-color: #4A9D7F;
    }
    .buy-btn:hover {
        background-color: #3C7F65;
    }
    .sell-btn {
        background-color: #FF6B6B;
    }
    .sell-btn:hover {
        background-color: #FF3838;
    }
    
    /* Order book styles */
    .orderbook-row {
        position: relative;
        height: 24px;
    }
    .orderbook-bg {
        position: absolute;
        top: 0;
        height: 100%;
        z-index: 1;
    }
    .orderbook-bid-bg {
        right: 0;
        background-color: rgba(74, 157, 127, 0.2);
    }
    .orderbook-ask-bg {
        left: 0;
        background-color: rgba(255, 107, 107, 0.2);
    }
    .orderbook-content {
        position: relative;
        z-index: 2;
    }
    
    /* Crypto Symbol dropdown styling */
    #cryptoSymbolSelect {
        max-height: 48px;
        overflow-y: auto;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 1rem;
        padding-right: 2.5rem;
    }
    
    #cryptoSymbolSelect optgroup {
        font-weight: 600;
        color: var(--text-primary);
    }
    
    #cryptoSymbolSelect option {
        padding: 8px;
    }
    
    /* Improve responsive layout */
    @media (max-width: 640px) {
        #cryptoSymbolSelect {
            font-size: 0.875rem;
        }
        
        .symbol-display {
            font-size: 1.5rem;
        }
        
        .symbol-description {
            font-size: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
    @if(isset($isFallback) && $isFallback)
    <div class="alert alert-warning mb-4" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Limited Data Available</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>
                        We're currently using a backup data source for cryptocurrency information. Some features may be limited.
                        @if(isset($ticker['source']) && $ticker['source'] === 'cryptocompare')
                            Data is being provided by CryptoCompare API.
                        @elseif(isset($ticker['source']) && $ticker['source'] === 'huobi')
                            Data is being provided by Huobi API.
                        @elseif(isset($ticker['source']) && $ticker['source'] === 'dummy_data')
                            <strong>Note:</strong> The price information displayed is not real-time market data.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Trading Header -->
    <div class="flex flex-col space-y-4 md:space-y-0 md:flex-row md:justify-between mb-6">
        <!-- Left side with back button and symbol information -->
        <div class="flex flex-col space-y-4 w-full md:w-auto">
            <div class="flex items-center justify-between">
                <a href="{{ route('trading') }}" class="mr-2 text-primary hover:text-primary-dark">
                    <i data-lucide="chevron-left" class="h-5 w-5 dark:text-gray-400 text-gray-600"></i>
                    <span class="hidden sm:inline ml-1">Back to Markets</span>
                </a>
                
                <!-- Refresh button (moved for mobile) -->
                <button id="refreshBtn" class="p-2 rounded-full hover:bg-light-100 dark:hover:bg-dark-50 md:hidden">
                    <i data-lucide="refresh-cw" class="h-5 w-5 dark:text-gray-400 text-gray-600"></i>
                </button>
            </div>
            
            <!-- Crypto Symbol Dropdown -->
            <div class="relative w-full sm:max-w-xs">
                <select id="cryptoSymbolSelect" class="block w-full px-4 py-2 text-gray-700 bg-white border border-light-200 dark:border-dark-100 dark:bg-dark-50 dark:text-white rounded shadow-sm focus:outline-none focus:ring-primary focus:border-primary appearance-none">
                    <option value="{{ $symbol }}" selected>{{ $symbol }}</option>
                    <option value="" disabled>Loading more symbols...</option>
                </select>
                <div id="symbolDropdownLoader" class="absolute inset-y-0 right-8 flex items-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-spinner fa-spin text-xs"></i>
                </div>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
            
            <!-- Symbol name -->
            <div class="flex items-center justify-between">
                <h1 class="symbol-display font-bold dark:text-white">
                    {{ $symbol }} <span class="symbol-description font-normal text-gray-500 block sm:inline">Crypto</span>
                </h1>
                
                <!-- Alpine.js Watchlist Star Button -->
                <div
                    x-data="{
                        inWatchlist: {{ $inWatchlist ? 'true' : 'false' }},
                        itemId: null,
                        symbol: '{{ $symbol }}',
                        toggleWatchlist() {
                            if (this.inWatchlist) {
                                this.removeFromWatchlist();
                            } else {
                                this.addToWatchlist();
                            }
                            this.inWatchlist = !this.inWatchlist;
                        },
                        addToWatchlist() {
                            fetch('/dashboard/api/trading/watchlist/add', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    symbol: this.symbol,
                                    type: 'crypto'
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.itemId = data.item_id;
                                    if (data.item) {
                                        this.addToTable(data.item, data.item_id);
                                    }
                                } else {
                                    this.inWatchlist = false;
                                    console.error('Error adding to watchlist:', data.message);
                                }
                            })
                            .catch(err => {
                                this.inWatchlist = false;
                                console.error('Error adding to watchlist:', err);
                            });
                        },
                        removeFromWatchlist() {
                            if (!this.itemId) return;
                            
                            fetch(`/dashboard/api/trading/watchlist/remove/${this.itemId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.itemId = null;
                                    const row = document.querySelector(`.watchlist-row[data-symbol='${data.symbol}']`);
                                    if (row) row.remove();
                                } else {
                                    this.inWatchlist = true;
                                    console.error('Error removing from watchlist:', data.message);
                                }
                            })
                            .catch(err => {
                                this.inWatchlist = true;
                                console.error('Error removing from watchlist:', err);
                            });
                        },
                        updateRowPrice(row, symbol) {
                            if (!row || !symbol) return;
                            
                            fetch(`/dashboard/api/trading/crypto/symbol?symbol=${symbol}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (!data) return;
                                    
                                    const priceElement = row.querySelector('.crypto-price');
                                    const changeElement = row.querySelector('.crypto-change');
                                    
                                    if (priceElement) {
                                        const price = parseFloat(data.lastPrice);
                                        priceElement.textContent = isNaN(price) ? '-' : '$' + price.toFixed(2);
                                    }
                                    
                                    if (changeElement) {
                                        const changePercent = parseFloat(data.priceChangePercent);
                                        if (!isNaN(changePercent)) {
                                            changeElement.textContent = `${changePercent >= 0 ? '+' : ''}${changePercent.toFixed(2)}%`;
                                            changeElement.className = `text-sm ${changePercent >= 0 ? 'text-green-500' : 'text-red-500'}`;
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error(`Error fetching price for ${symbol}:`, error);
                                });
                        },
                        addToTable(item, itemId) {
                            const watchlistBody = document.getElementById('watchlistBody');
                            if (!watchlistBody) return;
                            
                            const newRow = document.createElement('tr');
                            newRow.className = 'watchlist-row';
                            newRow.dataset.symbol = item.symbol;
                            newRow.dataset.itemId = itemId;
                            
                            newRow.innerHTML = `
                                <td class='px-4 py-2 whitespace-nowrap'>
                                    <a href='{{ route('trading.crypto') }}?symbol=${item.symbol}' class='block'>
                                        <div class='text-sm font-medium dark:text-white text-dark'>${item.symbol}</div>
                                        <div class='text-xs text-gray-500'>${item.base_currency}/${item.quote_currency}</div>
                                    </a>
                                </td>
                                <td class='px-4 py-2 whitespace-nowrap text-right'>
                                    <div class='text-sm dark:text-gray-300 text-gray-700 crypto-price'>-</div>
                                </td>
                                <td class='px-4 py-2 whitespace-nowrap text-right'>
                                    <div class='text-sm crypto-change'>-</div>
                                </td>
                                <td class='px-4 py-2 whitespace-nowrap text-right'>
                                    <span class='inline-flex items-center px-2 py-1 text-xs font-medium rounded text-gray-500 bg-gray-100'>
                                        Current
                                    </span>
                                </td>
                            `;
                            
                            watchlistBody.insertBefore(newRow, watchlistBody.firstChild);
                            
                            // Update the price immediately after adding to table
                            setTimeout(() => this.updateRowPrice(newRow, item.symbol), 100);
                        },
                        init() {
                            if (this.inWatchlist) {
                                fetch('/dashboard/api/trading/watchlist/add', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        symbol: this.symbol,
                                        type: 'crypto'
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.item_id) {
                                        this.itemId = data.item_id;
                                    }
                                })
                                .catch(console.error);
                            }
                        }
                    }"
                    x-init="init()"
                >
                    <button 
                        @click="toggleWatchlist()"
                        class="px-3 py-1.5 rounded border dark:border-dark-100 border-light-200 hover:border-primary focus:outline-none transition duration-150"
                        :class="inWatchlist ? 'bg-gray-100 dark:bg-dark-100' : ''"
                    >
                        <i :class="inWatchlist ? 'fas fa-star text-primary' : 'far fa-star text-gray-500'"></i>
                        <span x-text="inWatchlist ? 'Watchlist' : 'Add'" class="ml-1 text-sm sm:hidden"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Right side with price and change -->
        <div class="flex items-center space-x-3">
            @if($ticker)
            <div class="text-sm font-medium dark:text-white text-dark">{{ $ticker['lastPrice'] }} USDT</div>
            <div class="text-sm font-medium {{ (float)$ticker['priceChangePercent'] >= 0 ? 'market-positive' : 'market-negative' }}">
                {{ (float)$ticker['priceChangePercent'] >= 0 ? '+' : '' }}{{ $ticker['priceChangePercent'] }}%
            </div>
            @endif
            <button class="p-2 rounded-full hover:bg-light-100 dark:hover:bg-dark-50 hidden md:block" id="desktopRefreshBtn">
                <i data-lucide="refresh-cw" class="h-5 w-5 dark:text-gray-400 text-gray-600"></i>
            </button>
        </div>
    </div>

    <!-- Trading Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <!-- Main Chart & Order Book - 3 Columns on large screens -->
        <div class="lg:col-span-3 space-y-4">
            <!-- Chart Card -->
            <div class="dark:bg-dark-100 bg-light-200 rounded-xl p-4 border dark:border-dark-200 border-light-300">
                <!-- Chart Header with Timeframes -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold dark:text-white text-dark">Price Chart</h2>
                    <div class="flex items-center space-x-2 overflow-x-auto pb-1">
                        <button class="timeframe-btn px-2 py-1 text-xs font-medium rounded {{ $timeframe == '1m' ? 'active bg-primary text-white' : 'dark:bg-dark-50 bg-light-100 dark:text-gray-300 text-gray-700' }}" data-timeframe="1m">1m</button>
                        <button class="timeframe-btn px-2 py-1 text-xs font-medium rounded {{ $timeframe == '5m' ? 'active bg-primary text-white' : 'dark:bg-dark-50 bg-light-100 dark:text-gray-300 text-gray-700' }}" data-timeframe="5m">5m</button>
                        <button class="timeframe-btn px-2 py-1 text-xs font-medium rounded {{ $timeframe == '15m' ? 'active bg-primary text-white' : 'dark:bg-dark-50 bg-light-100 dark:text-gray-300 text-gray-700' }}" data-timeframe="15m">15m</button>
                        <button class="timeframe-btn px-2 py-1 text-xs font-medium rounded {{ $timeframe == '1h' ? 'active bg-primary text-white' : 'dark:bg-dark-50 bg-light-100 dark:text-gray-300 text-gray-700' }}" data-timeframe="1h">1h</button>
                        <button class="timeframe-btn px-2 py-1 text-xs font-medium rounded {{ $timeframe == '4h' ? 'active bg-primary text-white' : 'dark:bg-dark-50 bg-light-100 dark:text-gray-300 text-gray-700' }}" data-timeframe="4h">4h</button>
                        <button class="timeframe-btn px-2 py-1 text-xs font-medium rounded {{ $timeframe == '1d' ? 'active bg-primary text-white' : 'dark:bg-dark-50 bg-light-100 dark:text-gray-300 text-gray-700' }}" data-timeframe="1d">1d</button>
                    </div>
                </div>
                
                <!-- TradingView Chart Container -->
                <div id="tradingChart" class="w-full h-96"></div>
                
                <!-- Chart Information -->
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="dark:bg-dark-50 bg-light-100 p-2 rounded">
                        <div class="text-xs text-gray-500">24h High</div>
                        <div class="text-sm font-medium dark:text-white text-dark" id="high24h">
                            {{ number_format((float)$ticker['highPrice'], 2) }}
                        </div>
                    </div>
                    <div class="dark:bg-dark-50 bg-light-100 p-2 rounded">
                        <div class="text-xs text-gray-500">24h Low</div>
                        <div class="text-sm font-medium dark:text-white text-dark" id="low24h">
                            {{ number_format((float)$ticker['lowPrice'], 2) }}
                        </div>
                    </div>
                    <div class="dark:bg-dark-50 bg-light-100 p-2 rounded">
                        <div class="text-xs text-gray-500">24h Volume</div>
                        <div class="text-sm font-medium dark:text-white text-dark" id="volume24h">
                            {{ number_format((float)$ticker['volume'], 2) }}
                        </div>
                    </div>
                    <div class="dark:bg-dark-50 bg-light-100 p-2 rounded">
                        <div class="text-xs text-gray-500">24h Change</div>
                        <div class="text-sm font-medium {{ (float)$ticker['priceChangePercent'] >= 0 ? 'market-positive' : 'market-negative' }}" id="change24h">
                            {{ (float)$ticker['priceChangePercent'] >= 0 ? '+' : '' }}{{ number_format((float)$ticker['priceChangePercent'], 2) }}%
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Trading Information Tabs -->
            <div class="dark:bg-dark-100 bg-light-200 rounded-xl border dark:border-dark-200 border-light-300">
                <!-- Tabs Header -->
                <div class="flex border-b dark:border-dark-200 border-light-300">
                    <button class="trade-tab-btn flex-1 py-3 px-4 text-sm font-medium border-b-2 dark:border-primary border-primary dark:text-white text-dark" data-tab="orderbook">
                        Order Book
                    </button>
                    <button class="trade-tab-btn flex-1 py-3 px-4 text-sm font-medium border-b-2 border-transparent dark:text-gray-400 text-gray-600 hover:dark:text-white hover:text-dark" data-tab="trades">
                        Recent Trades
                    </button>
                    <button class="trade-tab-btn flex-1 py-3 px-4 text-sm font-medium border-b-2 border-transparent dark:text-gray-400 text-gray-600 hover:dark:text-white hover:text-dark" data-tab="openorders">
                        Open Orders
                    </button>
                </div>
                
                <!-- Tab Contents -->
                <div class="p-4">
                    <!-- Order Book Tab -->
                    <div class="trade-tab" id="orderbook-tab">
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Price Column Header -->
                            <div class="text-center">
                                <div class="text-xs font-medium dark:text-gray-400 text-gray-600 mb-2">Price (USDT)</div>
                                <!-- Simulated Asks (Sellers) -->
                                <div class="space-y-1 mb-2">
                                    <!-- This would be generated dynamically from API data -->
                                    @for($i = 5; $i > 0; $i--)
                                    @php
                                        $price = $ticker ? (float)$ticker['lastPrice'] * (1 + $i * 0.0005) : 10000 + ($i * 50);
                                        $amount = rand(10, 100) / 100;
                                        $width = ($amount / 1) * 100; // For visualizing depth
                                    @endphp
                                    <div class="orderbook-row">
                                        <div class="orderbook-bg orderbook-ask-bg" style="width: {{ $width }}%"></div>
                                        <div class="orderbook-content flex justify-center">
                                            <span class="text-xs market-negative">{{ number_format($price, 2) }}</span>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                                
                                <!-- Current Price -->
                                <div class="py-1 my-1 dark:bg-dark-50 bg-light-100 rounded">
                                    <span class="text-sm font-medium dark:text-white text-dark">
                                        {{ $ticker ? number_format((float)$ticker['lastPrice'], 2) : '0.00' }}
                                    </span>
                                </div>
                                
                                <!-- Simulated Bids (Buyers) -->
                                <div class="space-y-1 mt-2">
                                    @for($i = 1; $i <= 5; $i++)
                                    @php
                                        $price = $ticker ? (float)$ticker['lastPrice'] * (1 - $i * 0.0005) : 10000 - ($i * 50);
                                        $amount = rand(10, 100) / 100;
                                        $width = ($amount / 1) * 100; // For visualizing depth
                                    @endphp
                                    <div class="orderbook-row">
                                        <div class="orderbook-bg orderbook-bid-bg" style="width: {{ $width }}%"></div>
                                        <div class="orderbook-content flex justify-center">
                                            <span class="text-xs market-positive">{{ number_format($price, 2) }}</span>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                            
                            <!-- Amount Column -->
                            <div class="text-center">
                                <div class="text-xs font-medium dark:text-gray-400 text-gray-600 mb-2">Amount (BTC)</div>
                                <div class="space-y-1 mb-2">
                                    @for($i = 5; $i > 0; $i--)
                                    @php
                                        $amount = rand(10, 100) / 100;
                                    @endphp
                                    <div class="orderbook-row">
                                        <div class="orderbook-content flex justify-center">
                                            <span class="text-xs dark:text-gray-300 text-gray-700">{{ number_format($amount, 4) }}</span>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                                
                                <div class="py-1 my-1 dark:bg-dark-50 bg-light-100 rounded">
                                    <span class="text-xs text-gray-500">Price (USDT)</span>
                                </div>
                                
                                <div class="space-y-1 mt-2">
                                    @for($i = 1; $i <= 5; $i++)
                                    @php
                                        $amount = rand(10, 100) / 100;
                                    @endphp
                                    <div class="orderbook-row">
                                        <div class="orderbook-content flex justify-center">
                                            <span class="text-xs dark:text-gray-300 text-gray-700">{{ number_format($amount, 4) }}</span>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                            
                            <!-- Total Column -->
                            <div class="text-center">
                                <div class="text-xs font-medium dark:text-gray-400 text-gray-600 mb-2">Total (USDT)</div>
                                <div class="space-y-1 mb-2">
                                    @php
                                        $runningTotal = 0;
                                    @endphp
                                    @for($i = 5; $i > 0; $i--)
                                    @php
                                        $price = $ticker ? (float)$ticker['lastPrice'] * (1 + $i * 0.0005) : 10000 + ($i * 50);
                                        $amount = rand(10, 100) / 100;
                                        $total = $price * $amount;
                                        $runningTotal += $total;
                                    @endphp
                                    <div class="orderbook-row">
                                        <div class="orderbook-content flex justify-center">
                                            <span class="text-xs dark:text-gray-300 text-gray-700">{{ number_format($runningTotal, 2) }}</span>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                                
                                <div class="py-1 my-1 dark:bg-dark-50 bg-light-100 rounded">
                                    <span class="text-xs text-gray-500">Total (USDT)</span>
                                </div>
                                
                                @php
                                    $runningTotal = 0;
                                @endphp
                                <div class="space-y-1 mt-2">
                                    @for($i = 1; $i <= 5; $i++)
                                    @php
                                        $price = $ticker ? (float)$ticker['lastPrice'] * (1 - $i * 0.0005) : 10000 - ($i * 50);
                                        $amount = rand(10, 100) / 100;
                                        $total = $price * $amount;
                                        $runningTotal += $total;
                                    @endphp
                                    <div class="orderbook-row">
                                        <div class="orderbook-content flex justify-center">
                                            <span class="text-xs dark:text-gray-300 text-gray-700">{{ number_format($runningTotal, 2) }}</span>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Trades Tab -->
                    <div class="trade-tab hidden" id="trades-tab">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-full divide-y dark:divide-dark-200 divide-light-300">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y dark:divide-dark-200 divide-light-300" id="recentTradesBody">
                                    <!-- Recent trades will be populated dynamically via JavaScript -->
                                    @for($i = 0; $i < 10; $i++)
                                    @php
                                        $isBuy = rand(0, 1) == 1;
                                        $price = $ticker ? (float)$ticker['lastPrice'] * (1 + (rand(-10, 10) / 1000)) : 10000 + (rand(-500, 500));
                                        $amount = rand(1, 100) / 100;
                                        $time = now()->subMinutes(rand(1, 30))->format('H:i:s');
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="text-sm font-medium {{ $isBuy ? 'market-positive' : 'market-negative' }}">
                                                {{ number_format($price, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right">
                                            <div class="text-sm dark:text-gray-300 text-gray-700">
                                                {{ number_format($amount, 4) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right">
                                            <div class="text-sm dark:text-gray-400 text-gray-600">
                                                {{ $time }}
                                            </div>
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Open Orders Tab -->
                    <div class="trade-tab hidden" id="openorders-tab">
                        @if(isset($openOrders) && count($openOrders) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-full divide-y dark:divide-dark-200 divide-light-300">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y dark:divide-dark-200 divide-light-300">
                                    @foreach($openOrders as $order)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="text-sm font-medium {{ $order->side === 'buy' ? 'market-positive' : 'market-negative' }}">
                                                {{ ucfirst($order->side) }} {{ ucfirst($order->order_type) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right">
                                            <div class="text-sm dark:text-gray-300 text-gray-700">
                                                {{ $order->price ?? 'Market' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right">
                                            <div class="text-sm dark:text-gray-300 text-gray-700">
                                                {{ $order->quantity }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right">
                                            <div class="text-sm dark:text-gray-400 text-gray-600">
                                                {{ $order->created_at->format('H:i:s') }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right">
                                            <button class="cancel-order-btn px-2 py-1 text-xs font-medium rounded text-danger hover:text-red-700 focus:outline-none" data-order-id="{{ $order->id }}">
                                                Cancel
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="py-8 text-center">
                            <i data-lucide="info" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                            <p class="text-sm dark:text-gray-400 text-gray-600">No open orders for this symbol</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Form - 1 Column on large screens -->
        <div class="lg:col-span-1">
            <div class="dark:bg-dark-100 bg-light-200 rounded-xl p-4 border dark:border-dark-200 border-light-300">
                <!-- Order Form Tabs (Buy/Sell) -->
                <div class="flex border-b dark:border-dark-200 border-light-300 mb-4">
                    <button class="order-tab flex-1 py-2 text-sm font-medium border-b-2 dark:border-primary border-primary text-primary active" data-side="buy">
                        Buy
                    </button>
                    <button class="order-tab flex-1 py-2 text-sm font-medium border-b-2 border-transparent dark:text-gray-400 text-gray-600" data-side="sell">
                        Sell
                    </button>
                </div>
                
                <!-- Order Form -->
                <form id="orderForm" class="space-y-4">
                    <input type="hidden" name="symbol" value="{{ $symbol }}">
                    <input type="hidden" name="market_type" value="crypto">
                    <input type="hidden" name="side" value="buy" id="orderSide">
                    
                    <!-- Order Type Selection -->
                    <div>
                        <label class="text-xs font-medium dark:text-gray-400 text-gray-600 mb-1 block">Order Type</label>
                        <div class="flex space-x-2">
                            <button type="button" class="order-type-btn flex-1 py-2 px-3 text-xs font-medium rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark active" data-type="market">
                                Market
                            </button>
                            <button type="button" class="order-type-btn flex-1 py-2 px-3 text-xs font-medium rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark" data-type="limit">
                                Limit
                            </button>
                            <button type="button" class="order-type-btn flex-1 py-2 px-3 text-xs font-medium rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark" data-type="stop_limit">
                                Stop Limit
                            </button>
                        </div>
                        <input type="hidden" name="order_type" value="market" id="orderType">
                    </div>
                    
                    <!-- Price Input (for limit orders) -->
                    <div id="priceInputContainer" class="hidden">
                        <label for="priceInput" class="text-xs font-medium dark:text-gray-400 text-gray-600 mb-1 block">Price (USDT)</label>
                        <div class="relative">
                            <input type="text" id="priceInput" name="price" class="w-full px-3 py-2 rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark dark:border-dark-200 border-light-300 border focus:outline-none focus:ring-1 focus:ring-primary" placeholder="Enter price">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-xs dark:text-gray-400 text-gray-600">USDT</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stop Price Input (for stop-limit orders) -->
                    <div id="stopPriceInputContainer" class="hidden">
                        <label for="stopPriceInput" class="text-xs font-medium dark:text-gray-400 text-gray-600 mb-1 block">Stop Price (USDT)</label>
                        <div class="relative">
                            <input type="text" id="stopPriceInput" name="stop_price" class="w-full px-3 py-2 rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark dark:border-dark-200 border-light-300 border focus:outline-none focus:ring-1 focus:ring-primary" placeholder="Enter stop price">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-xs dark:text-gray-400 text-gray-600">USDT</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Amount Input -->
                    <div>
                        <label for="amountInput" class="text-xs font-medium dark:text-gray-400 text-gray-600 mb-1 block">Amount (BTC)</label>
                        <div class="relative">
                            <input type="text" id="amountInput" name="quantity" class="w-full px-3 py-2 rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark dark:border-dark-200 border-light-300 border focus:outline-none focus:ring-1 focus:ring-primary" placeholder="Enter amount">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-xs dark:text-gray-400 text-gray-600">BTC</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Percentage Buttons -->
                    <div class="flex space-x-2">
                        <button type="button" class="percentage-btn flex-1 py-1 text-xs font-medium rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark" data-percentage="25">25%</button>
                        <button type="button" class="percentage-btn flex-1 py-1 text-xs font-medium rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark" data-percentage="50">50%</button>
                        <button type="button" class="percentage-btn flex-1 py-1 text-xs font-medium rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark" data-percentage="75">75%</button>
                        <button type="button" class="percentage-btn flex-1 py-1 text-xs font-medium rounded dark:bg-dark-50 bg-light-100 dark:text-white text-dark" data-percentage="100">100%</button>
                    </div>
                    
                    <!-- Total -->
                    <div>
                        <label class="text-xs font-medium dark:text-gray-400 text-gray-600 mb-1 block">Total (USDT)</label>
                        <div class="text-sm font-medium dark:text-white text-dark p-2 dark:bg-dark-50 bg-light-100 rounded" id="orderTotal">
                            0.00
                        </div>
                    </div>
                    
                    <!-- Available Balance -->
                    <div class="balance-section">
                        <label class="text-xs font-medium dark:text-gray-400 text-gray-600 mb-1 block">Available Balance</label>
                        <div class="text-sm font-medium dark:text-white text-dark p-2 dark:bg-dark-50 bg-light-100 rounded flex justify-between">
                            <span title="Your current account balance">{{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2, '.', ',') }}</span>
                            <span id="balanceAfterOrder" class="text-gray-500" title="Balance after this order is executed">{{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2, '.', ',') }}</span>
                        </div>
                        <p class="text-xs mt-1 text-gray-500">Balance shown is for USDT trading</p>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" id="submitOrderBtn" class="w-full py-3 px-4 text-sm font-medium rounded text-white buy-btn">
                        Buy BTC
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Watchlist & Market Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Watchlist -->
        <div class="dark:bg-dark-100 bg-light-200 rounded-xl p-4 border dark:border-dark-200 border-light-300">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold dark:text-white text-dark">Watchlist</h2>
                <button class="text-sm text-primary hover:text-primary-600" id="refreshWatchlistBtn">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            
            <div class="overflow-y-auto max-h-60">
                <table class="w-full divide-y dark:divide-dark-200 divide-light-300">
                    <thead class="dark:bg-dark-50 bg-light-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">24h</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-dark-200 divide-light-300" id="watchlistBody">
                        @foreach($watchlist->items as $item)
                        <tr class="watchlist-row {{ $item->symbol === $symbol ? 'dark:bg-dark-50 bg-light-100' : '' }}" data-symbol="{{ $item->symbol }}">
                            <td class="px-4 py-2 whitespace-nowrap">
                                <a href="{{ route('trading.crypto', ['symbol' => $item->symbol]) }}" class="block">
                                    <div class="text-sm font-medium dark:text-white text-dark">{{ $item->formatted_symbol }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->base_currency }}/{{ $item->quote_currency }}</div>
                                </a>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <div class="text-sm dark:text-gray-300 text-gray-700 crypto-price">-</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <div class="text-sm crypto-change">-</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                @if($item->symbol !== $symbol)
                                <a href="{{ route('trading.crypto', ['symbol' => $item->symbol]) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-white bg-primary hover:bg-primary-600">
                                    Trade
                                </a>
                                @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-gray-500 bg-gray-100">
                                    Current
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Market Overview -->
        <div class="lg:col-span-2 dark:bg-dark-100 bg-light-200 rounded-xl p-4 border dark:border-dark-200 border-light-300">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold dark:text-white text-dark">Popular Markets</h2>
                <div class="text-sm">
                    <span class="text-gray-500">Last updated: </span>
                    <span class="dark:text-white text-dark" id="marketLastUpdated">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full divide-y dark:divide-dark-200 divide-light-300">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">24h Change</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-dark-200 divide-light-300" id="popularMarketsBody">
                        <!-- Market data will be loaded dynamically -->
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center dark:text-gray-400 text-gray-600">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Loading market data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://unpkg.com/lightweight-charts@3.8.0/dist/lightweight-charts.standalone.production.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart container
        const chartContainer = document.getElementById('tradingChart');
        
        // Global variables
        const symbol = '{{ $symbol }}';
        let watchlistItemId = null;
        
        // Set initial interval 
        let currentInterval = '1h'; // Default interval
        
        // User account balance
        const userAccountBalance = parseFloat('{{ Auth::user()->account_bal }}');
        let currentPrice = parseFloat('{{ $ticker ? $ticker["lastPrice"] : "0.00" }}');
        
        // Helper function to calculate crypto amount based on percentage of balance
        function calculateCryptoAmount(percentageOfBalance, price) {
            if (!price || price <= 0) return 0;
            const dollarAmount = userAccountBalance * (percentageOfBalance / 100);
            return dollarAmount / price;
        }
        
        // Create chart with transparent background
        const chart = LightweightCharts.createChart(chartContainer, {
            width: chartContainer.clientWidth,
            height: 400,
            layout: {
                background: { type: 'solid', color: 'transparent' },
                textColor: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#333',
            },
            grid: {
                vertLines: { color: document.documentElement.classList.contains('dark') ? 'rgba(42, 46, 57, 0.2)' : 'rgba(42, 46, 57, 0.1)' },
                horzLines: { color: document.documentElement.classList.contains('dark') ? 'rgba(42, 46, 57, 0.2)' : 'rgba(42, 46, 57, 0.1)' },
            },
            crosshair: {
                mode: LightweightCharts.CrosshairMode.Normal,
            },
            rightPriceScale: {
                borderColor: 'rgba(42, 46, 57, 0.2)',
            },
            timeScale: {
                borderColor: 'rgba(42, 46, 57, 0.2)',
                timeVisible: true,
            },
        });

        // Handle theme changes
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const isDark = document.documentElement.classList.contains('dark');
                    chart.applyOptions({
                        layout: {
                            textColor: isDark ? '#d1d5db' : '#333',
                        },
                        grid: {
                            vertLines: { color: isDark ? 'rgba(42, 46, 57, 0.2)' : 'rgba(42, 46, 57, 0.1)' },
                            horzLines: { color: isDark ? 'rgba(42, 46, 57, 0.2)' : 'rgba(42, 46, 57, 0.1)' },
                        }
                    });
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
        
        // Add series to chart
        const candleSeries = chart.addCandlestickSeries({
            upColor: '#4A9D7F',
            downColor: '#FF6B6B',
            borderUpColor: '#4A9D7F',
            borderDownColor: '#FF6B6B',
            wickUpColor: '#4A9D7F',
            wickDownColor: '#FF6B6B',
        });
        
        const volumeSeries = chart.addHistogramSeries({
            color: '#26a69a',
            priceFormat: {
                type: 'volume',
            },
            priceScaleId: '',
            scaleMargins: {
                top: 0.8,
                bottom: 0,
            },
        });
        
        // Function to load candle data
        function loadCandleData() {
            fetch(`/dashboard/api/trading/crypto/candles?symbol=${symbol}&interval=${currentInterval}`)
                .then(response => response.json())
                .then(responseData => {
                    if (responseData.success && responseData.data && responseData.data.length > 0) {
                        // Process data for chart
                        const candleData = responseData.data;
                        
                        // Set candle data
                        candleSeries.setData(candleData);
                        
                        // Set volume data
                        const volumeData = candleData.map(d => ({
                            time: d.time,
                            value: d.volume,
                            color: d.close >= d.open ? 'rgba(38, 166, 154, 0.5)' : 'rgba(239, 83, 80, 0.5)'
                        }));
                        volumeSeries.setData(volumeData);
                        
                        // Fit content
                        chart.timeScale().fitContent();
                    } else {
                        console.error('No candle data available or invalid format');
                    }
                })
                .catch(error => {
                    console.error('Error loading candle data:', error);
                });
        }
        
        // Initial load
        loadCandleData();
        
        // Handle window resize
        window.addEventListener('resize', function() {
            chart.resize(chartContainer.clientWidth, 400);
        });
        
        // Handle timeframe buttons
        const timeframeButtons = document.querySelectorAll('.timeframe-btn');
        timeframeButtons.forEach(button => {
            button.addEventListener('click', function() {
                timeframeButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-primary', 'text-white');
                    btn.classList.add('dark:bg-dark-50', 'bg-light-100', 'dark:text-gray-300', 'text-gray-700');
                });
                
                this.classList.remove('dark:bg-dark-50', 'bg-light-100', 'dark:text-gray-300', 'text-gray-700');
                this.classList.add('active', 'bg-primary', 'text-white');
                
                currentInterval = this.dataset.timeframe;
                savePreference('crypto_chart_timeframe', currentInterval);
                loadCandleData();
            });
        });
        
        // Handle trading tab buttons
        const tabButtons = document.querySelectorAll('.trade-tab-btn');
        const tabContents = document.querySelectorAll('.trade-tab');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const target = this.dataset.tab;
                
                // Hide all tabs
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show target tab
                document.getElementById(`${target}-tab`).classList.remove('hidden');
                
                // Update button styles
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-primary', 'dark:text-white', 'text-dark');
                    btn.classList.add('border-transparent', 'dark:text-gray-400', 'text-gray-600');
                });
                
                this.classList.remove('border-transparent', 'dark:text-gray-400', 'text-gray-600');
                this.classList.add('border-primary', 'dark:text-white', 'text-dark');
            });
        });
        
        // Order form handling
        const orderForm = document.getElementById('orderForm');
        const orderTypeButtons = document.querySelectorAll('.order-type-btn');
        const priceInputContainer = document.getElementById('priceInputContainer');
        const stopPriceInputContainer = document.getElementById('stopPriceInputContainer');
        const priceInput = document.getElementById('priceInput');
        const amountInput = document.getElementById('amountInput');
        const orderTotal = document.getElementById('orderTotal');
        
        // Order type change handler
        orderTypeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderType = this.dataset.type;
                
                // Update UI
                orderTypeButtons.forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Update hidden input
                document.getElementById('orderType').value = orderType;
                
                // Show/hide price inputs based on order type
                if (orderType === 'market') {
                    priceInputContainer.classList.add('hidden');
                    stopPriceInputContainer.classList.add('hidden');
                } else if (orderType === 'limit') {
                    priceInputContainer.classList.remove('hidden');
                    stopPriceInputContainer.classList.add('hidden');
                } else if (orderType === 'stop_limit') {
                    priceInputContainer.classList.remove('hidden');
                    stopPriceInputContainer.classList.remove('hidden');
                }
                
                updateOrderTotal();
            });
        });
        
        // Order side tabs
        const orderTabs = document.querySelectorAll('.order-tab');
        const submitOrderBtn = document.getElementById('submitOrderBtn');
        const balanceSection = document.querySelector('.balance-section');
        
        orderTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const side = this.dataset.side;
                
                // Update UI
                orderTabs.forEach(t => {
                    t.classList.remove('active', 'border-primary', 'text-primary');
                    t.classList.add('border-transparent', 'dark:text-gray-400', 'text-gray-600');
                });
                
                this.classList.add('active', 'border-primary', 'text-primary');
                this.classList.remove('border-transparent', 'dark:text-gray-400', 'text-gray-600');
                
                // Update hidden input
                document.getElementById('orderSide').value = side;
                
                // Update submit button
                submitOrderBtn.textContent = side === 'buy' ? 'Buy BTC' : 'Sell BTC';
                submitOrderBtn.classList.remove('buy-btn', 'sell-btn');
                submitOrderBtn.classList.add(side === 'buy' ? 'buy-btn' : 'sell-btn');
                
                // Toggle balance display for buy/sell
                if (balanceSection) {
                    if (side === 'buy') {
                        balanceSection.classList.remove('hidden');
                        // Reset balance display when switching to buy
                        updateOrderTotal();
                    } else {
                        balanceSection.classList.add('hidden');
                    }
                }
            });
        });
        
        // Percentage buttons
        const percentageButtons = document.querySelectorAll('.percentage-btn');
        
        percentageButtons.forEach(button => {
            button.addEventListener('click', function() {
                const percentage = parseInt(this.dataset.percentage);
                const orderSide = document.getElementById('orderSide').value;
                
                if (orderSide === 'buy') {
                    // For buy orders, calculate based on available USD balance
                    const orderType = document.getElementById('orderType').value;
                    const price = orderType === 'market' ? currentPrice : parseFloat(priceInput?.value || 0);
                    
                    // Use the helper function to calculate the amount
                    const cryptoAmount = calculateCryptoAmount(percentage, price);
                    amountInput.value = cryptoAmount.toFixed(8);
                    updateOrderTotal();
                } else {
                    // For sell orders, would calculate based on available crypto
                    // For now, use a dummy value (to be replaced with actual crypto balance)
                    const maxAmount = 1; // 1 BTC (placeholder)
                    amountInput.value = (maxAmount * percentage / 100).toFixed(8);
                    updateOrderTotal();
                }
            });
        });
        
        // Update total when inputs change
        priceInput?.addEventListener('input', updateOrderTotal);
        amountInput?.addEventListener('input', updateOrderTotal);
        
        function updateOrderTotal() {
            const orderType = document.getElementById('orderType').value;
            const price = orderType === 'market' ? currentPrice : parseFloat(priceInput?.value || 0);
            const amount = parseFloat(amountInput?.value || 0);
            const orderSide = document.getElementById('orderSide').value;
            
            const total = price * amount;
            if (orderTotal) {
                orderTotal.textContent = total.toFixed(2);
                // Add tooltip with more information
                orderTotal.title = `Total: ${total.toFixed(2)} USDT (${amount} × ${price})`;
            }
            
            // Update balance after order
            const balanceAfterOrder = document.getElementById('balanceAfterOrder');
            if (balanceAfterOrder) {
                // Use the global userAccountBalance variable
                
                if (orderSide === 'buy') {
                    // For buy orders, subtract from balance
                    const remainingBalance = userAccountBalance - total;
                    balanceAfterOrder.textContent = '{{ $settings->currency }}' + remainingBalance.toFixed(2);
                    balanceAfterOrder.title = "Estimated balance after order execution";
                    
                    // Highlight in red if insufficient balance
                    if (remainingBalance < 0) {
                        balanceAfterOrder.classList.add('text-red-500');
                        balanceAfterOrder.classList.remove('text-gray-500');
                        // Disable submit button if not enough funds
                        if (submitOrderBtn) {
                            submitOrderBtn.disabled = true;
                            submitOrderBtn.title = "Insufficient funds for this order";
                        }
                    } else {
                        balanceAfterOrder.classList.add('text-gray-500');
                        balanceAfterOrder.classList.remove('text-red-500');
                        // Enable submit button
                        if (submitOrderBtn) {
                            submitOrderBtn.disabled = false;
                            submitOrderBtn.title = "";
                        }
                    }
                } else {
                    // For sell orders, add to balance (for market orders)
                    if (orderType === 'market') {
                        const newBalance = userAccountBalance + total;
                        balanceAfterOrder.textContent = '{{ $settings->currency }}' + newBalance.toFixed(2);
                        balanceAfterOrder.title = "Estimated balance after order execution";
                        balanceAfterOrder.classList.add('text-gray-500');
                        balanceAfterOrder.classList.remove('text-red-500');
                    } else {
                        // For limit/stop orders, show current balance
                        balanceAfterOrder.textContent = '{{ $settings->currency }}' + currentBalance.toFixed(2);
                        balanceAfterOrder.title = "Current balance (will change when order executes)";
                    }
                }
            }
        }
        
        // Form submission
        orderForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const orderSide = document.getElementById('orderSide').value;
            const orderType = document.getElementById('orderType').value;
            const price = orderType === 'market' ? currentPrice : parseFloat(priceInput?.value || 0);
            const amount = parseFloat(amountInput?.value || 0);
            const total = price * amount;
            
            // Check if user has sufficient balance for buy orders
            if (orderSide === 'buy') {
                if (total > userAccountBalance) {
                    alert('Insufficient balance to place this order.');
                    return;
                }
            }
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Disable button during submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Processing...';
            
            fetch('/dashboard/api/trading/order/create', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Order placed successfully!');
                    
                    // Reset form
                    orderForm.reset();
                    
                    // Reload the page to show the updated orders
                    window.location.reload();
                } else {
                    // Show error message
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred while placing the order. Please try again.');
                console.error('Error:', error);
            })
            .finally(() => {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Place Order';
            });
        });
        
        // Function to save user preferences
        function savePreference(key, value) {
            fetch('/dashboard/api/trading/preferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ key, value })
            })
            .catch(error => console.error('Error saving preference:', error));
        }
        
        // Auto-refresh data
        function refreshData() {
            fetch(`/dashboard/api/trading/crypto/symbol?symbol=${symbol}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        console.log('Received data:', data); // Debug log
                        
                        // Update global price variable
                        currentPrice = parseFloat(data.lastPrice);
                        
                        // Update price info in the header section
                        const lastPriceElement = document.querySelector('.flex.items-center.space-x-3 .text-sm.font-medium.dark\\:text-white');
                        const priceChangeElement = document.querySelector('.flex.items-center.space-x-3 .text-sm.font-medium[class*="market-"]');

                        
                        // Update order total if market order is selected
                        const orderTypeElement = document.getElementById("orderType");
                        if (orderTypeElement && orderTypeElement.value === "market") {
                            updateOrderTotal();
                        }
                        
                        if (lastPriceElement) {
                            lastPriceElement.textContent = `${number_format(data.lastPrice, 2)} USDT`;
                        }
                        
                        if (priceChangeElement) {
                            const changePercent = parseFloat(data.priceChangePercent);
                            priceChangeElement.textContent = `${changePercent >= 0 ? '+' : ''}${number_format(changePercent, 2)}%`;
                            priceChangeElement.className = `text-sm font-medium ${changePercent >= 0 ? 'market-positive' : 'market-negative'}`;
                        }
                        
                        // Update chart information values using IDs
                        const high24h = document.getElementById('high24h');
                        const low24h = document.getElementById('low24h');
                        const volume24h = document.getElementById('volume24h');
                        const change24h = document.getElementById('change24h');

                        if (high24h && data.highPrice) {
                            high24h.textContent = number_format(data.highPrice, 2);
                        }
                        
                        if (low24h && data.lowPrice) {
                            low24h.textContent = number_format(data.lowPrice, 2);
                        }
                        
                        if (volume24h && data.volume) {
                            volume24h.textContent = number_format(data.volume, 2);
                        }
                        
                        if (change24h && data.priceChangePercent) {
                            const changePercent = parseFloat(data.priceChangePercent);
                            change24h.textContent = `${changePercent >= 0 ? '+' : ''}${number_format(changePercent, 2)}%`;
                            change24h.className = `text-sm font-medium ${changePercent >= 0 ? 'market-positive' : 'market-negative'}`;
                        }

                        // Update order book
                        updateOrderBook(data);

                        // Update recent trades
                        updateRecentTrades(data);

                        // Update popular markets
                        updatePopularMarkets(data);

                        // Show fallback alert if using fallback data
                        const fallbackAlert = document.querySelector('.alert.alert-warning');
                        if (data.isFallback && fallbackAlert) {
                            fallbackAlert.classList.remove('hidden');
                        } else if (fallbackAlert) {
                            fallbackAlert.classList.add('hidden');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error refreshing data:', error);
                });
        }
        
        // Function to update order book
        function updateOrderBook(data) {
            const asks = data.orderBook?.asks || generateDummyOrderBook(data.lastPrice || data.price, true);
            const bids = data.orderBook?.bids || generateDummyOrderBook(data.lastPrice || data.price, false);
            
            // Update asks
            const askRows = document.querySelectorAll('#orderbook-tab .space-y-1.mb-2 .orderbook-row');
            askRows.forEach((row, index) => {
                if (asks[index]) {
                    const [price, amount] = asks[index];
                    const priceElement = row.querySelector('.orderbook-content .text-xs');
                    if (priceElement) {
                        priceElement.textContent = parseFloat(price).toFixed(2);
                    }
                }
            });

            // Update bids
            const bidRows = document.querySelectorAll('#orderbook-tab .space-y-1.mt-2 .orderbook-row');
            bidRows.forEach((row, index) => {
                if (bids[index]) {
                    const [price, amount] = bids[index];
                    const priceElement = row.querySelector('.orderbook-content .text-xs');
                    if (priceElement) {
                        priceElement.textContent = parseFloat(price).toFixed(2);
                    }
                }
            });
        }

        // Function to generate dummy order book data
        function generateDummyOrderBook(basePrice, isAsks) {
            const price = parseFloat(basePrice);
            return Array.from({ length: 5 }, (_, i) => {
                const offset = isAsks ? (i + 1) * 0.001 : -(i + 1) * 0.001;
                return [price * (1 + offset), Math.random() * 0.5 + 0.1];
            });
        }

        // Function to update recent trades
        function updateRecentTrades(data) {
            const trades = data.recentTrades || generateDummyTrades(data.lastPrice || data.price);
            const tbody = document.querySelector('#recentTradesBody');
            
            if (tbody) {
                trades.forEach((trade, index) => {
                    const row = tbody.children[index];
                    if (row) {
                        const priceCell = row.querySelector('td:first-child .text-sm');
                        const amountCell = row.querySelector('td:nth-child(2) .text-sm');
                        const timeCell = row.querySelector('td:last-child .text-sm');

                        if (priceCell) {
                            priceCell.textContent = parseFloat(trade.price).toFixed(2);
                            priceCell.className = `text-sm font-medium ${trade.isBuyerMaker ? 'market-positive' : 'market-negative'}`;
                        }
                        if (amountCell) {
                            amountCell.textContent = parseFloat(trade.amount).toFixed(4);
                        }
                        if (timeCell) {
                            timeCell.textContent = formatTime(trade.time);
                        }
                    }
                });
            }
        }

        // Function to generate dummy trades
        function generateDummyTrades(basePrice) {
            const price = parseFloat(basePrice);
            return Array.from({ length: 10 }, () => ({
                price: price * (1 + (Math.random() * 0.002 - 0.001)),
                amount: Math.random() * 0.5 + 0.1,
                time: new Date().getTime() - Math.floor(Math.random() * 1800000),
                isBuyerMaker: Math.random() > 0.5
            }));
        }

        // Function to update popular markets
        function updatePopularMarkets(data) {
            fetch('/dashboard/api/trading/crypto/top')
                .then(response => response.json())
                .then(data => {
                    const marketBody = document.getElementById('popularMarketsBody');
                    if (!marketBody) return;
                    
                    // Clear existing content
                    marketBody.innerHTML = '';
                    
                    // Use data.data if available (API response format), otherwise use data directly
                    const markets = (data && data.success && Array.isArray(data.data)) ? data.data : data;
                    
                    // Get top 5 markets to display
                    const topMarkets = markets.slice(0, 5);
                    
                    topMarkets.forEach(market => {
                        // Make sure to extract the base symbol (without USDT) for the link
                        const baseSymbol = market.symbol.replace('USDT', '');
                        
                        // Ensure values are numbers, not strings
                        const price = parseFloat(market.lastPrice || market.price || 0);
                        const changePercent = parseFloat(market.priceChangePercent || market.change24h || 0);
                        const isPositive = changePercent >= 0;
                        
                        const row = document.createElement('tr');
                        row.className = 'popular-market-row';
                        row.dataset.symbol = market.symbol;
                        
                        row.innerHTML = `
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium dark:text-white text-dark">${market.symbol}USDT</div>
                                        <div class="text-xs text-gray-500">${market.name || (market.baseAsset ? market.baseAsset + '/' + market.quoteAsset : baseSymbol + '/USDT')}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <div class="text-sm font-medium dark:text-white text-dark market-price">$${isNaN(price) ? '0.00' : price.toFixed(2)}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <div class="text-sm font-medium ${isPositive ? 'market-positive' : 'market-negative'} market-change">
                                    ${isPositive ? '+' : ''}${isNaN(changePercent) ? '0.00' : changePercent.toFixed(2)}%
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <a href="/dashboard/trading/crypto/${market.symbol}USDT" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-primary hover:bg-primary-600 focus:outline-none">
                                    ${market.symbol === symbol ? 'Current' : 'Trade'}
                                </a>
                            </td>
                        `;
                        
                        marketBody.appendChild(row);
                    });
                    
                    // Update last updated time
                    const lastUpdatedElement = document.getElementById('marketLastUpdated');
                    if (lastUpdatedElement) {
                        lastUpdatedElement.textContent = new Date().toLocaleTimeString();
                    }
                })
                .catch(error => {
                    console.error('Error loading market data:', error);
                    
                    // Show error message
                    const marketBody = document.getElementById('popularMarketsBody');
                    if (marketBody) {
                        marketBody.innerHTML = `
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-red-500">
                                    <i class="fas fa-exclamation-circle mr-2"></i> Failed to load market data. Please try again later.
                                </td>
                            </tr>
                        `;
                    }
                });
        }

        // Helper function for time formatting
        function formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString('en-US', { hour12: false });
        }

        // Helper function for number formatting
        function number_format(number, decimals = 2) {
            return parseFloat(number).toFixed(decimals);
        }
        
        // Initial data refresh
        refreshData();
        
        // Set up auto-refresh every 10 seconds
        setInterval(refreshData, 10000);

        // Clean up when leaving page
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    });

    // Load crypto symbols for dropdown
    function loadCryptoSymbols() {
        const loader = document.getElementById('symbolDropdownLoader');
        if (loader) loader.style.display = 'flex';
        
        // Define popular cryptocurrency pairs as fallback
        const popularCryptoPairs = [
            { symbol: 'BTCUSDT', name: 'Bitcoin' },
            { symbol: 'ETHUSDT', name: 'Ethereum' },
            { symbol: 'SOLUSDT', name: 'Solana' },
            { symbol: 'BNBUSDT', name: 'Binance Coin' },
            { symbol: 'ADAUSDT', name: 'Cardano' },
            { symbol: 'XRPUSDT', name: 'Ripple' },
            { symbol: 'DOGEUSDT', name: 'Dogecoin' },
            { symbol: 'DOTUSDT', name: 'Polkadot' },
            { symbol: 'AVAXUSDT', name: 'Avalanche' },
            { symbol: 'MATICUSDT', name: 'Polygon' },
            { symbol: 'LINKUSDT', name: 'Chainlink' },
            { symbol: 'UNIUSDT', name: 'Uniswap' },
            { symbol: 'LTCUSDT', name: 'Litecoin' },
            { symbol: 'ATOMUSDT', name: 'Cosmos' },
            { symbol: 'ALGOUSDT', name: 'Algorand' },
            { symbol: 'XMRUSDT', name: 'Monero' },
            { symbol: 'ICPUSDT', name: 'Internet Computer' },
            { symbol: 'FILUSDT', name: 'Filecoin' },
            { symbol: 'VETUSDT', name: 'VeChain' },
            { symbol: 'THETAUSDT', name: 'Theta Network' },
            { symbol: 'EOSUSDT', name: 'EOS' },
            { symbol: 'AAVEUSDT', name: 'Aave' },
            { symbol: 'AXSUSDT', name: 'Axie Infinity' },
            { symbol: 'NEARUSDT', name: 'NEAR Protocol' },
            { symbol: 'XLMUSDT', name: 'Stellar' },
            { symbol: 'FTMUSDT', name: 'Fantom' },
            { symbol: 'HBARUSDT', name: 'Hedera' },
            { symbol: 'MKRUSDT', name: 'Maker' },
            { symbol: 'SANDUSDT', name: 'The Sandbox' },
            { symbol: 'MANAUSDT', name: 'Decentraland' },
            { symbol: 'SHIBUSDT', name: 'Shiba Inu' },
            { symbol: 'GRTUSDT', name: 'The Graph' },
            { symbol: 'CELOUSDT', name: 'Celo' },
            { symbol: 'ENJUSDT', name: 'Enjin Coin' },
            { symbol: '1INCHUSDT', name: '1inch' },
            { symbol: 'COMPUSDT', name: 'Compound' },
            { symbol: 'SUSHIUSDT', name: 'SushiSwap' },
            { symbol: 'CRVUSDT', name: 'Curve DAO' },
            { symbol: 'LRCUSDT', name: 'Loopring' },
            { symbol: 'BATUSDT', name: 'Basic Attention Token' }
        ];
        
        fetch('/dashboard/api/trading/crypto/top?limit=50')
            .then(response => response.json())
            .then(data => {
                // Process API data or fallback to predefined list
                let cryptosData = [];
                
                if (data && (data.data || Array.isArray(data))) {
                    cryptosData = data.data || data;
                    
                    // If API returned less than 20 results, supplement with our predefined list
                    if (cryptosData.length < 20) {
                        console.log('API returned limited results, supplementing with predefined pairs');
                        
                        // Get existing symbols to avoid duplicates
                        const existingSymbols = cryptosData.map(c => c.symbol);
                        
                        // Add predefined pairs that aren't already in the API response
                        popularCryptoPairs.forEach(pair => {
                            const symbolWithoutSuffix = pair.symbol.replace('USDT', '');
                            if (!existingSymbols.includes(symbolWithoutSuffix) && !existingSymbols.includes(pair.symbol)) {
                                cryptosData.push(pair);
                            }
                        });
                    }
                } else {
                    // If no valid data from API, use our predefined list
                    console.log('Using predefined cryptocurrency pairs');
                    cryptosData = popularCryptoPairs;
                }
                
                const select = document.getElementById('cryptoSymbolSelect');
                const currentSymbol = '{{ $symbol }}';
                
                // Clear existing options except the loading option
                while (select.options.length > 0) {
                    if (select.options[0].disabled && select.options[0].textContent === 'Loading more symbols...') {
                        // Keep the loading option
                        break;
                    }
                    select.remove(0);
                }
                
                // Create an option group for the current selection
                const currentGroup = document.createElement('optgroup');
                currentGroup.label = 'Current Selection';
                
                // Add the current symbol as the first option in its own group
                const currentOption = document.createElement('option');
                currentOption.value = currentSymbol;
                currentOption.textContent = `${currentSymbol}`;
                currentOption.selected = true;
                currentGroup.appendChild(currentOption);
                select.appendChild(currentGroup);
                
                // Create an option group for the top cryptos
                const popularGroup = document.createElement('optgroup');
                popularGroup.label = 'Popular Cryptocurrencies';
                
                // Add all available cryptos in alphabetical order by symbol
                const sortedCryptos = [...cryptosData];
                sortedCryptos.sort((a, b) => a.symbol.localeCompare(b.symbol));
                
                sortedCryptos.forEach(crypto => {
                    if (crypto.symbol !== currentSymbol) {
                        const option = document.createElement('option');
                        // Ensure the symbol ends with USDT
                        const symbolWithSuffix = crypto.symbol.endsWith('USDT') ? crypto.symbol : `${crypto.symbol}USDT`;
                        option.value = symbolWithSuffix;
                        option.textContent = `${symbolWithSuffix} - ${crypto.name || ''}`;
                        popularGroup.appendChild(option);
                    }
                });
                
                select.appendChild(popularGroup);
                
                // Remove any loading options
                Array.from(select.options).forEach(option => {
                    if (option.disabled && option.textContent === 'Loading more symbols...') {
                        select.removeChild(option);
                    }
                });
                
                // Hide loader
                if (loader) loader.style.display = 'none';
                
                // Add change event
                select.addEventListener('change', function() {
                    // Show a loading message
                    const selectedSymbol = this.value;
                    if (selectedSymbol !== '{{ $symbol }}') {
                        // Check if dark mode is enabled
                        const isDarkMode = document.documentElement.classList.contains('dark');
                        
                        const loadingDiv = document.createElement('div');
                        loadingDiv.id = 'symbol-loading';
                        loadingDiv.className = 'fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black bg-opacity-50 z-50';
                        loadingDiv.innerHTML = `<div class="${isDarkMode ? 'bg-dark-800 text-white' : 'bg-white text-gray-800'} p-4 rounded-lg shadow-lg flex flex-col items-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-3"></div>
                            <p class="${isDarkMode ? 'text-gray-300' : 'text-gray-700'}">Loading ${selectedSymbol}...</p>
                        </div>`;
                        document.body.appendChild(loadingDiv);
                        
                        // Navigate to the selected crypto
                        window.location.href = `/dashboard/trading/crypto?symbol=${selectedSymbol}`;
                    }
                });
            })
            .catch(error => {
                console.error('Error loading crypto symbols:', error);
                
                // Fallback to predefined list on error
                const select = document.getElementById('cryptoSymbolSelect');
                const currentSymbol = '{{ $symbol }}';
                
                // Clear existing options
                while (select.options.length > 0) {
                    if (select.options[0].disabled && select.options[0].textContent === 'Loading more symbols...') {
                        break;
                    }
                    select.remove(0);
                }
                
                // Add current selection
                const currentGroup = document.createElement('optgroup');
                currentGroup.label = 'Current Selection';
                
                const currentOption = document.createElement('option');
                currentOption.value = currentSymbol;
                currentOption.textContent = `${currentSymbol}`;
                currentOption.selected = true;
                currentGroup.appendChild(currentOption);
                select.appendChild(currentGroup);
                
                // Add fallback options from predefined list
                const popularGroup = document.createElement('optgroup');
                popularGroup.label = 'Popular Cryptocurrencies';
                
                popularCryptoPairs.forEach(pair => {
                    if (pair.symbol !== currentSymbol) {
                        const option = document.createElement('option');
                        option.value = pair.symbol;
                        option.textContent = `${pair.symbol} - ${pair.name}`;
                        popularGroup.appendChild(option);
                    }
                });
                
                select.appendChild(popularGroup);
                
                // Add change event (same as above)
                select.addEventListener('change', function() {
                    const selectedSymbol = this.value;
                    if (selectedSymbol !== '{{ $symbol }}') {
                        const isDarkMode = document.documentElement.classList.contains('dark');
                        
                        const loadingDiv = document.createElement('div');
                        loadingDiv.id = 'symbol-loading';
                        loadingDiv.className = 'fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black bg-opacity-50 z-50';
                        loadingDiv.innerHTML = `<div class="${isDarkMode ? 'bg-dark-800 text-white' : 'bg-white text-gray-800'} p-4 rounded-lg shadow-lg flex flex-col items-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-3"></div>
                            <p class="${isDarkMode ? 'text-gray-300' : 'text-gray-700'}">Loading ${selectedSymbol}...</p>
                        </div>`;
                        document.body.appendChild(loadingDiv);
                        
                        window.location.href = `/dashboard/trading/crypto?symbol=${selectedSymbol}`;
                    }
                });
                
                // Hide loader
                const loader = document.getElementById('symbolDropdownLoader');
                if (loader) loader.style.display = 'none';
            });
    }
    
    // Function to update all watchlist item prices
    function updateAllWatchlistPrices() {
        const watchlistRows = document.querySelectorAll('.watchlist-row');
        if (watchlistRows.length === 0) return;
        
        console.log(`Updating prices for ${watchlistRows.length} watchlist items`);
        
        watchlistRows.forEach(row => {
            const symbol = row.dataset.symbol;
            if (!symbol) return;
            
            fetch(`/dashboard/api/trading/crypto/symbol?symbol=${symbol}`)
                .then(response => response.json())
                .then(data => {
                    if (!data) return;
                    
                    const priceElement = row.querySelector('.crypto-price');
                    const changeElement = row.querySelector('.crypto-change');
                    
                    if (priceElement) {
                        const price = parseFloat(data.lastPrice);
                        priceElement.textContent = isNaN(price) ? '-' : '$' + price.toFixed(2);
                    }
                    
                    if (changeElement) {
                        const changePercent = parseFloat(data.priceChangePercent);
                        if (!isNaN(changePercent)) {
                            changeElement.textContent = `${changePercent >= 0 ? '+' : ''}${changePercent.toFixed(2)}%`;
                            changeElement.className = `text-sm ${changePercent >= 0 ? 'text-green-500' : 'text-red-500'}`;
                        }
                    }
                })
                .catch(error => {
                    console.error(`Error fetching price for ${symbol}:`, error);
                });
        });
    }

    // Update prices on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Load crypto symbols dropdown
        loadCryptoSymbols();
        
        // Initial update of watchlist prices
        setTimeout(updateAllWatchlistPrices, 500);
        
        // Set interval to update watchlist prices every 30 seconds
        const priceUpdateInterval = setInterval(updateAllWatchlistPrices, 30000);
        
        // Set up refresh button click handlers
        const mobileRefreshBtn = document.getElementById('refreshBtn');
        const desktopRefreshBtn = document.getElementById('desktopRefreshBtn');
        
        const refreshClickHandler = function() {
            // Refresh data
            refreshData();
            updateAllWatchlistPrices();
            
            // Show visual feedback
            this.querySelector('i').classList.add('animate-spin');
            setTimeout(() => {
                this.querySelector('i').classList.remove('animate-spin');
            }, 1000);
        };
        
        if (mobileRefreshBtn) {
            mobileRefreshBtn.addEventListener('click', refreshClickHandler);
        }
        
        if (desktopRefreshBtn) {
            desktopRefreshBtn.addEventListener('click', refreshClickHandler);
        }
        
        // Clear interval when leaving page
        window.addEventListener('beforeunload', function() {
            clearInterval(priceUpdateInterval);
        });
    });

    // Auto-update watchlist prices
    function updateWatchlistPrices() {
        const watchlistRows = document.querySelectorAll('.watchlist-row');
        watchlistRows.forEach(row => {
            const symbol = row.dataset.symbol;
            if (!symbol) return;
            
            fetch(`/dashboard/api/trading/crypto/symbol?symbol=${symbol}`)
                .then(response => response.json())
                .then(data => {
                    if (!data) return;
                    
                    const priceElement = row.querySelector('.crypto-price');
                    const changeElement = row.querySelector('.crypto-change');
                    
                    if (priceElement) {
                        const price = parseFloat(data.lastPrice);
                        priceElement.textContent = isNaN(price) ? '-' : '$' + price.toFixed(2);
                    }
                    
                    if (changeElement) {
                        const changePercent = parseFloat(data.priceChangePercent);
                        if (!isNaN(changePercent)) {
                            changeElement.textContent = `${changePercent >= 0 ? '+' : ''}${changePercent.toFixed(2)}%`;
                            changeElement.className = `text-sm ${changePercent >= 0 ? 'text-green-500' : 'text-red-500'}`;
                        }
                    }
                })
                .catch(error => {
                    console.error(`Error fetching price for ${symbol}:`, error);
                });
        });
    }
    
    // Set up auto-refresh every 30 seconds
    setInterval(updateWatchlistPrices, 30000);
    
    // Initialize watchlist prices on page load
    updateWatchlistPrices();
    
    // Set up cancel order buttons
    const cancelOrderButtons = document.querySelectorAll('.cancel-order-btn');
    cancelOrderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            if (!orderId) return;
            
            if (confirm('Are you sure you want to cancel this order?')) {
                // Show loading state
                this.textContent = 'Cancelling...';
                this.disabled = true;
                
                fetch(`/dashboard/api/trading/order/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order cancelled successfully');
                        // Remove the row from the table or reload the page
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                        // Reset button
                        this.textContent = 'Cancel';
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while cancelling the order');
                    // Reset button
                    this.textContent = 'Cancel';
                    this.disabled = false;
                });
            }
        });
    });
</script>
@endsection 