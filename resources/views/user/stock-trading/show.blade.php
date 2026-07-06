@extends('layouts.dash')

@section('title', $stock->symbol . ' - Stock Trading')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center flex-wrap gap-3">
            <img src="{{ $stock->logo_url }}" alt="{{ $stock->symbol }}" class="w-12 h-12 sm:w-16 sm:h-16 rounded-full flex-shrink-0" onerror="this.src='data:image/svg+xml;base64,{{ base64_encode($stock->getPlaceholderSvg()) }}'">
            <div class="flex-1 min-w-0">
                <h1 class="text-lg sm:text-2xl md:text-3xl font-bold text-white">{{ $stock->symbol }}</h1>
                <p class="text-xs sm:text-sm text-gray-400 mt-1 truncate">{{ $stock->name }} • {{ $stock->exchange }}</p>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('stock-trading.portfolio') }}" class="bg-primary-500 hover:bg-primary-500 text-white px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg flex items-center justify-center space-x-1 sm:space-x-2 flex-1 sm:flex-initial">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-xs sm:text-sm whitespace-nowrap">My Portfolio</span>
                </a>
                <form action="{{ route('stock-trading.watchlist.add') }}" method="POST" class="flex-1 sm:flex-initial">
                    @csrf
                    <input type="hidden" name="symbol" value="{{ $stock->symbol }}">
                    <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg flex items-center justify-center space-x-1 sm:space-x-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        <span class="text-xs sm:text-sm whitespace-nowrap">Add to Watchlist</span>
                    </button>
                </form>
            </div>
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

    @if($errors->any())
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Chart & Info -->
        <div class="lg:col-span-2 space-y-6 order-2 lg:order-1">
            <!-- Price Overview -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">Current Price</p>
                    <p class="text-2xl font-bold text-white">${{ number_format($stock->price, 2) }}</p>
                    <p class="text-sm {{ $stock->percent_change >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                        {{ $stock->percent_change >= 0 ? '+' : '' }}{{ number_format($stock->change, 2) }} ({{ $stock->percent_change >= 0 ? '+' : '' }}{{ number_format($stock->percent_change, 2) }}%)
                    </p>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">Open</p>
                    <p class="text-xl font-semibold text-white">${{ number_format($stock->open, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Previous: ${{ number_format($stock->previous_close, 2) }}</p>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">Day Range</p>
                    <p class="text-xl font-semibold text-white">${{ number_format($stock->low, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-1">High: ${{ number_format($stock->high, 2) }}</p>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">Volume</p>
                    <p class="text-xl font-semibold text-white">{{ number_format($stock->volume / 1000000, 2) }}M</p>
                    @if($stock->market_cap)
                    <p class="text-sm text-gray-500 mt-1">Cap: {{ number_format($stock->market_cap / 1000000000, 2) }}B</p>
                    @endif
                </div>
            </div>

            <!-- TradingView Chart -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-white mb-4">Chart</h2>
                
                <div class="tradingview-widget-container" style="min-height:800px; height:800px; width:100%;">
                    <div id="tradingview_widget" style="height:800px; width:100%;"></div>
                </div>
            </div>

            <!-- Your Position -->
            @if($position)
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-white mb-4">Your Position</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm">Quantity</p>
                        <p class="text-xl font-bold text-white mt-1">{{ $position['quantity'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Avg. Cost</p>
                        <p class="text-xl font-bold text-white mt-1">${{ number_format($position['avg_cost'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Current Value</p>
                        <p class="text-xl font-bold text-white mt-1">${{ number_format($position['current_value'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">P/L</p>
                        <p class="text-xl font-bold {{ $position['profit_loss'] >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                            {{ $position['profit_loss'] >= 0 ? '+' : '' }}${{ number_format($position['profit_loss'], 2) }}
                        </p>
                        <p class="text-sm {{ $position['profit_loss_percent'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ $position['profit_loss_percent'] >= 0 ? '+' : '' }}{{ number_format($position['profit_loss_percent'], 2) }}%
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">ROI</p>
                        <p class="text-xl font-bold {{ $roi >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                            {{ $roi >= 0 ? '+' : '' }}{{ number_format($roi, 2) }}%
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Return on Investment</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Trade History -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-white mb-4">Your Trade History</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Date</th>
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Type</th>
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Status</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Quantity</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Price</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Fee</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userTrades as $trade)
                            <tr class="border-b border-gray-800 hover:bg-gray-900/30">
                                <td class="py-3 px-2 text-gray-300 text-sm">{{ $trade->created_at->format('M d, Y H:i') }}</td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $trade->trade_type === 'buy' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                        {{ strtoupper($trade->trade_type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $trade->status === 'completed' ? 'bg-blue-500/10 text-blue-400' : ($trade->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-gray-500/10 text-gray-400') }}">
                                        {{ strtoupper($trade->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right text-white">{{ $trade->quantity }}</td>
                                <td class="py-3 px-2 text-right text-white">${{ number_format($trade->price, 2) }}</td>
                                <td class="py-3 px-2 text-right text-gray-400">${{ number_format($trade->fee_amount, 2) }}</td>
                                <td class="py-3 px-2 text-right text-white font-semibold">${{ number_format($trade->net_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    No trades yet for this stock
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Open Orders (Order Book) -->
            @if($pendingOrders->count() > 0)
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Open Orders</h2>
                    <span class="text-sm text-gray-400">{{ $pendingOrders->count() }} pending</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Date</th>
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Side</th>
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Type</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Quantity</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Target/Trigger</th>
                                <th class="text-right text-gray-400 font-medium py-2 px-2">Current</th>
                                <th class="text-left text-gray-400 font-medium py-2 px-2">Expiry</th>
                                <th class="text-center text-gray-400 font-medium py-2 px-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingOrders as $order)
                            <tr class="border-b border-gray-800 hover:bg-gray-900/30">
                                <td class="py-3 px-2 text-gray-300 text-sm">{{ $order->created_at->format('M d, H:i') }}</td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $order->trade_type === 'buy' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                        {{ strtoupper($order->trade_type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-500/10 text-purple-400">
                                        {{ strtoupper($order->order_type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right text-white">{{ $order->quantity }}</td>
                                <td class="py-3 px-2 text-right text-white">
                                    @if($order->order_type === 'limit')
                                        ${{ number_format($order->target_price, 2) }}
                                    @elseif($order->order_type === 'stop')
                                        <div class="text-xs">
                                            <div>Trigger: ${{ number_format($order->trigger_price, 2) }}</div>
                                            <div class="text-gray-400">Execute: ${{ number_format($order->target_price, 2) }}</div>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <span class="text-white">${{ number_format($stock->price, 2) }}</span>
                                    @php
                                        $priceDiff = 0;
                                        if($order->order_type === 'limit') {
                                            $priceDiff = (($stock->price - $order->target_price) / $order->target_price) * 100;
                                        } elseif($order->order_type === 'stop') {
                                            $priceDiff = (($stock->price - $order->trigger_price) / $order->trigger_price) * 100;
                                        }
                                    @endphp
                                    <div class="text-xs {{ $priceDiff >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $priceDiff >= 0 ? '+' : '' }}{{ number_format($priceDiff, 2) }}%
                                    </div>
                                </td>
                                <td class="py-3 px-2 text-gray-300 text-sm">
                                    @if($order->expiry_type === 'gtc')
                                        <span class="text-blue-400">GTC</span>
                                    @elseif($order->expiry_type === 'day')
                                        <span class="text-yellow-400">Day</span>
                                    @else
                                        <span class="text-gray-400">{{ $order->expires_at->format('M d') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <form action="{{ route('stock-trading.order.cancel', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Cancel this order?')" 
                                            class="text-red-400 hover:text-red-300 text-sm font-medium">
                                            Cancel
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Trade Form -->
        <div class="lg:col-span-1 order-1 lg:order-2">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg p-4 sticky top-4">
                <h2 class="text-lg font-semibold text-white mb-4">Trade</h2>

                <!-- Trade Type Tabs -->
                <div class="flex space-x-2 mb-4 relative z-20">
                    <button type="button" onclick="switchTradeType('buy')" id="buyTab" class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg font-semibold transition-colors cursor-pointer z-10 relative">
                        Buy
                    </button>
                    <button type="button" onclick="switchTradeType('sell')" id="sellTab" class="flex-1 bg-gray-700 text-white py-2 px-4 rounded-lg font-semibold transition-colors cursor-pointer z-10 relative">
                        Sell
                    </button>
                </div>

                <!-- Buy Form -->
                <form id="buyForm" action="{{ route('stock-trading.order.place') }}" method="POST" class="space-y-4 relative z-10">
                    @csrf
                    <input type="hidden" name="symbol" value="{{ $stock->symbol }}">
                    <input type="hidden" name="trade_type" value="buy">

                    <!-- Order Type Selector -->
                    <div>
                        <label class="text-gray-400 text-sm block mb-2">Order Type</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="selectOrderType('buy', 'market')" id="buyMarketBtn" class="order-type-btn bg-primary-500 text-white py-2 rounded-lg text-sm font-semibold">
                                Market
                            </button>
                            <button type="button" onclick="selectOrderType('buy', 'limit')" id="buyLimitBtn" class="order-type-btn bg-gray-700 text-white py-2 rounded-lg text-sm font-semibold">
                                Limit
                            </button>
                            <button type="button" onclick="selectOrderType('buy', 'stop')" id="buyStopBtn" class="order-type-btn bg-gray-700 text-white py-2 rounded-lg text-sm font-semibold">
                                Stop
                            </button>
                        </div>
                        <input type="hidden" name="order_type" id="buyOrderType" value="market">
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm block mb-2">Quantity</label>
                        <input type="number" name="quantity" id="buyQuantity" min="1" step="1" required
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary-500"
                            oninput="calculateBuyTotal()">
                    </div>

                    <!-- Current Price (for reference) -->
                    <div id="buyCurrentPriceDiv">
                        <label class="text-gray-400 text-sm block mb-2">Current Market Price</label>
                        <input type="text" value="${{ number_format($stock->price, 2) }}" readonly
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-gray-400">
                    </div>

                    <!-- Target Price (for limit/stop orders) -->
                    <div id="buyTargetPriceDiv" class="hidden">
                        <label class="text-gray-400 text-sm block mb-2">
                            <span id="buyTargetPriceLabel">Target Price</span>
                        </label>
                        <input type="number" name="target_price" id="buyTargetPrice" step="0.01" min="0.01"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white"
                            placeholder="Enter target price">
                    </div>

                    <!-- Trigger Price (for stop orders only) -->
                    <div id="buyTriggerPriceDiv" class="hidden">
                        <label class="text-gray-400 text-sm block mb-2">Stop Price (Trigger)</label>
                        <input type="number" name="trigger_price" id="buyTriggerPrice" step="0.01" min="0.01"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white"
                            placeholder="Enter stop trigger price">
                    </div>

                    <!-- Expiry Options -->
                    <div id="buyExpiryDiv" class="hidden">
                        <label class="text-gray-400 text-sm block mb-2">Order Expiry</label>
                        <select name="expiry_type" id="buyExpiryType" onchange="toggleExpiryDate('buy')"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white">
                            <option value="gtc">Good Till Cancelled</option>
                            <option value="day">Day Order (expires at market close)</option>
                            <option value="date">Custom Expiry Date</option>
                        </select>
                    </div>

                    <div id="buyExpiryDateDiv" class="hidden">
                        <input type="datetime-local" name="expires_at" id="buyExpiresAt"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white">
                    </div>

                    <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-3 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Subtotal</span>
                            <span class="text-white" id="buySubtotal">$0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Fee ({{ number_format($feePercent, 2) }}%)</span>
                            <span class="text-white" id="buyFee">$0.00</span>
                        </div>
                        <div class="flex justify-between text-base font-semibold border-t border-gray-700 pt-2">
                            <span class="text-gray-400">Total Cost</span>
                            <span class="text-white" id="buyTotal">$0.00</span>
                        </div>
                    </div>

                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Available Balance</span>
                            <span class="text-white font-semibold">${{ number_format(Auth::user()->account_bal, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" id="buySubmitBtn" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                        Buy {{ $stock->symbol }}
                    </button>
                </form>

                <!-- Sell Form -->
                <form id="sellForm" action="{{ route('stock-trading.order.place') }}" method="POST" class="space-y-4 hidden relative z-10">
                    @csrf
                    <input type="hidden" name="symbol" value="{{ $stock->symbol }}">
                    <input type="hidden" name="trade_type" value="sell">

                    <!-- Order Type Selector -->
                    <div>
                        <label class="text-gray-400 text-sm block mb-2">Order Type</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="selectOrderType('sell', 'market')" id="sellMarketBtn" class="order-type-btn bg-primary-500 text-white py-2 rounded-lg text-sm font-semibold">
                                Market
                            </button>
                            <button type="button" onclick="selectOrderType('sell', 'limit')" id="sellLimitBtn" class="order-type-btn bg-gray-700 text-white py-2 rounded-lg text-sm font-semibold">
                                Limit
                            </button>
                            <button type="button" onclick="selectOrderType('sell', 'stop')" id="sellStopBtn" class="order-type-btn bg-gray-700 text-white py-2 rounded-lg text-sm font-semibold">
                                Stop
                            </button>
                        </div>
                        <input type="hidden" name="order_type" id="sellOrderType" value="market">
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm block mb-2">Quantity</label>
                        <input type="number" name="quantity" id="sellQuantity" min="1" step="1" 
                            max="{{ $position ? $position['quantity'] : 0 }}" required
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary-500"
                            oninput="calculateSellTotal()">
                        @if($position)
                        <p class="text-xs text-gray-400 mt-1">Available: {{ $position['quantity'] }} shares</p>
                        @endif
                    </div>

                    <!-- Current Price (for reference) -->
                    <div id="sellCurrentPriceDiv">
                        <label class="text-gray-400 text-sm block mb-2">Current Market Price</label>
                        <input type="text" value="${{ number_format($stock->price, 2) }}" readonly
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-gray-400">
                    </div>

                    <!-- Target Price (for limit/stop orders) -->
                    <div id="sellTargetPriceDiv" class="hidden">
                        <label class="text-gray-400 text-sm block mb-2">
                            <span id="sellTargetPriceLabel">Target Price</span>
                        </label>
                        <input type="number" name="target_price" id="sellTargetPrice" step="0.01" min="0.01"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white"
                            placeholder="Enter target price">
                    </div>

                    <!-- Trigger Price (for stop orders only) -->
                    <div id="sellTriggerPriceDiv" class="hidden">
                        <label class="text-gray-400 text-sm block mb-2">Stop Price (Trigger)</label>
                        <input type="number" name="trigger_price" id="sellTriggerPrice" step="0.01" min="0.01"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white"
                            placeholder="Enter stop trigger price">
                    </div>

                    <!-- Expiry Options -->
                    <div id="sellExpiryDiv" class="hidden">
                        <label class="text-gray-400 text-sm block mb-2">Order Expiry</label>
                        <select name="expiry_type" id="sellExpiryType" onchange="toggleExpiryDate('sell')"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white">
                            <option value="gtc">Good Till Cancelled</option>
                            <option value="day">Day Order (expires at market close)</option>
                            <option value="date">Custom Expiry Date</option>
                        </select>
                    </div>

                    <div id="sellExpiryDateDiv" class="hidden">
                        <input type="datetime-local" name="expires_at" id="sellExpiresAt"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white">
                    </div>

                    <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-3 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Subtotal</span>
                            <span class="text-white" id="sellSubtotal">$0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Fee ({{ number_format($feePercent, 2) }}%)</span>
                            <span class="text-white" id="sellFee">$0.00</span>
                        </div>
                        <div class="flex justify-between text-base font-semibold border-t border-gray-700 pt-2">
                            <span class="text-gray-400">You'll Receive</span>
                            <span class="text-white" id="sellTotal">$0.00</span>
                        </div>
                    </div>

                    @if($position)
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Your Position</span>
                            <span class="text-white">{{ $position['quantity'] }} shares</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Avg. Cost</span>
                            <span class="text-white">${{ number_format($position['avg_cost'], 2) }}</span>
                        </div>
                    </div>
                    @endif

                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                        {{ !$position ? 'disabled' : '' }}>
                        {{ $position ? 'Sell ' . $stock->symbol : 'No Position to Sell' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TradingView Widget Script -->
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script type="text/javascript">
const stockPrice = parseFloat('{{ $stock->price }}') || 0;
const feePercent = parseFloat('{{ $feePercent }}') || 0;

// Trade Type Switching - Must be global
function switchTradeType(type) {
    const buyTab = document.getElementById('buyTab');
    const sellTab = document.getElementById('sellTab');
    const buyForm = document.getElementById('buyForm');
    const sellForm = document.getElementById('sellForm');

    if (type === 'buy') {
        buyTab.classList.remove('bg-gray-700');
        buyTab.classList.add('bg-green-500');
        sellTab.classList.remove('bg-red-500');
        sellTab.classList.add('bg-gray-700');
        buyForm.classList.remove('hidden');
        sellForm.classList.add('hidden');
    } else {
        sellTab.classList.remove('bg-gray-700');
        sellTab.classList.add('bg-red-500');
        buyTab.classList.remove('bg-green-500');
        buyTab.classList.add('bg-gray-700');
        sellForm.classList.remove('hidden');
        buyForm.classList.add('hidden');
    }
}

// Order Type Selection
function selectOrderType(tradeType, orderType) {
    const prefix = tradeType; // 'buy' or 'sell'
    const marketBtn = document.getElementById(prefix + 'MarketBtn');
    const limitBtn = document.getElementById(prefix + 'LimitBtn');
    const stopBtn = document.getElementById(prefix + 'StopBtn');
    const orderTypeInput = document.getElementById(prefix + 'OrderType');
    const targetPriceDiv = document.getElementById(prefix + 'TargetPriceDiv');
    const triggerPriceDiv = document.getElementById(prefix + 'TriggerPriceDiv');
    const expiryDiv = document.getElementById(prefix + 'ExpiryDiv');
    const currentPriceDiv = document.getElementById(prefix + 'CurrentPriceDiv');
    const targetPriceLabel = document.getElementById(prefix + 'TargetPriceLabel');
    const submitBtn = document.getElementById(prefix + 'SubmitBtn');
    
    // Reset all buttons
    [marketBtn, limitBtn, stopBtn].forEach(btn => {
        btn.classList.remove('bg-primary-500');
        btn.classList.add('bg-gray-700');
    });
    
    // Hide all conditional inputs
    targetPriceDiv.classList.add('hidden');
    triggerPriceDiv.classList.add('hidden');
    expiryDiv.classList.add('hidden');
    
    // Show selected and update UI
    if (orderType === 'market') {
        marketBtn.classList.add('bg-primary-500');
        marketBtn.classList.remove('bg-gray-700');
        currentPriceDiv.classList.remove('hidden');
        if (submitBtn) {
            submitBtn.textContent = (tradeType === 'buy' ? 'Buy ' : 'Sell ') + '{{ $stock->symbol }}' + ' (Market)';
        }
    } else if (orderType === 'limit') {
        limitBtn.classList.add('bg-primary-500');
        limitBtn.classList.remove('bg-gray-700');
        targetPriceDiv.classList.remove('hidden');
        expiryDiv.classList.remove('hidden');
        currentPriceDiv.classList.remove('hidden');
        targetPriceLabel.textContent = 'Limit Price';
        if (submitBtn) {
            submitBtn.textContent = 'Place Limit Order to ' + (tradeType === 'buy' ? 'Buy' : 'Sell');
        }
    } else if (orderType === 'stop') {
        stopBtn.classList.add('bg-primary-500');
        stopBtn.classList.remove('bg-gray-700');
        targetPriceDiv.classList.remove('hidden');
        triggerPriceDiv.classList.remove('hidden');
        expiryDiv.classList.remove('hidden');
        currentPriceDiv.classList.remove('hidden');
        targetPriceLabel.textContent = 'Execution Price';
        if (submitBtn) {
            submitBtn.textContent = 'Place Stop Order to ' + (tradeType === 'buy' ? 'Buy' : 'Sell');
        }
    }
    
    orderTypeInput.value = orderType;
}

// Toggle Expiry Date Input
function toggleExpiryDate(tradeType) {
    const expiryType = document.getElementById(tradeType + 'ExpiryType').value;
    const expiryDateDiv = document.getElementById(tradeType + 'ExpiryDateDiv');
    
    if (expiryType === 'date') {
        expiryDateDiv.classList.remove('hidden');
    } else {
        expiryDateDiv.classList.add('hidden');
    }
}

// Calculate Buy Total
function calculateBuyTotal() {
    const quantity = parseFloat(document.getElementById('buyQuantity').value) || 0;
    const subtotal = quantity * stockPrice;
    const fee = subtotal * (feePercent / 100);
    const total = subtotal + fee;

    document.getElementById('buySubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('buyFee').textContent = '$' + fee.toFixed(2);
    document.getElementById('buyTotal').textContent = '$' + total.toFixed(2);
}

// Calculate Sell Total
function calculateSellTotal() {
    const quantity = parseFloat(document.getElementById('sellQuantity').value) || 0;
    const subtotal = quantity * stockPrice;
    const fee = subtotal * (feePercent / 100);
    const total = subtotal - fee;

    document.getElementById('sellSubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('sellFee').textContent = '$' + fee.toFixed(2);
    document.getElementById('sellTotal').textContent = '$' + total.toFixed(2);
}

// Initialize TradingView Widget on page load
document.addEventListener('DOMContentLoaded', function() {
    new TradingView.widget({
        "width": "100%",
        "height": 800,
        "symbol": "{{ $stock->exchange }}:{{ $stock->symbol }}",
        "interval": "D",
        "timezone": "America/New_York",
        "theme": "dark",
        "style": "1",
        "locale": "en",
        "toolbar_bg": "#f1f3f6",
        "enable_publishing": false,
        "allow_symbol_change": false,
        "container_id": "tradingview_widget",
        "hide_top_toolbar": false,
        "hide_side_toolbar": false,
        "save_image": false
    });
});

// Trade Confirmation with SweetAlert
document.getElementById('buyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const quantity = document.getElementById('buyQuantity').value;
    const orderType = document.getElementById('buyOrderType').value;
    const total = document.getElementById('buyTotal').textContent;
    
    let priceInfo = '';
    if (orderType === 'limit') {
        const targetPrice = document.getElementById('buyTargetPrice').value;
        priceInfo = `Target Price: $${parseFloat(targetPrice).toFixed(2)}\n`;
    } else if (orderType === 'stop') {
        const triggerPrice = document.getElementById('buyTriggerPrice').value;
        priceInfo = `Trigger Price: $${parseFloat(triggerPrice).toFixed(2)}\n`;
    }
    
    const message = `Stock: {{ $stock->symbol }} ({{ $stock->name }})\n` +
                   `Order Type: ${orderType.toUpperCase()}\n` +
                   `Quantity: ${quantity} shares\n` +
                   priceInfo +
                   `\nTotal Cost: ${total}\n\n` +
                   `⚠️ This action cannot be undone. Please review carefully.`;
    
    swal({
        title: "Confirm Buy Order",
        text: message,
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancel",
                value: false,
                visible: true,
                className: "swal-button--cancel-red",
                closeModal: true,
            },
            confirm: {
                text: "Confirm Buy",
                value: true,
                visible: true,
                className: "swal-button--confirm-green",
                closeModal: true
            }
        },
        dangerMode: false,
    }).then((willProceed) => {
        if (willProceed) {
            form.submit();
        }
    });
});

document.getElementById('sellForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const quantity = document.getElementById('sellQuantity').value;
    const orderType = document.getElementById('sellOrderType').value;
    const total = document.getElementById('sellTotal').textContent;
    
    let priceInfo = '';
    if (orderType === 'limit') {
        const targetPrice = document.getElementById('sellTargetPrice').value;
        priceInfo = `Target Price: $${parseFloat(targetPrice).toFixed(2)}\n`;
    } else if (orderType === 'stop') {
        const triggerPrice = document.getElementById('sellTriggerPrice').value;
        priceInfo = `Trigger Price: $${parseFloat(triggerPrice).toFixed(2)}\n`;
    }
    
    const message = `Stock: {{ $stock->symbol }} ({{ $stock->name }})\n` +
                   `Order Type: ${orderType.toUpperCase()}\n` +
                   `Quantity: ${quantity} shares\n` +
                   priceInfo +
                   `\nTotal Proceeds: ${total}\n\n` +
                   `⚠️ This action cannot be undone. Please review carefully.`;
    
    swal({
        title: "Confirm Sell Order",
        text: message,
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancel",
                value: false,
                visible: true,
                className: "swal-button--cancel-red",
                closeModal: true,
            },
            confirm: {
                text: "Confirm Sell",
                value: true,
                visible: true,
                className: "swal-button--confirm-green",
                closeModal: true
            }
        },
        dangerMode: false,
    }).then((willProceed) => {
        if (willProceed) {
            form.submit();
        }
    });
});
</script>

<style>
.swal-button--confirm-green {
    background-color: #22c55e !important;
    color: white !important;
    font-weight: 600 !important;
}
.swal-button--confirm-green:hover {
    background-color: #16a34a !important;
}
.swal-button--cancel-red {
    background-color: #ef4444 !important;
    color: white !important;
    font-weight: 600 !important;
}
.swal-button--cancel-red:hover {
    background-color: #dc2626 !important;
}
</style>
@endsection
