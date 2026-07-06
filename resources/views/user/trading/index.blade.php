@extends('layouts.dash')
@section('title', 'Trading Platform')

@section('styles')
<style>
    .market-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .market-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .crypto-positive {
        color: #4A9D7F;
    }
    .crypto-negative {
        color: #FF6B6B;
    }
    .category-btn.active {
        background-color: theme('colors.primary.DEFAULT');
        color: white;
    }
</style>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold dark:text-white text-dark">Trading Platform</h1>
        <p class="text-sm dark:text-gray-400 text-gray-600 mt-1">Trade cryptocurrencies and stocks in real-time</p>
    </div>

    <!-- Trading Categories Tabs -->
    <div class="flex items-center space-x-4 mb-6 overflow-x-auto pb-2">
        <button class="category-btn active px-4 py-2 rounded-lg text-sm font-medium bg-primary text-white focus:outline-none" data-category="all">
            All
        </button>
        <button class="category-btn px-4 py-2 rounded-lg text-sm font-medium dark:bg-dark-100 bg-light-200 dark:text-gray-300 text-gray-700 hover:bg-primary/10 focus:outline-none" data-category="crypto">
            Crypto
        </button>
        <button class="category-btn px-4 py-2 rounded-lg text-sm font-medium dark:bg-dark-100 bg-light-200 dark:text-gray-300 text-gray-700 hover:bg-primary/10 focus:outline-none" data-category="stocks">
            Stocks
        </button>
        <button class="category-btn px-4 py-2 rounded-lg text-sm font-medium dark:bg-dark-100 bg-light-200 dark:text-gray-300 text-gray-700 hover:bg-primary/10 focus:outline-none" data-category="watchlist">
            My Watchlist
        </button>
    </div>

    <!-- Quick Trading Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <a href="{{ route('trading.crypto') }}" class="market-card dark:bg-dark-100 bg-light-200 rounded-xl p-6 flex flex-col h-full border dark:border-dark-200 border-light-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-tertiary/20 flex items-center justify-center mr-4">
                        <i data-lucide="bitcoin" class="h-6 w-6 text-tertiary"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold dark:text-white text-dark">Crypto Trading</h3>
                        <p class="text-sm dark:text-gray-400 text-gray-600">Trade major cryptocurrencies</p>
                    </div>
                </div>
                <div class="text-primary">
                    <i data-lucide="arrow-right" class="h-5 w-5"></i>
                </div>
            </div>
            <div class="mt-auto grid grid-cols-3 gap-4">
                <div class="p-2 rounded dark:bg-dark-50 bg-light-100 text-center">
                    <p class="text-xs dark:text-gray-400 text-gray-600">BTC/USDT</p>
                </div>
                <div class="p-2 rounded dark:bg-dark-50 bg-light-100 text-center">
                    <p class="text-xs dark:text-gray-400 text-gray-600">ETH/USDT</p>
                </div>
                <div class="p-2 rounded dark:bg-dark-50 bg-light-100 text-center">
                    <p class="text-xs dark:text-gray-400 text-gray-600">BNB/USDT</p>
                </div>
            </div>
        </a>

        <a href="{{ route('trading.stocks') }}" class="market-card dark:bg-dark-100 bg-light-200 rounded-xl p-6 flex flex-col h-full border dark:border-dark-200 border-light-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-accent/20 flex items-center justify-center mr-4">
                        <i data-lucide="trending-up" class="h-6 w-6 text-accent"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold dark:text-white text-dark">Stock Trading</h3>
                        <p class="text-sm dark:text-gray-400 text-gray-600">Trade major stocks</p>
                    </div>
                </div>
                <div class="text-primary">
                    <i data-lucide="arrow-right" class="h-5 w-5"></i>
                </div>
            </div>
            <div class="mt-auto grid grid-cols-3 gap-4">
                <div class="p-2 rounded dark:bg-dark-50 bg-light-100 text-center">
                    <p class="text-xs dark:text-gray-400 text-gray-600">AAPL</p>
                </div>
                <div class="p-2 rounded dark:bg-dark-50 bg-light-100 text-center">
                    <p class="text-xs dark:text-gray-400 text-gray-600">MSFT</p>
                </div>
                <div class="p-2 rounded dark:bg-dark-50 bg-light-100 text-center">
                    <p class="text-xs dark:text-gray-400 text-gray-600">GOOG</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Market Overview -->
    <div class="mb-8 dark:bg-dark-100 bg-light-200 rounded-xl p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold dark:text-white text-dark">Market Overview</h2>
            <div class="text-sm">
                <span class="text-gray-500">Last updated: </span>
                <span class="dark:text-white text-dark" id="lastUpdated">{{ now()->format('H:i:s') }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y dark:divide-dark-200 divide-light-300">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">24h Change</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Market Cap</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="dark:bg-dark-100 bg-light-200 divide-y dark:divide-dark-200 divide-light-300" id="marketOverviewBody">
                    @foreach ($topCryptos as $index => $crypto)
                    <tr class="{{ $index % 2 == 0 ? 'dark:bg-dark-100 bg-light-200' : 'dark:bg-dark-50 bg-light-100' }}">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <img src="{{ $crypto['imageUrl'] }}" alt="{{ $crypto['symbol'] }}" class="w-6 h-6 rounded-full mr-3">
                                <div>
                                    <div class="text-sm font-medium dark:text-white text-dark">{{ $crypto['symbol'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $crypto['name'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <div class="text-sm font-medium dark:text-white text-dark">{{ $crypto['priceFormatted'] }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <div class="text-sm font-medium {{ $crypto['change24h'] >= 0 ? 'crypto-positive' : 'crypto-negative' }}">
                                {{ $crypto['change24h'] >= 0 ? '+' : '' }}{{ $crypto['change24hFormatted'] }}%
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <div class="text-sm dark:text-gray-300 text-gray-700">{{ $crypto['marketCapFormatted'] }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <a href="{{ route('trading.crypto', ['symbol' => $crypto['symbol'] . 'USDT']) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-primary hover:bg-primary-600 focus:outline-none">
                                Trade
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Watchlists Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold dark:text-white text-dark">Your Watchlists</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Crypto Watchlist -->
            <div class="dark:bg-dark-100 bg-light-200 rounded-xl p-4 border dark:border-dark-200 border-light-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold dark:text-white text-dark">
                        <i data-lucide="bitcoin" class="inline h-5 w-5 mr-2 text-tertiary"></i>
                        Crypto Watchlist
                    </h3>
                    <a href="{{ route('trading.crypto') }}" class="text-sm text-primary hover:text-primary-600">
                        View All
                    </a>
                </div>

                <div class="overflow-y-auto max-h-60">
                    <table class="w-full divide-y dark:divide-dark-200 divide-light-300">
                        <thead class="dark:bg-dark-50 bg-light-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-dark-200 divide-light-300">
                            @forelse ($cryptoWatchlist->items as $item)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium dark:text-white text-dark">{{ $item->formatted_symbol }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-right">
                                    <div class="text-sm dark:text-gray-300 text-gray-700">-</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-right">
                                    <a href="{{ route('trading.crypto', ['symbol' => $item->symbol]) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-primary hover:text-primary-600 focus:outline-none">
                                        Trade
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">
                                    No items in watchlist
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stock Watchlist -->
            <div class="dark:bg-dark-100 bg-light-200 rounded-xl p-4 border dark:border-dark-200 border-light-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold dark:text-white text-dark">
                        <i data-lucide="trending-up" class="inline h-5 w-5 mr-2 text-accent"></i>
                        Stock Watchlist
                    </h3>
                    <a href="{{ route('trading.stocks') }}" class="text-sm text-primary hover:text-primary-600">
                        View All
                    </a>
                </div>

                <div class="overflow-y-auto max-h-60">
                    <table class="w-full divide-y dark:divide-dark-200 divide-light-300">
                        <thead class="dark:bg-dark-50 bg-light-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-dark-200 divide-light-300">
                            @forelse ($stockWatchlist->items as $item)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium dark:text-white text-dark">{{ $item->symbol }}</div>
                                    @if ($item->market)
                                    <div class="text-xs text-gray-500">{{ $item->market }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-right">
                                    <div class="text-sm dark:text-gray-300 text-gray-700">-</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-right">
                                    <a href="{{ route('trading.stocks', ['symbol' => $item->symbol]) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-primary hover:text-primary-600 focus:outline-none">
                                        Trade
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">
                                    No items in watchlist
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryButtons = document.querySelectorAll('.category-btn');

        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                categoryButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-primary', 'text-white');
                    btn.classList.add('dark:bg-dark-100', 'bg-light-200', 'dark:text-gray-300', 'text-gray-700');
                });

                // Add active class to clicked button
                this.classList.add('active', 'bg-primary', 'text-white');
                this.classList.remove('dark:bg-dark-100', 'bg-light-200', 'dark:text-gray-300', 'text-gray-700');

                // TODO: Filter content based on category
                const category = this.dataset.category;
                console.log(`Filtering for category: ${category}`);
            });
        });

        // Auto-refresh market data every 60 seconds
        setInterval(function() {
            // This would be replaced with real API calls in production
            const now = new Date();
            document.getElementById('lastUpdated').textContent =
                now.getHours().toString().padStart(2, '0') + ':' +
                now.getMinutes().toString().padStart(2, '0') + ':' +
                now.getSeconds().toString().padStart(2, '0');
        }, 60000);
    });
</script>
@endsection
