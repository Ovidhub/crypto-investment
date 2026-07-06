@extends('layouts.dash')

@section('title', 'Stock Trading')

@section('styles')
<style>
    /* Chart styles */
    #tradingChart {
        height: 400px;
        width: 100%;
        background-color: var(--bg-card);
        border-radius: 0.5rem;
    }
    
    /* Button styles */
    .timeframe-btn.active {
        background-color: var(--primary);
        color: white;
    }
    
    /* Market status indicators */
    .market-open {
        color: #4A9D7F;
    }
    .market-closed {
        color: #FF6B6B;
    }
    
    /* Order book styles */
    .order-book-row:hover {
        background-color: rgba(var(--primary-rgb), 0.1);
    }
    .ask-price {
        color: #FF6B6B;
    }
    .bid-price {
        color: #4A9D7F;
    }
    
    /* Buy/Sell buttons */
    .buy-btn {
        background-color: #4A9D7F;
        color: white;
    }
    .buy-btn:hover {
        background-color: #3a8d6f;
    }
    .sell-btn {
        background-color: #FF6B6B;
        color: white;
    }
    .sell-btn:hover {
        background-color: #ff5b5b;
    }

    /* Symbol dropdown styling */
    #stockSymbolSelect {
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
    
    #stockSymbolSelect optgroup {
        font-weight: 600;
        color: var(--text-primary);
    }
    
    #stockSymbolSelect option {
        padding: 8px;
    }
    
    /* Improve responsive layout */
    @media (max-width: 640px) {
        #stockSymbolSelect {
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
    <div class="mb-4 px-4 py-3 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded relative" role="alert">
        <strong class="font-bold">Notice:</strong>
        <span class="block sm:inline"> 
            @if(isset($apiProvider))
                Using {{ $apiProvider }} data. 
                @if($apiProvider == 'Fallback Data')
                    Fallback due to Finnhub API limitations.
                @elseif($apiProvider == 'Sample Data')
                    Unable to connect to market data providers.
                @endif
            @else
                Using demo data or experiencing API rate limits.
            @endif 
            Some features may be limited.
        </span>
        @if(isset($quote['error']) && $quote['error'] == 'API rate limit reached')
        <span class="block mt-1 text-sm">API rate limit reached. Please try again later.</span>
        @endif
    </div>
    @endif

    <!-- Trading header -->
    <div class="flex flex-col space-y-4 md:space-y-0 md:flex-row md:justify-between mb-6">
        <!-- Left side with back button and symbol information -->
        <div class="flex flex-col space-y-4 w-full md:w-auto">
            <div class="flex items-center justify-between">
                <a href="{{ route('trading') }}" class="text-primary hover:text-primary-dark">
                    <i class="fas fa-arrow-left"></i> <span class="hidden sm:inline">Back to Markets</span>
                </a>
                
                <!-- Refresh button (moved for mobile) -->
                <button id="refreshBtn" class="p-2 rounded-full hover:bg-light-100 dark:hover:bg-dark-50 md:hidden">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            
            <!-- Stock Symbol Dropdown -->
            <div class="relative w-full sm:max-w-xs">
                <select id="stockSymbolSelect" class="block w-full px-4 py-2 text-gray-700 bg-white border border-light-200 dark:border-dark-100 dark:bg-dark-50 dark:text-white rounded shadow-sm focus:outline-none focus:ring-primary focus:border-primary appearance-none">
                    <option value="{{ $symbol }}" selected>{{ $symbol }} - {{ $fullName }}</option>
                    <option value="" disabled>Loading more symbols...</option>
                </select>
                <div id="symbolDropdownLoader" class="absolute inset-y-0 right-8 flex items-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-spinner fa-spin text-xs"></i>
                </div>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
            
            <!-- Symbol and company name -->
            <div class="flex items-center justify-between">
                <h1 class="symbol-display font-bold dark:text-white">
                    {{ $symbol }}
                    <span class="symbol-description font-normal text-gray-500 block sm:inline">{{ $fullName ?? ($companyInfo['name'] ?? $symbol . ' Inc.') }}</span>
                </h1>
                
                <!-- Alpine.js Watchlist Star Button -->
                <div
                    x-data="{
                        inWatchlist: {{ isset($inWatchlist) && $inWatchlist ? 'true' : 'false' }},
                        itemId: {{ isset($watchlistItemId) && $watchlistItemId ? $watchlistItemId : 'null' }},
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
                                    type: 'stock'
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.itemId = data.item_id;
                                    // Update UI
                                    console.log('Added to watchlist successfully', data);
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
                                    console.log('Removed from watchlist successfully');
                                } else {
                                    this.inWatchlist = true;
                                    console.error('Error removing from watchlist:', data.message);
                                }
                            })
                            .catch(err => {
                                this.inWatchlist = true;
                                console.error('Error removing from watchlist:', err);
                            });
                        }
                    }"
                >
                    <button 
                        @click="toggleWatchlist()"
                        class="px-3 py-1.5 rounded border dark:border-dark-100 border-light-200 hover:border-primary focus:outline-none transition duration-150"
                        :class="inWatchlist ? 'bg-gray-100 dark:bg-dark-100' : ''"
                    >
                        <i :class="inWatchlist ? 'fas fa-star text-primary' : 'far fa-star text-gray-500'"></i>
                        <span x-text="inWatchlist ? 'Watchlist' : 'Add'" class="ml-1 text-sm sm:hidden"></span>
                        <span x-text="inWatchlist ? 'In Watchlist' : 'Add to Watchlist'" class="ml-1 text-sm hidden sm:inline"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Right side with price information -->
        <div class="flex flex-col space-y-2 md:items-end">
            <div class="text-xl font-bold dark:text-white">
                <span id="currentPrice">{{ $ticker['lastPrice'] }}</span>
                <span id="priceChange" class="{{ ($ticker['change'] >= 0) ? 'text-green-500' : 'text-red-500' }}">
                    {{ ($ticker['change'] >= 0) ? '+' : '' }}{{ $ticker['change'] }}
                </span>
                <span id="priceChangePercent" class="{{ ($ticker['change'] >= 0) ? 'text-green-500' : 'text-red-500' }}">
                    ({{ $ticker['changePercent'] }})
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="text-sm text-gray-500">
                    <span class="{{ ($ticker['marketStatus'] == 'open') ? 'market-open' : 'market-closed' }}">
                        <i class="fas fa-circle text-xs"></i>
                        {{ ($ticker['marketStatus'] == 'open') ? 'Market Open' : 'Market Closed' }}
                    </span>
                    | <span class="hidden sm:inline">Volume:</span> <span id="volume">{{ $ticker['volume'] }}</span>
                </div>
                
                <button id="refreshBtn" class="p-2 rounded-full hover:bg-light-100 dark:hover:bg-dark-50 hidden md:block">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Trading layout grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main chart and order book (2/3 width on large screens) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Price chart section -->
            <div class="bg-light-50 dark:bg-dark-50 rounded-lg shadow-sm p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-bold dark:text-white">Price Chart</h2>
                    <div class="flex space-x-2">
                        <button data-timeframe="1d" class="timeframe-btn px-3 py-1 text-sm rounded bg-light-100 dark:bg-dark-50 dark:text-gray-300 text-gray-700 hover:bg-gray-200 dark:hover:bg-dark-100">1D</button>
                        <button data-timeframe="1w" class="timeframe-btn px-3 py-1 text-sm rounded bg-light-100 dark:bg-dark-50 dark:text-gray-300 text-gray-700 hover:bg-gray-200 dark:hover:bg-dark-100">1W</button>
                        <button data-timeframe="1m" class="timeframe-btn px-3 py-1 text-sm rounded bg-primary text-white active">1M</button>
                        <button data-timeframe="3m" class="timeframe-btn px-3 py-1 text-sm rounded bg-light-100 dark:bg-dark-50 dark:text-gray-300 text-gray-700 hover:bg-gray-200 dark:hover:bg-dark-100">3M</button>
                        <button data-timeframe="1y" class="timeframe-btn px-3 py-1 text-sm rounded bg-light-100 dark:bg-dark-50 dark:text-gray-300 text-gray-700 hover:bg-gray-200 dark:hover:bg-dark-100">1Y</button>
                    </div>
                </div>
                
                <div id="tradingChart"></div>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 text-center">
                    <div class="bg-light-100 dark:bg-dark-100 p-2 rounded">
                        <div class="text-xs text-gray-500">Open</div>
                        <div id="openPrice" class="font-semibold dark:text-white">${{ $ticker['open'] }}</div>
                    </div>
                    <div class="bg-light-100 dark:bg-dark-100 p-2 rounded">
                        <div class="text-xs text-gray-500">High</div>
                        <div id="highPrice" class="font-semibold dark:text-white">${{ $ticker['high'] }}</div>
                    </div>
                    <div class="bg-light-100 dark:bg-dark-100 p-2 rounded">
                        <div class="text-xs text-gray-500">Low</div>
                        <div id="lowPrice" class="font-semibold dark:text-white">${{ $ticker['low'] }}</div>
                    </div>
                    <div class="bg-light-100 dark:bg-dark-100 p-2 rounded">
                        <div class="text-xs text-gray-500">Prev Close</div>
                        <div id="prevClosePrice" class="font-semibold dark:text-white">${{ $ticker['prevClose'] }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Trading information tabs -->
            <div class="bg-light-50 dark:bg-dark-50 rounded-lg shadow-sm">
                <!-- Tab buttons -->
                <div class="flex border-b border-light-200 dark:border-dark-100">
                    <button data-tab="overview" class="trade-tab-btn px-6 py-3 text-sm border-b-2 border-primary dark:text-white text-dark">Overview</button>
                    <button data-tab="trades" class="trade-tab-btn px-6 py-3 text-sm border-b-2 border-transparent dark:text-gray-400 text-gray-600">Recent Trades</button>
                    <button data-tab="orders" class="trade-tab-btn px-6 py-3 text-sm border-b-2 border-transparent dark:text-gray-400 text-gray-600">Open Orders</button>
                </div>
                
                <!-- Tab content -->
                <div class="p-4">
                    <!-- Overview tab -->
                    <div id="overview-tab" class="trade-tab">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-semibold mb-3 dark:text-white">Company Information</h3>
                                <table class="w-full text-sm">
                                    <tr>
                                        <td class="py-2 text-gray-500">Sector</td>
                                        <td class="py-2 dark:text-white">{{ $companyInfo['sector'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-gray-500">Industry</td>
                                        <td class="py-2 dark:text-white">{{ $companyInfo['industry'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-gray-500">Employees</td>
                                        <td class="py-2 dark:text-white">{{ $companyInfo['employees'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-gray-500">Country</td>
                                        <td class="py-2 dark:text-white">{{ $companyInfo['country'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-gray-500">Exchange</td>
                                        <td class="py-2 dark:text-white">{{ $companyInfo['exchange'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-semibold mb-3 dark:text-white">Key Stats</h3>
                                <table class="w-full text-sm">
                                    <tr>
                                        <td class="py-2 text-gray-500">Market Cap</td>
                                        <td class="py-2 dark:text-white">{{ $companyStats['marketCap'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-gray-500">P/E Ratio</td>
                                        <td class="py-2 dark:text-white">{{ $companyStats['peRatio'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-gray-500">EPS</td>
                                        <td class="py-2 dark:text-white">{{ $companyStats['eps'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-gray-500">Dividend Yield</td>
                                        <td class="py-2 dark:text-white">{{ $companyStats['dividendYield'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-gray-500">52-Week Range</td>
                                        <td class="py-2 dark:text-white">{{ $companyStats['52WeekRange'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-gray-500">Beta</td>
                                        <td class="py-2 dark:text-white">{{ $companyStats['beta'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold mb-3 dark:text-white">About</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $companyInfo['description'] }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Recent Trades Tab -->
                    <div id="trades-tab" class="trade-tab hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-light-200 dark:divide-dark-100">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shares</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-light-200 dark:divide-dark-100">
                                    @if(isset($recentTrades) && count($recentTrades) > 0)
                                        @foreach($recentTrades as $trade)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm {{ $trade['direction'] == 'up' ? 'text-green-500' : 'text-red-500' }}">
                                                    ${{ $trade['price'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm dark:text-white">
                                                    {{ $trade['amount'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $trade['time'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @php
                                            // Generate sample trades for demonstration
                                            $sampleTrades = [];
                                            $basePrice = 148.56;
                                            $now = time();
                                            
                                            for($i = 0; $i < 10; $i++) {
                                                $direction = rand(0, 1) ? 'up' : 'down';
                                                $price = $basePrice + ($direction == 'up' ? rand(1, 10) / 100 : -rand(1, 10) / 100);
                                                $amount = rand(1, 100);
                                                $time = date('H:i:s', $now - rand(30, 3600));
                                                
                                                $sampleTrades[] = [
                                                    'price' => number_format($price, 2),
                                                    'amount' => $amount,
                                                    'time' => $time,
                                                    'direction' => $direction
                                                ];
                                            }
                                        @endphp
                                        
                                        @foreach($sampleTrades as $trade)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm {{ $trade['direction'] == 'up' ? 'text-green-500' : 'text-red-500' }}">
                                                    ${{ $trade['price'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm dark:text-white">
                                                    {{ $trade['amount'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $trade['time'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Open Orders Tab -->
                    <div id="orders-tab" class="trade-tab hidden">
                        @if(isset($openOrders) && count($openOrders) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-light-200 dark:divide-dark-100">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-light-200 dark:divide-dark-100">
                                        @foreach($openOrders as $order)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm dark:text-white">
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $order['side'] == 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($order['side']) }} {{ ucfirst($order['type']) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm dark:text-white">
                                                    ${{ $order['price'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm dark:text-white">
                                                    {{ $order['amount'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $order['time'] }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    <button class="cancel-order-btn text-red-500 hover:text-red-700" data-order-id="{{ $order['id'] }}">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-2"></i>
                                <p class="text-gray-500">You don't have any open orders for {{ $symbol ?? 'AAPL' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order form (1/3 width on large screens) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Trade box -->
            <div class="bg-light-50 dark:bg-dark-50 rounded-lg shadow-sm p-4">
                <div class="flex border-b border-light-200 dark:border-dark-100 mb-4">
                    <button data-side="buy" class="order-tab w-1/2 py-2 text-center border-b-2 border-primary text-primary active">Buy</button>
                    <button data-side="sell" class="order-tab w-1/2 py-2 text-center border-b-2 border-transparent dark:text-gray-400 text-gray-600">Sell</button>
                </div>
                
                <form id="orderForm" class="space-y-4">
                    <input type="hidden" id="orderSide" name="side" value="buy">
                    <input type="hidden" name="symbol" value="{{ $symbol ?? 'AAPL' }}">
                    <input type="hidden" name="market_type" value="stock">
                    
                    <!-- Order type selection -->
                    <div>
                        <div class="text-xs text-gray-500 mb-2">Order Type</div>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" data-type="market" class="order-type-btn text-sm py-2 text-center rounded dark:bg-dark-100 active">Market</button>
                            <button type="button" data-type="limit" class="order-type-btn text-sm py-2 text-center rounded dark:bg-dark-50 bg-light-100">Limit</button>
                            <button type="button" data-type="stop_limit" class="order-type-btn text-sm py-2 text-center rounded dark:bg-dark-50 bg-light-100">Stop Limit</button>
                        </div>
                        <input type="hidden" id="orderType" name="order_type" value="market">
                    </div>
                    
                    <!-- Price input (hidden for market orders) -->
                    <div id="priceInputContainer" class="hidden">
                        <label class="block text-xs text-gray-500 mb-2">Price ($)</label>
                        <input type="number" id="priceInput" name="price" step="0.01" min="0.01" class="w-full p-2 rounded bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 dark:text-white" placeholder="Enter price">
                    </div>
                    
                    <!-- Stop price input (hidden for market and limit orders) -->
                    <div id="stopPriceInputContainer" class="hidden">
                        <label class="block text-xs text-gray-500 mb-2">Stop Price ($)</label>
                        <input type="number" name="stop_price" step="0.01" min="0.01" class="w-full p-2 rounded bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 dark:text-white" placeholder="Enter stop price">
                    </div>
                    
                    <!-- Amount input -->
                    <div>
                        <label class="block text-xs text-gray-500 mb-2">Amount (Shares)</label>
                        <input type="number" id="amountInput" name="quantity" step="1" min="1" class="w-full p-2 rounded bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 dark:text-white" placeholder="Enter amount">
                        
                        <!-- Percentage buttons -->
                        <div class="grid grid-cols-4 gap-2 mt-2">
                            <button type="button" data-percentage="25" class="percentage-btn text-xs py-1 rounded bg-light-100 dark:bg-dark-100 hover:bg-light-200 dark:hover:bg-dark-200">25%</button>
                            <button type="button" data-percentage="50" class="percentage-btn text-xs py-1 rounded bg-light-100 dark:bg-dark-100 hover:bg-light-200 dark:hover:bg-dark-200">50%</button>
                            <button type="button" data-percentage="75" class="percentage-btn text-xs py-1 rounded bg-light-100 dark:bg-dark-100 hover:bg-light-200 dark:hover:bg-dark-200">75%</button>
                            <button type="button" data-percentage="100" class="percentage-btn text-xs py-1 rounded bg-light-100 dark:bg-dark-100 hover:bg-light-200 dark:hover:bg-dark-200">100%</button>
                        </div>
                    </div>
                    
                    <!-- Total calculation -->
                    <div class="border-t border-light-200 dark:border-dark-100 pt-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Total</span>
                            <span class="font-semibold dark:text-white">$<span id="orderTotal">0.00</span></span>
                        </div>
                        
                        <div class="text-xs text-gray-500 mt-2">
                            Available: ${{ $availableBalance ?? '10,000.00' }}
                        </div>
                    </div>
                    
                    <!-- Submit button -->
                    <button type="submit" id="submitOrderBtn" class="w-full py-3 rounded buy-btn">Buy {{ $symbol ?? 'AAPL' }}</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Watchlist Table -->
    @if(isset($watchlist) && $watchlist->items->count() > 0)
    <div class="mt-6 bg-light-50 dark:bg-dark-50 rounded-lg shadow-sm p-4">
        <h3 class="text-lg font-medium mb-4 dark:text-white">Your Watchlist</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-light-200 dark:divide-dark-100">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-light-200 dark:divide-dark-100">
                    @foreach($watchlist->items as $item)
                    <tr class="watchlist-row {{ $item->symbol === $symbol ? 'dark:bg-dark-50 bg-light-100' : '' }}" data-symbol="{{ $item->symbol }}">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('trading.stocks', ['symbol' => $item->symbol]) }}" class="text-primary hover:underline font-medium">
                                {{ $item->symbol }}
                            </a>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm dark:text-white">
                            {{ $item->name ?? $item->symbol }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap font-medium dark:text-white stock-price">
                            {{ $item->price_formatted ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap stock-change text-sm {{ ($item->change_percent ?? 0) >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $item->change_percent_formatted ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('trading.stocks', ['symbol' => $item->symbol]) }}" class="text-primary hover:text-primary-dark">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                                <button 
                                    class="remove-from-watchlist text-gray-500 hover:text-red-500" 
                                    data-id="{{ $item->id }}"
                                    data-symbol="{{ $item->symbol }}"
                                    onclick="removeFromWatchlist({{ $item->id }}, '{{ $item->symbol }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

@endsection

@section('scripts')
<script src="https://unpkg.com/lightweight-charts@3.8.0/dist/lightweight-charts.standalone.production.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Trading Chart Initialization
        const chartContainer = document.getElementById('tradingChart');
        
        // Make sure we have the chart container
        if (!chartContainer) {
            console.error('Chart container not found');
            return;
        }
        
        try {
            const chart = LightweightCharts.createChart(chartContainer, {
                width: chartContainer.clientWidth,
                height: 400,
                layout: {
                    backgroundColor: '#00000000',
                    textColor: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#374151',
                    fontSize: 12,
                },
                grid: {
                    vertLines: {
                        visible: false,
                    },
                    horzLines: {
                        color: document.documentElement.classList.contains('dark') ? 'rgba(42, 48, 60, 0.5)' : 'rgba(226, 232, 240, 0.5)',
                    },
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                timeScale: {
                    borderVisible: false,
                    timeVisible: true,
                },
            });
            
            // Add series with error handling
            try {
                // Candlestick series
                const candlestickSeries = chart.addCandlestickSeries({
                    upColor: '#4A9D7F',
                    downColor: '#FF6B6B',
                    borderUpColor: '#4A9D7F',
                    borderDownColor: '#FF6B6B',
                    wickUpColor: '#4A9D7F',
                    wickDownColor: '#FF6B6B',
                });
                
                // Generate sample data
                function generateSampleData() {
                    const data = [];
                    let basePrice = 148.56; // Starting price for AAPL
                    const currentTime = Math.floor(Date.now() / 1000);
                    
                    for (let i = 0; i < 100; i++) {
                        const time = currentTime - ((100 - i) * 86400);
                        const volatility = basePrice * 0.02;
                        const open = basePrice;
                        const high = open + (Math.random() * volatility);
                        const low = open - (Math.random() * volatility);
                        const close = (Math.random() > 0.5) ? 
                            low + (Math.random() * (high - low)) : 
                            high - (Math.random() * (high - low));
                        
                        data.push({
                            time: time,
                            open: open,
                            high: high,
                            low: low,
                            close: close,
                        });
                        
                        basePrice = close;
                    }
                    
                    return data;
                }
                
                // Set data and make responsive
                candlestickSeries.setData(generateSampleData());
                
                function resizeChart() {
                    chart.applyOptions({
                        width: chartContainer.clientWidth,
                    });
                    chart.timeScale().fitContent();
                }
                
                window.addEventListener('resize', resizeChart);
            } catch (chartError) {
                console.error('Error setting up chart series:', chartError);
                
                // Fallback to a basic line chart if candlestick fails
                try {
                    const lineSeries = chart.addLineSeries({
                        color: '#4A9D7F',
                        lineWidth: 2,
                    });
                    
                    // Generate simple line data
                    const lineData = [];
                    let basePrice = 148.56;
                    const currentTime = Math.floor(Date.now() / 1000);
                    
                    for (let i = 0; i < 100; i++) {
                        const time = currentTime - ((100 - i) * 86400);
                        basePrice = basePrice + (Math.random() * 3 - 1.5);
                        
                        lineData.push({
                            time: time,
                            value: basePrice
                        });
                    }
                    
                    lineSeries.setData(lineData);
                    chart.timeScale().fitContent();
                } catch (fallbackError) {
                    console.error('Even fallback chart failed:', fallbackError);
                    chartContainer.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-red-500">Chart could not be loaded. Please refresh.</p></div>';
                }
            }
        } catch (error) {
            console.error('Error creating chart:', error);
            chartContainer.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-red-500">Chart could not be loaded. Please refresh.</p></div>';
        }
        
        // Basic UI interactions
        const timeframeButtons = document.querySelectorAll('.timeframe-btn');
        timeframeButtons.forEach(button => {
            button.addEventListener('click', function() {
                timeframeButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-primary', 'text-white');
                    btn.classList.add('dark:bg-dark-50', 'bg-light-100', 'dark:text-gray-300', 'text-gray-700');
                });
                
                this.classList.add('active', 'bg-primary', 'text-white');
                this.classList.remove('dark:bg-dark-50', 'bg-light-100', 'dark:text-gray-300', 'text-gray-700');
            });
        });
        
        // Tab switching
        const tradeTabButtons = document.querySelectorAll('.trade-tab-btn');
        const tradeTabs = document.querySelectorAll('.trade-tab');
        
        tradeTabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                tradeTabButtons.forEach(btn => {
                    btn.classList.remove('dark:border-primary', 'border-primary', 'dark:text-white', 'text-dark');
                    btn.classList.add('border-transparent', 'dark:text-gray-400', 'text-gray-600');
                });
                
                tradeTabs.forEach(tab => tab.classList.add('hidden'));
                
                this.classList.add('dark:border-primary', 'border-primary', 'dark:text-white', 'text-dark');
                this.classList.remove('border-transparent', 'dark:text-gray-400', 'text-gray-600');
                
                document.getElementById(`${tabName}-tab`).classList.remove('hidden');
            });
        });
        
        // Order form handling
        const orderTabs = document.querySelectorAll('.order-tab');
        const orderSideInput = document.getElementById('orderSide');
        const submitOrderBtn = document.getElementById('submitOrderBtn');
        
        // Buy/Sell tab switching
        orderTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const side = this.dataset.side;
                
                // Update UI
                orderTabs.forEach(t => {
                    t.classList.remove('dark:border-primary', 'border-primary', 'text-primary', 'active');
                    t.classList.add('border-transparent', 'dark:text-gray-400', 'text-gray-600');
                });
                
                this.classList.add('dark:border-primary', 'border-primary', 'text-primary', 'active');
                this.classList.remove('border-transparent', 'dark:text-gray-400', 'text-gray-600');
                
                // Update order side
                orderSideInput.value = side;
                
                // Update submit button
                submitOrderBtn.textContent = side === 'buy' ? 'Buy {{ $symbol ?? 'AAPL' }}' : 'Sell {{ $symbol ?? 'AAPL' }}';
                submitOrderBtn.classList.remove('buy-btn', 'sell-btn');
                submitOrderBtn.classList.add(side === 'buy' ? 'buy-btn' : 'sell-btn');
            });
        });
        
        // Order type handling
        const orderTypeButtons = document.querySelectorAll('.order-type-btn');
        const orderTypeInput = document.getElementById('orderType');
        const priceInputContainer = document.getElementById('priceInputContainer');
        const stopPriceInputContainer = document.getElementById('stopPriceInputContainer');
        
        orderTypeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const type = this.dataset.type;
                
                // Update UI
                orderTypeButtons.forEach(btn => {
                    btn.classList.remove('active', 'dark:bg-dark-100');
                    btn.classList.add('dark:bg-dark-50', 'bg-light-100');
                });
                
                this.classList.add('active', 'dark:bg-dark-100');
                this.classList.remove('dark:bg-dark-50', 'bg-light-100');
                
                // Update order type
                orderTypeInput.value = type;
                
                // Show/hide price inputs based on order type
                if (type === 'market') {
                    priceInputContainer.classList.add('hidden');
                    stopPriceInputContainer.classList.add('hidden');
                } else if (type === 'limit') {
                    priceInputContainer.classList.remove('hidden');
                    stopPriceInputContainer.classList.add('hidden');
                } else if (type === 'stop_limit') {
                    priceInputContainer.classList.remove('hidden');
                    stopPriceInputContainer.classList.remove('hidden');
                }
                
                // Update total calculation
                updateOrderTotal();
            });
        });
        
        // Percentage buttons
        const percentageButtons = document.querySelectorAll('.percentage-btn');
        const amountInput = document.getElementById('amountInput');
        
        percentageButtons.forEach(button => {
            button.addEventListener('click', function() {
                const percentage = parseInt(this.dataset.percentage);
                
                // Calculate shares based on available balance
                const availableBalance = parseFloat('{{ $availableBalance ?? '10000.00' }}');
                const currentPrice = ({{ $ticker['lastPrice'] ?? '148.56' }});
                const maxShares = Math.floor(availableBalance / currentPrice);
                const amount = Math.floor(maxShares * percentage / 100);
                
                amountInput.value = amount;
                
                // Update total calculation
                updateOrderTotal();
            });
        });
        
        // Calculate and update order total
        function updateOrderTotal() {
            const orderType = orderTypeInput.value;
            const amount = parseFloat(amountInput.value) || 0;
            const price = orderType === 'market' 
                ? ({{ $ticker['lastPrice'] ?? '148.56' }}) 
                : (parseFloat(document.getElementById('priceInput').value) || 0);
            
            const total = (amount * price).toFixed(2);
            document.getElementById('orderTotal').textContent = total;
        }
        
        // Update total when inputs change
        amountInput.addEventListener('input', updateOrderTotal);
        if (document.getElementById('priceInput')) {
            document.getElementById('priceInput').addEventListener('input', updateOrderTotal);
        }
        
        // Form submission
        const orderForm = document.getElementById('orderForm');
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(orderForm);
            const orderData = Object.fromEntries(formData.entries());
            
            // Send order to server
            fetch('{{ route('api.trading.order.create') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    
                    if (data.order) {
                        // Reset form
                        orderForm.reset();
                        orderTypeInput.value = 'market';
                        orderSideInput.value = 'buy';
                        updateOrderTotal();
                        
                        // Show orders tab
                        document.querySelector('[data-tab="orders"]').click();
                    }
                }
            })
            .catch(error => {
                console.error('Error creating order', error);
                alert('Error creating order. Please try again.');
            });
        });
        
        // Refresh button
        const refreshBtn = document.getElementById('refreshBtn');
        refreshBtn.addEventListener('click', function() {
            // Refresh both main stock data and watchlist
            refreshStockData();
            updateStockWatchlistPrices();
            
            // Rotate the icon for visual feedback
            this.querySelector('i').classList.add('animate-spin');
            setTimeout(() => {
                this.querySelector('i').classList.remove('animate-spin');
            }, 1000);
        });
        
        // Cancel order buttons
        const cancelOrderBtns = document.querySelectorAll('.cancel-order-btn');
        cancelOrderBtns.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                
                if (confirm('Are you sure you want to cancel this order?')) {
                    fetch(`{{ url('dashboard/api/trading/order') }}/${orderId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            alert(data.message);
                            
                            // Remove the order from the list
                            if (data.success) {
                                this.closest('tr').remove();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error cancelling order', error);
                        alert('Error cancelling order. Please try again.');
                    });
                }
            });
        });

       
        
        
        
        // Set up auto-refresh every 30 seconds for watchlist prices
        setInterval(updateStockWatchlistPrices, 30000);
        
        // Initialize watchlist prices immediately
        updateStockWatchlistPrices();
    });
    
    // Helper function to update market info elements
        function updateMarketInfo(elementId, value) {
            const element = document.getElementById(elementId);
            if (element && value) {
                element.textContent = '$' + parseFloat(value).toFixed(2);
            }
        }
    
     // For stock data, refresh data periodically
       function refreshStockData() {
    const symbol = document.querySelector('[name="symbol"]').value || '{{ $symbol }}';
    const timestamp = new Date().getTime(); // Add timestamp to prevent caching
    
    fetch(`/dashboard/api/trading/stocks/quote/${symbol}?t=${timestamp}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data) {
                // Update main ticker display
                const priceElement = document.getElementById('currentPrice');
                if (priceElement) {
                    priceElement.textContent = data.priceFormatted || parseFloat(data.price).toFixed(2);
                }
                
                // Update price change
                const changeElement = document.getElementById('priceChange');
                const changePercentElement = document.getElementById('priceChangePercent');
                
                if (changeElement && changePercentElement) {
                    const change = parseFloat(data.change);
                    const changePercent = parseFloat(data.changePercent);
                    
                    const formattedChange = change >= 0 ? '+' + change.toFixed(2) : change.toFixed(2);
                    const formattedPercent = '(' + changePercent.toFixed(2) + '%)';
                    
                    changeElement.textContent = formattedChange;
                    changePercentElement.textContent = formattedPercent;
                    
                    // Set color based on price change
                    const colorClass = change >= 0 ? 'text-green-500' : 'text-red-500';
                    changeElement.className = colorClass;
                    changePercentElement.className = colorClass;
                }
                
                // Update market info
                updateMarketInfo('highPrice', data.high);
                updateMarketInfo('lowPrice', data.low);
                updateMarketInfo('openPrice', data.open);
                updateMarketInfo('prevClosePrice', data.prevClose);
                
                // Update volume with formatting
                const volumeElement = document.getElementById('volume');
                if (volumeElement && data.volume) {
                    volumeElement.textContent = new Intl.NumberFormat().format(data.volume);
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing stock data:', error);
        });
}

    

    function updateStockWatchlistPrices() {
    const watchlistRows = document.querySelectorAll('.watchlist-row');
    if (watchlistRows.length === 0) return;
    
    console.log(`Updating prices for ${watchlistRows.length} stock watchlist items`);
    
    // Create an array to batch API requests
    const symbols = [];
    const rowsBySymbol = {};
    
    watchlistRows.forEach(row => {
        const symbol = row.dataset.symbol;
        if (symbol) {
            symbols.push(symbol);
            rowsBySymbol[symbol] = row;
        }
    });
    
    // Fetch data for all symbols (could be optimized with a batch API if available)
    symbols.forEach(symbol => {
        const timestamp = new Date().getTime(); // Add timestamp to prevent caching
        fetch(`/dashboard/api/trading/stocks/quote/${symbol}?t=${timestamp}`)
            .then(response => response.json())
            .then(data => {
                if (!data || (data.success === false)) {
                    console.error(`No valid data received for ${symbol}:`, data);
                    return;
                }
                
                const row = rowsBySymbol[symbol];
                if (!row) return;
                
                const priceElement = row.querySelector('.stock-price');
                const changeElement = row.querySelector('.stock-change');
                
                if (priceElement) {
                    const price = parseFloat(data.price);
                    if (!isNaN(price)) {
                        priceElement.textContent = `$${price.toFixed(2)}`;
                    }
                }
                
                if (changeElement) {
                    const changePercent = parseFloat(data.changePercent);
                    if (!isNaN(changePercent)) {
                        const isPositive = changePercent >= 0;
                        const formattedChange = `${isPositive ? '+' : ''}${changePercent.toFixed(2)}%`;
                        
                        changeElement.textContent = formattedChange;
                        
                        // Remove all color classes and add the right one
                        changeElement.classList.remove('text-red-500', 'text-green-500');
                        changeElement.classList.add(isPositive ? 'text-green-500' : 'text-red-500');
                    }
                }
            })
            .catch(error => {
                console.error(`Error fetching price for ${symbol}:`, error);
            });
    });
}
    
    // Function to handle removing items from the watchlist
    function removeFromWatchlist(itemId, symbol) {
        if (!itemId) {
            console.error('Item ID is required');
            return;
        }
        
        if (!confirm(`Remove ${symbol} from your watchlist?`)) {
            return;
        }
        
        fetch(`/dashboard/api/trading/watchlist/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Find and remove the row
                const row = document.querySelector(`.watchlist-row[data-symbol="${symbol}"]`);
                if (row) {
                    row.remove();
                    
                    // If this was the current symbol, update the UI to reflect it's no longer in the watchlist
                    const currentSymbol = '{{ $symbol }}';
                    if (symbol === currentSymbol) {
                        const watchlistButton = document.querySelector('[x-data]');
                        if (watchlistButton && typeof Alpine !== 'undefined') {
                            const component = Alpine.$data(watchlistButton);
                            if (component) {
                                component.inWatchlist = false;
                                component.itemId = null;
                            }
                        }
                    }
                    
                    // Show a success notification
                    if (typeof showNotification === 'function') {
                        showNotification('success', `${symbol} removed from watchlist`);
                    } else {
                        alert(`${symbol} removed from watchlist`);
                    }
                    
                    // Hide the watchlist container if there are no more items
                    const watchlistRows = document.querySelectorAll('.watchlist-row');
                    if (watchlistRows.length === 0) {
                        const watchlistContainer = document.querySelector('.watchlist-row').closest('.mt-6');
                        if (watchlistContainer) {
                            watchlistContainer.style.display = 'none';
                        }
                    }
                }
            } else {
                console.error('Error removing from watchlist:', data.message);
                alert(`Error: ${data.message || 'Could not remove item from watchlist'}`);
            }
        })
        .catch(err => {
            console.error('Error removing from watchlist:', err);
            alert('Could not remove item from watchlist. Please try again.');
        });
    }

    // Load stock symbols for dropdown
    function loadStockSymbols() {
        const loader = document.getElementById('symbolDropdownLoader');
        if (loader) loader.style.display = 'flex';
        
        fetch('/dashboard/api/trading/stocks/symbols?exchange=US')
            .then(response => response.json())
            .then(data => {
                if (data && (data.data || Array.isArray(data))) {
                    const stocksData = data.data || data;
                    const select = document.getElementById('stockSymbolSelect');
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
                    currentOption.textContent = `${currentSymbol} - {{ $fullName }}`;
                    currentOption.selected = true;
                    currentGroup.appendChild(currentOption);
                    select.appendChild(currentGroup);
                    
                    // Create an option group for the top stocks
                    const popularGroup = document.createElement('optgroup');
                    popularGroup.label = 'Popular Stocks';
                    
                    // Add all available stocks in alphabetical order by symbol
                    const sortedStocks = [...stocksData];
                    sortedStocks.sort((a, b) => a.symbol.localeCompare(b.symbol));
                    
                    sortedStocks.forEach(stock => {
                        if (stock.symbol !== currentSymbol) {
                            const option = document.createElement('option');
                            option.value = stock.symbol;
                            option.textContent = `${stock.symbol} - ${stock.description}`;
                            popularGroup.appendChild(option);
                        }
                    });
                    
                    // Add the popular stocks group
                    if (popularGroup.children.length > 0) {
                        select.appendChild(popularGroup);
                    }
                    
                    // Remove loading option if it exists
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].textContent === 'Loading more symbols...') {
                            select.remove(i);
                            break;
                        }
                    }
                } else {
                    console.error('Invalid data format for stock symbols');
                }
                
                // Hide loader
                if (loader) loader.style.display = 'none';
            })
            .catch(error => {
                console.error('Error loading stock symbols:', error);
                // Hide loader even on error
                if (loader) loader.style.display = 'none';
            });
    }
    
    // Handle dropdown change
    document.getElementById('stockSymbolSelect').addEventListener('change', function() {
        const symbol = this.value;
        if (symbol && symbol !== '{{ $symbol }}') {
            // Show loading indicator
            const loader = document.createElement('div');
            loader.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            loader.innerHTML = '<div class="bg-white dark:bg-dark-50 p-4 rounded-lg shadow-lg flex items-center space-x-3">' +
                '<i class="fas fa-spinner fa-spin text-primary"></i>' +
                '<span class="text-gray-700 dark:text-gray-300">Loading stock data...</span>' +
                '</div>';
            document.body.appendChild(loader);
            
            // Navigate to the selected stock
            window.location.href = `/dashboard/trading/stocks/${symbol}`;
        }
    });
    
    // Keep track of page activity
    let pageActive = true;
    let refreshInterval = null;
    
    // User activity detection
    function resetActivity() {
        pageActive = true;
        document.body.classList.remove('inactive');
        
        // If refresh interval is not set, restart it
        if (!refreshInterval) {
            startRefreshInterval();
        }
    }
    
    // Start the refresh interval
    function startRefreshInterval() {
        // Clear any existing interval first
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        
        // Set up auto-refresh every 30 seconds for stock data if page is active
        refreshInterval = setInterval(() => {
            if (pageActive) {
                refreshStockData();
                updateStockWatchlistPrices();
            }
        }, 30000);
    }
    
    // Add event listeners for page activity
    document.addEventListener('mousemove', resetActivity);
    document.addEventListener('keypress', resetActivity);
    document.addEventListener('click', resetActivity);
    document.addEventListener('scroll', resetActivity);
    
    // Handle page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            resetActivity();
            refreshStockData(); // Refresh immediately when page becomes visible
        } else {
            pageActive = false;
        }
    });
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Load stock symbols
        loadStockSymbols();
        
        // Initial data load
        refreshStockData();
        
        // Start refresh interval
        startRefreshInterval();
        
        // Update watchlist prices on load
        setTimeout(updateStockWatchlistPrices, 500);
        
        // Set up auto-refresh every 30 seconds for stock data
        setInterval(refreshStockData, 30000);
        
        // Clean up when leaving page
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    });
</script>
@endsection 