@extends('layouts.dash')

@section('title', $crypto->name . ' Trading')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('crypto-trading.index') }}" class="text-primary-500 hover:text-primary-400 flex items-center mb-4 text-xs sm:text-sm">
            <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5 mr-2"></i>
            Back to Crypto Trading
        </a>
        
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center space-x-3 sm:space-x-4 min-w-0 flex-1">
                @if($crypto->logo)
                <img src="{{ $crypto->logo }}" alt="{{ $crypto->name }}" class="w-12 h-12 sm:w-16 sm:h-16 rounded-full flex-shrink-0">
                @else
                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-primary-500/10 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="bitcoin" class="w-6 h-6 sm:w-8 sm:h-8 text-primary-500"></i>
                </div>
                @endif
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">{{ $crypto->name }}</h1>
                    <p class="text-xs sm:text-sm text-gray-400">{{ $crypto->coin_symbol }}</p>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xl sm:text-2xl md:text-3xl font-bold text-white">${{ number_format($crypto->price_usd, 2) }}</p>
                <p class="text-sm sm:text-base md:text-lg {{ $crypto->percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $crypto->percent_change_24h >= 0 ? '+' : '' }}{{ number_format($crypto->percent_change_24h, 2) }}% (24h)
                </p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Trading Form -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
                <h2 class="text-xl font-bold text-white mb-4">Place Order</h2>
                
                <form id="tradeForm" action="{{ route('crypto-trading.order.place') }}" method="POST">
                    @csrf
                    <input type="hidden" name="coin_symbol" value="{{ $crypto->coin_symbol }}">
                    
                    <!-- Trade Type Tabs -->
                    <div class="flex space-x-2 mb-4">
                        <button type="button" onclick="setTradeType('buy')" class="flex-1 py-2 rounded-lg font-semibold transition-all duration-200 bg-green-500 text-white" id="buyTab">
                            Buy
                        </button>
                        <button type="button" onclick="setTradeType('sell')" class="flex-1 py-2 rounded-lg font-semibold transition-all duration-200 bg-gray-700 text-gray-400" id="sellTab">
                            Sell
                        </button>
                    </div>
                    <input type="hidden" name="trade_type" id="tradeType" value="buy">
                    
                    <!-- Order Type -->
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm font-semibold mb-2">Order Type</label>
                        <select name="order_type" id="orderType" class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:border-primary-500" onchange="handleOrderTypeChange()">
                            <option value="market">Market Order (Instant)</option>
                            <option value="limit">Limit Order</option>
                            <option value="stop_loss">Stop-Loss Order</option>
                        </select>
                    </div>
                    
                    <!-- Quantity -->
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm font-semibold mb-2">Quantity</label>
                        <input type="number" name="quantity" id="quantity" step="0.00000001" min="0.00000001" class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:border-primary-500" placeholder="0.00000000" required oninput="calculateTotal()">
                    </div>
                    
                    <!-- Limit Price (for limit orders) -->
                    <div class="mb-4 hidden" id="limitPriceField">
                        <label class="block text-gray-400 text-sm font-semibold mb-2">Limit Price</label>
                        <input type="number" name="limit_price" id="limitPrice" step="0.01" min="0.01" class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:border-primary-500" placeholder="0.00">
                    </div>
                    
                    <!-- Stop Price (for stop-loss orders) -->
                    <div class="mb-4 hidden" id="stopPriceField">
                        <label class="block text-gray-400 text-sm font-semibold mb-2">Stop Price</label>
                        <input type="number" name="stop_price" id="stopPrice" step="0.01" min="0.01" class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:border-primary-500" placeholder="0.00">
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4 mb-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Price</span>
                            <span class="text-white font-semibold" id="displayPrice">${{ number_format($crypto->price_usd, 2) }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Fee ({{ $settings->crypto_trade_fee_percent ?? 0.25 }}%)</span>
                            <span class="text-white" id="displayFee">$0.00</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-700 pt-2">
                            <span class="text-gray-400 font-semibold">Total</span>
                            <span class="text-white font-bold" id="displayTotal">$0.00</span>
                        </div>
                    </div>
                    
                    <div class="text-sm text-gray-400 mb-4">
                        <p>Available Balance: <span class="text-white font-semibold">${{ number_format($availableBalance, 2) }}</span></p>
                    </div>
                    
                    <button type="submit" class="w-full py-3 rounded-lg font-bold transition-all duration-200 bg-green-500 hover:bg-green-600 text-white" id="submitBtn">
                        Buy {{ $crypto->coin_symbol }}
                    </button>
                </form>
            </div>
            
            <!-- Position Info -->
            @if($currentPosition > 0)
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl mt-4">
                <h3 class="text-lg font-bold text-white mb-3">Your Position</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Holdings</span>
                        <span class="text-white font-semibold">{{ number_format($currentPosition, 8) }} {{ $crypto->coin_symbol }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Avg Cost</span>
                        <span class="text-white">${{ number_format($averageCost, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Current Value</span>
                        <span class="text-white">${{ number_format($currentPosition * $crypto->price_usd, 2) }}</span>
                    </div>
                    @php
                        $positionPL = ($currentPosition * $crypto->price_usd) - ($currentPosition * $averageCost);
                    @endphp
                    <div class="flex justify-between pt-2 border-t border-gray-700">
                        <span class="text-gray-400">P/L</span>
                        <span class="font-semibold {{ $positionPL >= 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ $positionPL >= 0 ? '+' : '' }}${{ number_format($positionPL, 2) }}
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Chart & Market Data -->
        <div class="lg:col-span-2 space-y-6">
            <!-- TradingView Chart -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
                <h2 class="text-xl font-bold text-white mb-4">{{ $crypto->name }} Chart</h2>
                <div class="tradingview-widget-container" style="height: 500px;">
                    <div id="tradingview_chart" style="height: 100%;"></div>
                </div>
            </div>
            
            <!-- Pending Orders -->
            @if($pendingOrders->isNotEmpty())
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
                <h2 class="text-xl font-bold text-white mb-4">Pending Orders</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left text-gray-400 text-sm font-semibold py-2">Type</th>
                                <th class="text-left text-gray-400 text-sm font-semibold py-2">Order</th>
                                <th class="text-right text-gray-400 text-sm font-semibold py-2">Quantity</th>
                                <th class="text-right text-gray-400 text-sm font-semibold py-2">Target Price</th>
                                <th class="text-right text-gray-400 text-sm font-semibold py-2">Total</th>
                                <th class="text-center text-gray-400 text-sm font-semibold py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingOrders as $order)
                            <tr class="border-b border-gray-800">
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $order->trade_type === 'buy' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                        {{ strtoupper($order->trade_type) }}
                                    </span>
                                </td>
                                <td class="py-3 text-white">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</td>
                                <td class="py-3 text-right text-white">{{ number_format($order->quantity, 8) }}</td>
                                <td class="py-3 text-right text-white">${{ number_format($order->limit_price ?? $order->stop_price, 2) }}</td>
                                <td class="py-3 text-right text-white">${{ number_format($order->net_amount, 2) }}</td>
                                <td class="py-3 text-center">
                                    <form action="{{ route('crypto-trading.order.cancel', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-3 py-1 rounded text-xs font-semibold transition-colors duration-150">
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
            
            <!-- Recent Market Trades -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
                <h2 class="text-xl font-bold text-white mb-4">Recent Market Trades</h2>
                @if($marketTrades->isEmpty())
                <p class="text-gray-400 text-center py-4">No recent trades</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left text-gray-400 text-sm font-semibold py-2">Type</th>
                                <th class="text-right text-gray-400 text-sm font-semibold py-2">Quantity</th>
                                <th class="text-right text-gray-400 text-sm font-semibold py-2">Price</th>
                                <th class="text-right text-gray-400 text-sm font-semibold py-2">Total</th>
                                <th class="text-left text-gray-400 text-sm font-semibold py-2">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($marketTrades as $trade)
                            <tr class="border-b border-gray-800">
                                <td class="py-2">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $trade->trade_type === 'buy' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                        {{ strtoupper($trade->trade_type) }}
                                    </span>
                                </td>
                                <td class="py-2 text-right text-white">{{ number_format($trade->quantity, 8) }}</td>
                                <td class="py-2 text-right text-white">${{ number_format($trade->price_usd, 2) }}</td>
                                <td class="py-2 text-right text-white">${{ number_format($trade->total_amount, 2) }}</td>
                                <td class="py-2 text-gray-400 text-sm">{{ $trade->completed_at->diffForHumans() }}</td>
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
@endsection

@push('scripts')
<!-- TradingView Widget -->
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script type="text/javascript">
// Initialize TradingView Chart
new TradingView.widget({
    "width": "100%",
    "height": 500,
    "symbol": "BINANCE:{{ $crypto->coin_symbol }}USDT",
    "interval": "D",
    "timezone": "Etc/UTC",
    "theme": "dark",
    "style": "1",
    "locale": "en",
    "toolbar_bg": "#1E2430",
    "enable_publishing": false,
    "backgroundColor": "#1E2430",
    "gridColor": "#2A303C",
    "allow_symbol_change": false,
    "container_id": "tradingview_chart",
    "hide_side_toolbar": false,
    "studies": [
        "MASimple@tv-basicstudies",
        "RSI@tv-basicstudies"
    ],
    "show_popup_button": true,
    "popup_width": "1000",
    "popup_height": "650"
});
</script>

<script>
const cryptoPrice = {{ $crypto->price_usd }};
const feePercent = {{ $settings->crypto_trade_fee_percent ?? 0.25 }};
const cryptoSymbol = '{{ $crypto->coin_symbol }}';

function setTradeType(type) {
    document.getElementById('tradeType').value = type;
    
    const buyTab = document.getElementById('buyTab');
    const sellTab = document.getElementById('sellTab');
    const submitBtn = document.getElementById('submitBtn');
    
    if (type === 'buy') {
        buyTab.classList.remove('bg-gray-700', 'text-gray-400');
        buyTab.classList.add('bg-green-500', 'text-white');
        sellTab.classList.remove('bg-red-500', 'text-white');
        sellTab.classList.add('bg-gray-700', 'text-gray-400');
        
        submitBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
        submitBtn.classList.add('bg-green-500', 'hover:bg-green-600');
        submitBtn.textContent = 'Buy ' + cryptoSymbol;
    } else {
        sellTab.classList.remove('bg-gray-700', 'text-gray-400');
        sellTab.classList.add('bg-red-500', 'text-white');
        buyTab.classList.remove('bg-green-500', 'text-white');
        buyTab.classList.add('bg-gray-700', 'text-gray-400');
        
        submitBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
        submitBtn.classList.add('bg-red-500', 'hover:bg-red-600');
        submitBtn.textContent = 'Sell ' + cryptoSymbol;
    }
    
    calculateTotal();
}

function handleOrderTypeChange() {
    const orderType = document.getElementById('orderType').value;
    const limitPriceField = document.getElementById('limitPriceField');
    const stopPriceField = document.getElementById('stopPriceField');
    
    limitPriceField.classList.add('hidden');
    stopPriceField.classList.add('hidden');
    
    if (orderType === 'limit') {
        limitPriceField.classList.remove('hidden');
    } else if (orderType === 'stop_loss') {
        stopPriceField.classList.remove('hidden');
    }
    
    calculateTotal();
}

function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const total = quantity * cryptoPrice;
    const fee = (total * feePercent) / 100;
    const netTotal = total + fee;
    
    document.getElementById('displayFee').textContent = '$' + fee.toFixed(2);
    document.getElementById('displayTotal').textContent = '$' + netTotal.toFixed(2);
}

// Trade confirmation
document.getElementById('tradeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const tradeType = document.getElementById('tradeType').value;
    const quantity = document.getElementById('quantity').value;
    const orderType = document.getElementById('orderType').value;
    const total = document.getElementById('displayTotal').textContent;
    
    const orderTypeText = orderType === 'market' ? 'Market' : (orderType === 'limit' ? 'Limit' : 'Stop-Loss');
    
    swal({
        title: tradeType.toUpperCase() + ' ' + cryptoSymbol,
        text: `Order Type: ${orderTypeText}\nQuantity: ${quantity} ${cryptoSymbol}\nTotal: ${total}\n\nConfirm this trade?`,
        icon: 'warning',
        buttons: {
            cancel: {
                text: 'Cancel',
                value: null,
                visible: true,
                className: 'bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded',
                closeModal: true
            },
            confirm: {
                text: 'Confirm ' + tradeType.toUpperCase(),
                value: true,
                visible: true,
                className: tradeType === 'buy' ? 'bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded' : 'bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded',
                closeModal: true
            }
        }
    }).then((confirm) => {
        if (confirm) {
            this.submit();
        }
    });
});

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
