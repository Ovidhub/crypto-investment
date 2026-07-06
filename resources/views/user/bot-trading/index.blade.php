@extends('layouts.dash')

@section('content')
    <div class="relative min-h-screen dark:bg-gradient-to-br dark:from-dark dark:via-dark-50 dark:to-dark bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-16">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-primary/5 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-secondary/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <!-- Hero Header Section -->
            <div class="mb-10">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center shadow-lg flex-shrink-0">
                            <i data-lucide="bot" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold bg-gradient-to-r from-primary via-secondary to-primary bg-clip-text text-transparent">
                                Trading Bots
                            </h1>
                            <p class="text-xs sm:text-sm dark:text-gray-400 text-gray-600 mt-1">
                                <i data-lucide="zap" class="w-3 h-3 inline mr-1"></i>
                                <span class="hidden sm:inline">Automated trading • 24/7 monitoring • AI-powered strategies</span>
                                <span class="sm:hidden">Automated trading • 24/7</span>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('user.bot-trading.subscriptions') }}" class="flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm sm:text-base bg-secondary hover:bg-secondary/90 text-white transition-colors shadow-md whitespace-nowrap flex-shrink-0">
                        <i data-lucide="list" class="w-4 h-4"></i>
                        <span>My Subscriptions</span>
                    </a>
                </div>

                <!-- Stats Banner -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="rounded-lg p-4 bg-gray-800 border border-gray-700 hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                <i data-lucide="cpu" class="w-4 h-4 text-primary"></i>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">Active Bots</span>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ count($bots) }}</p>
                    </div>
                    <div class="rounded-lg p-4 bg-gray-800 border border-gray-700 hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-4 h-4 text-secondary"></i>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">Avg ROI</span>
                        </div>
                        <p class="text-2xl font-bold text-secondary">{{ $bots->avg('roi_percentage') ? number_format($bots->avg('roi_percentage'), 1) : '0' }}%</p>
                    </div>
                    <div class="rounded-lg p-4 bg-gray-800 border border-gray-700 hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                <i data-lucide="target" class="w-4 h-4 text-blue-500"></i>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">Win Rate</span>
                        </div>
                        <p class="text-2xl font-bold text-blue-400">{{ $bots->avg('win_rate') ? number_format($bots->avg('win_rate'), 0) : '0' }}%</p>
                    </div>
                    <div class="rounded-lg p-4 bg-gray-800 border border-gray-700 hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                <i data-lucide="users" class="w-4 h-4 text-primary"></i>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">Subscribers</span>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ $bots->sum(function($bot) { return ($bot->active_subscriptions_count ?? 0) + ($bot->fake_active_users ?? 0); }) }}</p>
                    </div>
                </div>
            </div>

            <!-- Feedback Messages -->
            <x-danger-alert />
            <x-success-alert />

            <!-- Search & Filter Bar -->
            <!-- Search & Filter Bar -->
            <div class="mb-8 flex flex-col gap-4">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="botSearch" 
                        placeholder="Search bots..." 
                        class="w-full px-4 sm:px-5 py-3 sm:py-4 pl-10 sm:pl-14 rounded-xl sm:rounded-2xl border border-gray-200/50 dark:border-dark-200/50 bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl text-dark dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary/50 transition-all shadow-sm hover:shadow-md text-sm sm:text-base"
                    >
                    <i data-lucide="search" class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400"></i>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button onclick="filterByStrategy('all')" class="filter-btn active px-3 sm:px-4 md:px-5 py-2 sm:py-3 md:py-4 rounded-lg sm:rounded-xl md:rounded-2xl font-medium text-xs sm:text-sm transition-all bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl border border-gray-200/50 dark:border-dark-200/50 hover:shadow-md whitespace-nowrap" data-filter="all">
                        All
                    </button>
                    <button onclick="filterByStrategy('Scalping')" class="filter-btn px-3 sm:px-4 md:px-5 py-2 sm:py-3 md:py-4 rounded-lg sm:rounded-xl md:rounded-2xl font-medium text-xs sm:text-sm transition-all bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl border border-gray-200/50 dark:border-dark-200/50 hover:shadow-md whitespace-nowrap" data-filter="Scalping">
                        Scalping
                    </button>
                    <button onclick="filterByStrategy('Day Trading')" class="filter-btn px-3 sm:px-4 md:px-5 py-2 sm:py-3 md:py-4 rounded-lg sm:rounded-xl md:rounded-2xl font-medium text-xs sm:text-sm transition-all bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl border border-gray-200/50 dark:border-dark-200/50 hover:shadow-md whitespace-nowrap" data-filter="Day Trading">
                        Day Trading
                    </button>
                    <button onclick="filterByStrategy('Swing Trading')" class="filter-btn px-3 sm:px-4 md:px-5 py-2 sm:py-3 md:py-4 rounded-lg sm:rounded-xl md:rounded-2xl font-medium text-xs sm:text-sm transition-all bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl border border-gray-200/50 dark:border-dark-200/50 hover:shadow-md whitespace-nowrap" data-filter="Swing Trading">
                        Swing Trading
                    </button>
                </div>
            </div>

            <!-- Trading Bots Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="botsGrid">
                @forelse ($bots as $bot)
                <div class="bot-card group rounded-2xl overflow-hidden bg-white dark:bg-dark-50 border border-gray-200 dark:border-dark-200 hover:border-primary dark:hover:border-primary shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1" data-strategy="{{ $bot->strategy_type }}">
                    
                    <!-- Auto Trade Badge -->
                    @if($bot->auto_trade == 'on')
                    <div class="absolute top-4 right-4 z-10 px-2.5 py-1 rounded-lg bg-green-600 flex items-center gap-1.5 shadow-md">
                        <i data-lucide="zap" class="w-3.5 h-3.5 text-white"></i>
                        <span class="text-xs font-semibold text-white">Live</span>
                    </div>
                    @endif

                    <!-- Bot Header -->
                    <div class="relative p-6 bg-gray-50 dark:bg-dark-100 border-b border-gray-200 dark:border-dark-200">
                        <div class="relative flex items-start gap-4">
                            <div class="relative flex-shrink-0">
                                @if ($bot->photo)
                                    <img src="{{ asset('storage/app/public/' . $bot->photo) }}" 
                                         alt="{{ $bot->name }}" 
                                         class="w-16 h-16 rounded-xl object-cover border-2 border-gray-200 dark:border-dark-200 shadow-sm">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-primary flex items-center justify-center text-white text-xl font-bold border-2 border-gray-200 dark:border-dark-200 shadow-sm">
                                        <i data-lucide="bot" class="w-8 h-8"></i>
                                    </div>
                                @endif
                                <!-- Active Indicator -->
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-dark-50 animate-pulse"></div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-lg dark:text-white text-dark bot-name truncate mb-2">
                                    {{ $bot->name }}
                                </h3>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="flex items-center gap-1.5 text-xs dark:text-gray-400 text-gray-600">
                                        <i data-lucide="users-2" class="w-3.5 h-3.5"></i>
                                        <span class="font-medium">{{ ($bot->active_subscriptions_count ?? 0) + ($bot->fake_active_users ?? 0) }} active</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs dark:text-gray-400 text-gray-600">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        <span class="font-medium">{{ $bot->trade_interval }}</span>
                                    </div>
                                </div>
                                <div class="inline-flex px-2.5 py-1 rounded-md text-xs font-semibold bg-primary/10 text-primary dark:bg-primary/20 dark:text-primary">
                                    {{ $bot->strategy_type }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bot Stats -->
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-4 mb-5">
                            <div class="text-center">
                                <div class="flex items-center justify-center gap-1 mb-1">
                                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-secondary"></i>
                                    <span class="text-xs dark:text-gray-400 text-gray-600">ROI</span>
                                </div>
                                <p class="text-lg font-bold text-secondary dark:text-secondary">{{ number_format($bot->roi_percentage, 1) }}%</p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center gap-1 mb-1">
                                    <i data-lucide="target" class="w-3.5 h-3.5 text-primary"></i>
                                    <span class="text-xs dark:text-gray-400 text-gray-600">Win Rate</span>
                                </div>
                                <p class="text-lg font-bold text-primary dark:text-primary">{{ number_format($bot->win_rate, 0) }}%</p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center gap-1 mb-1">
                                    <i data-lucide="activity" class="w-3.5 h-3.5 text-purple-500"></i>
                                    <span class="text-xs dark:text-gray-400 text-gray-600">Trades</span>
                                </div>
                                <p class="text-lg font-bold dark:text-white text-dark">{{ number_format($bot->total_trades) }}</p>
                            </div>
                        </div>

                        <div class="space-y-2 mb-5">
                            <div class="text-sm dark:text-gray-300 text-gray-700 line-clamp-2">
                                {{ $bot->description }}
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="dark:text-gray-400 text-gray-600">
                                    <i data-lucide="dollar-sign" class="w-3 h-3 inline"></i>
                                    ${{ number_format($bot->min_capital) }} - ${{ number_format($bot->max_capital) }}
                                </span>
                                <span class="font-semibold {{ $bot->total_profit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $bot->total_profit >= 0 ? '+' : '' }}${{ number_format($bot->total_profit, 2) }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('user.bot-trading.show', $bot) }}" 
                           class="block w-full text-center px-4 py-3 rounded-xl font-semibold text-white bg-primary hover:bg-primary/90 transition-all shadow-md hover:shadow-lg">
                            <div class="flex items-center justify-center gap-2">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                <span>Subscribe Now</span>
                            </div>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-16">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-dark-100 flex items-center justify-center">
                        <i data-lucide="bot" class="w-10 h-10 text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold dark:text-white text-dark mb-2">No Trading Bots Available</h3>
                    <p class="text-sm dark:text-gray-400 text-gray-600">Check back later for available bots</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Bot search functionality
        document.getElementById('botSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const botCards = document.querySelectorAll('.bot-card');
            
            botCards.forEach(card => {
                const name = card.querySelector('.bot-name').textContent.toLowerCase();
                const strategy = card.dataset.strategy.toLowerCase();
                
                if (name.includes(searchTerm) || strategy.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Strategy filter
        function filterByStrategy(strategy) {
            const botCards = document.querySelectorAll('.bot-card');
            const filterBtns = document.querySelectorAll('.filter-btn');
            
            filterBtns.forEach(btn => btn.classList.remove('active', 'bg-primary', 'text-white'));
            event.target.classList.add('active', 'bg-primary', 'text-white');
            
            botCards.forEach(card => {
                if (strategy === 'all' || card.dataset.strategy === strategy) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
    @endpush
@endsection
