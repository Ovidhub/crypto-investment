@extends('layouts.dash')

@section('title', 'Crypto Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div class="min-w-0 flex-1">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">Crypto Orders</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">View and manage your cryptocurrency orders</p>
        </div>
        <a href="{{ route('crypto-trading.index') }}" class="bg-primary-500 hover:bg-primary-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm sm:text-base flex items-center space-x-2 transition-colors duration-150 whitespace-nowrap flex-shrink-0">
            <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            <span>Back to Trading</span>
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

    <!-- Order Tabs -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
        <div class="flex border-b border-gray-700">
            <button onclick="showTab('active')" id="activeTab" class="flex-1 px-6 py-4 text-white font-semibold border-b-2 border-primary-500 bg-gray-800/50">
                <div class="flex items-center justify-center space-x-2">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                    <span>Active Orders ({{ $activeOrders->count() }})</span>
                </div>
            </button>
            <button onclick="showTab('completed')" id="completedTab" class="flex-1 px-6 py-4 text-gray-400 font-semibold hover:bg-gray-800/30 transition-colors duration-150">
                <div class="flex items-center justify-center space-x-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span>Completed ({{ $completedOrders->count() }})</span>
                </div>
            </button>
            <button onclick="showTab('cancelled')" id="cancelledTab" class="flex-1 px-6 py-4 text-gray-400 font-semibold hover:bg-gray-800/30 transition-colors duration-150">
                <div class="flex items-center justify-center space-x-2">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                    <span>Cancelled ({{ $cancelledOrders->count() }})</span>
                </div>
            </button>
        </div>

        <!-- Active Orders Content -->
        <div id="activeContent" class="p-5">
            @if($activeOrders->isEmpty())
            <div class="text-center py-12">
                <i data-lucide="inbox" class="w-16 h-16 text-gray-600 mx-auto mb-4"></i>
                <p class="text-gray-400 text-lg mb-2">No active orders</p>
                <p class="text-gray-500 text-sm">All your pending orders will appear here</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Cryptocurrency</th>
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Type</th>
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Order Type</th>
                            <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Quantity</th>
                            <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Target Price</th>
                            <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Total</th>
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Date</th>
                            <th class="text-center text-gray-400 text-sm font-semibold py-3 px-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeOrders as $order)
                        <tr class="border-b border-gray-800 hover:bg-gray-800/30 transition-colors duration-150">
                            <td class="py-4 px-3">
                                <div class="flex items-center space-x-2">
                                    @if($order->cryptoPrice && $order->cryptoPrice->logo)
                                    <img src="{{ $order->cryptoPrice->logo }}" alt="{{ $order->coin_symbol }}" class="w-8 h-8 rounded-full">
                                    @else
                                    <div class="w-8 h-8 rounded-full bg-primary-500/10 flex items-center justify-center">
                                        <i data-lucide="bitcoin" class="w-5 h-5 text-primary-500"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-white font-semibold">{{ $order->coin_symbol }}</p>
                                        @if($order->cryptoPrice)
                                        <p class="text-gray-400 text-xs">{{ $order->cryptoPrice->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $order->trade_type === 'buy' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                    {{ strtoupper($order->trade_type) }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-yellow-500/10 text-yellow-400">
                                    {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                                </span>
                            </td>
                            <td class="py-4 px-3 text-right text-white">{{ number_format($order->quantity, 8) }}</td>
                            <td class="py-4 px-3 text-right text-white">${{ number_format($order->limit_price ?? $order->stop_price, 2) }}</td>
                            <td class="py-4 px-3 text-right text-white font-semibold">${{ number_format($order->net_amount, 2) }}</td>
                            <td class="py-4 px-3 text-gray-400 text-sm">{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td class="py-4 px-3 text-center">
                                <form action="{{ route('crypto-trading.order.cancel', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    @csrf
                                    <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-3 py-1 rounded-lg text-sm font-semibold transition-colors duration-150">
                                        Cancel
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $activeOrders->links() }}
            </div>
            @endif
        </div>

        <!-- Completed Orders Content -->
        <div id="completedContent" class="p-5 hidden">
            @if($completedOrders->isEmpty())
            <div class="text-center py-12">
                <i data-lucide="inbox" class="w-16 h-16 text-gray-600 mx-auto mb-4"></i>
                <p class="text-gray-400 text-lg mb-2">No completed orders</p>
                <p class="text-gray-500 text-sm">Your completed trades will appear here</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Cryptocurrency</th>
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Type</th>
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Order Type</th>
                            <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Quantity</th>
                            <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Price</th>
                            <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Total</th>
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Completed Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedOrders as $order)
                        <tr class="border-b border-gray-800 hover:bg-gray-800/30 transition-colors duration-150">
                            <td class="py-4 px-3">
                                <div class="flex items-center space-x-2">
                                    @if($order->cryptoPrice && $order->cryptoPrice->logo)
                                    <img src="{{ $order->cryptoPrice->logo }}" alt="{{ $order->coin_symbol }}" class="w-8 h-8 rounded-full">
                                    @else
                                    <div class="w-8 h-8 rounded-full bg-primary-500/10 flex items-center justify-center">
                                        <i data-lucide="bitcoin" class="w-5 h-5 text-primary-500"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-white font-semibold">{{ $order->coin_symbol }}</p>
                                        @if($order->cryptoPrice)
                                        <p class="text-gray-400 text-xs">{{ $order->cryptoPrice->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $order->trade_type === 'buy' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                    {{ strtoupper($order->trade_type) }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-500/10 text-blue-400">
                                    {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                                </span>
                            </td>
                            <td class="py-4 px-3 text-right text-white">{{ number_format($order->quantity, 8) }}</td>
                            <td class="py-4 px-3 text-right text-white">${{ number_format($order->price_usd, 2) }}</td>
                            <td class="py-4 px-3 text-right text-white font-semibold">${{ number_format($order->net_amount, 2) }}</td>
                            <td class="py-4 px-3 text-gray-400 text-sm">{{ $order->completed_at ? $order->completed_at->format('M d, Y H:i') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $completedOrders->links() }}
            </div>
            @endif
        </div>

        <!-- Cancelled Orders Content -->
        <div id="cancelledContent" class="p-5 hidden">
            @if($cancelledOrders->isEmpty())
            <div class="text-center py-12">
                <i data-lucide="inbox" class="w-16 h-16 text-gray-600 mx-auto mb-4"></i>
                <p class="text-gray-400 text-lg mb-2">No cancelled orders</p>
                <p class="text-gray-500 text-sm">Cancelled orders will appear here</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Cryptocurrency</th>
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Type</th>
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Order Type</th>
                            <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Quantity</th>
                            <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Target Price</th>
                            <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Total</th>
                            <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Cancelled Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cancelledOrders as $order)
                        <tr class="border-b border-gray-800 hover:bg-gray-800/30 transition-colors duration-150">
                            <td class="py-4 px-3">
                                <div class="flex items-center space-x-2">
                                    @if($order->cryptoPrice && $order->cryptoPrice->logo)
                                    <img src="{{ $order->cryptoPrice->logo }}" alt="{{ $order->coin_symbol }}" class="w-8 h-8 rounded-full">
                                    @else
                                    <div class="w-8 h-8 rounded-full bg-primary-500/10 flex items-center justify-center">
                                        <i data-lucide="bitcoin" class="w-5 h-5 text-primary-500"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-white font-semibold">{{ $order->coin_symbol }}</p>
                                        @if($order->cryptoPrice)
                                        <p class="text-gray-400 text-xs">{{ $order->cryptoPrice->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-500/10 text-gray-400">
                                    {{ strtoupper($order->trade_type) }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-500/10 text-gray-400">
                                    {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                                </span>
                            </td>
                            <td class="py-4 px-3 text-right text-gray-400">{{ number_format($order->quantity, 8) }}</td>
                            <td class="py-4 px-3 text-right text-gray-400">${{ number_format($order->limit_price ?? $order->stop_price, 2) }}</td>
                            <td class="py-4 px-3 text-right text-gray-400">${{ number_format($order->net_amount, 2) }}</td>
                            <td class="py-4 px-3 text-gray-400 text-sm">{{ $order->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $cancelledOrders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all content
    document.getElementById('activeContent').classList.add('hidden');
    document.getElementById('completedContent').classList.add('hidden');
    document.getElementById('cancelledContent').classList.add('hidden');
    
    // Reset all tabs
    document.getElementById('activeTab').classList.remove('border-primary-500', 'bg-gray-800/50', 'text-white');
    document.getElementById('activeTab').classList.add('text-gray-400');
    document.getElementById('completedTab').classList.remove('border-primary-500', 'bg-gray-800/50', 'text-white');
    document.getElementById('completedTab').classList.add('text-gray-400');
    document.getElementById('cancelledTab').classList.remove('border-primary-500', 'bg-gray-800/50', 'text-white');
    document.getElementById('cancelledTab').classList.add('text-gray-400');
    
    // Show selected content and highlight tab
    if (tabName === 'active') {
        document.getElementById('activeContent').classList.remove('hidden');
        document.getElementById('activeTab').classList.add('border-primary-500', 'bg-gray-800/50', 'text-white');
        document.getElementById('activeTab').classList.remove('text-gray-400');
    } else if (tabName === 'completed') {
        document.getElementById('completedContent').classList.remove('hidden');
        document.getElementById('completedTab').classList.add('border-primary-500', 'bg-gray-800/50', 'text-white');
        document.getElementById('completedTab').classList.remove('text-gray-400');
    } else if (tabName === 'cancelled') {
        document.getElementById('cancelledContent').classList.remove('hidden');
        document.getElementById('cancelledTab').classList.add('border-primary-500', 'bg-gray-800/50', 'text-white');
        document.getElementById('cancelledTab').classList.remove('text-gray-400');
    }
    
    // Reinitialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Initialize Lucide icons on page load
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection
