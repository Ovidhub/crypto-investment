@extends('layouts.dash')

@section('content')
    <div class="relative min-h-screen dark:bg-dark bg-light pb-16">
        <!-- Header Section -->
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold dark:text-white text-dark">Stock Investment Plans</h1>
                <p class="mt-2 text-sm dark:text-gray-400 text-gray-600">Invest in top global stocks with guaranteed returns</p>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <input 
                        type="text" 
                        id="stockSearch" 
                        placeholder="Search stocks by company name or symbol..." 
                        class="w-full px-4 py-3 pl-12 rounded-xl border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-50 text-dark dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                    >
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                </div>
            </div>

            <!-- Feedback Messages -->
            @if (session('success'))
            <div class="mb-6 rounded-xl p-4 bg-green-500/10 border border-green-500/30 text-green-600 dark:text-green-400">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if (session('error'))
            <div class="mb-6 rounded-xl p-4 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-6 rounded-xl p-4 bg-yellow-500/10 border border-yellow-500/30 text-yellow-600 dark:text-yellow-400">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Stock Plans Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($stockPlans as $plan)
                <div class="rounded-xl overflow-hidden border border-light-200/50 dark:border-dark-200/50 bg-gradient-to-br from-white to-white/80 dark:from-dark-50 dark:to-dark-50/80 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <!-- TradingView Widget -->
                    <div class="p-4 bg-gradient-to-br from-green-50/80 to-green-50/50 dark:from-green-900/30 dark:to-green-900/20">
                        <x-tradingview-widget :symbol="$plan->stock_symbol" />
                    </div>

                    <!-- Company Header -->
                    <div class="p-5 bg-gradient-to-r from-green-600 to-green-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-xl text-white">{{ $plan->stock_company }}</h3>
                                <p class="text-sm text-green-100 mt-1 font-mono">{{ $plan->stock_symbol }}</p>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <i class="fas fa-chart-candlestick text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Details -->
                    <div class="p-5">
                        <!-- Plan Name -->
                        <h4 class="font-semibold text-lg dark:text-white text-dark mb-2">{{ $plan->name }}</h4>
                        
                        <!-- Description -->
                        @if ($plan->stock_description)
                        <p class="text-sm dark:text-gray-400 text-gray-600 mb-4 line-clamp-3">{{ $plan->stock_description }}</p>
                        @endif

                        <!-- Investment Range -->
                        <div class="grid grid-cols-2 gap-3 mb-4" data-search-visible="true">
                            <div class="rounded-lg px-3 py-2.5 bg-green-500/10 border border-green-500/20">
                                <p class="text-xs dark:text-gray-400 text-gray-600 mb-1 font-medium">Minimum</p>
                                <p class="text-base font-bold text-green-600 dark:text-green-400" data-min-price="{{ $plan->min_price }}">{{ $settings->currency }}{{ number_format($plan->min_price, 0) }}</p>
                            </div>
                            <div class="rounded-lg px-3 py-2.5 bg-green-500/10 border border-green-500/20">
                                <p class="text-xs dark:text-gray-400 text-gray-600 mb-1 font-medium">Maximum</p>
                                <p class="text-base font-bold text-green-600 dark:text-green-400" data-max-price="{{ $plan->max_price }}">{{ $settings->currency }}{{ number_format($plan->max_price, 0) }}</p>
                            </div>
                        </div>

                        <!-- ROI Details -->
                        <div class="rounded-lg p-3 mb-4 bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/20">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs dark:text-gray-400 text-gray-600">Expected Return</span>
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ $plan->expected_return }}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs dark:text-gray-400 text-gray-600">Duration</span>
                                <span class="text-sm font-medium dark:text-white text-dark">{{ $plan->expiration }}</span>
                            </div>
                        </div>

                        <!-- ROI Range -->
                        {{-- <div class="flex items-center gap-2 mb-4">
                            <div class="flex-1 rounded-lg px-2 py-1.5 bg-primary/10 text-center">
                                <p class="text-xs dark:text-gray-400 text-gray-600">Min ROI</p>
                                <p class="text-sm font-bold text-primary">{{ $plan->minr }}%</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-right dark:text-gray-600 text-gray-400"></i>
                            </div>
                            <div class="flex-1 rounded-lg px-2 py-1.5 bg-secondary/10 text-center">
                                <p class="text-xs dark:text-gray-400 text-gray-600">Max ROI</p>
                                <p class="text-sm font-bold text-secondary">{{ $plan->maxr }}%</p>
                            </div>
                        </div> --}}

                        <!-- Bonus/Gift -->
                        {{-- @if ($plan->gift > 0)
                        <div class="rounded-lg p-2 mb-4 bg-green-500/10 border border-green-500/20 flex items-center gap-2">
                            <i class="fas fa-gift text-green-600 dark:text-green-400"></i>
                            <span class="text-sm dark:text-gray-300 text-gray-700">Bonus: {{ $settings->currency }}{{ number_format($plan->gift) }}</span>
                        </div>
                        @endif --}}

                        <!-- Investment Form -->
                        <form action="{{ route('stockplans.purchase') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            
                            <div>
                                <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                                    Investment Amount
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 dark:text-gray-400 text-gray-600">{{ $settings->currency }}</span>
                                    <input 
                                        type="number" 
                                        name="amount"
                                        min="{{ $plan->min_price }}"
                                        max="{{ $plan->max_price }}"
                                        step="0.01"
                                        required
                                        class="w-full pl-10 pr-4 py-2 rounded-lg dark:bg-dark-100 bg-light-200 border border-light-200 dark:border-dark-200 dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary"
                                        placeholder="Enter amount"
                                    >
                                </div>
                            </div>

                            <button 
                                type="submit"
                                class="w-full py-3 rounded-lg bg-gradient-to-r from-green-600 to-green-600 hover:from-green-700 hover:to-green-700 text-white font-medium transition-all duration-200 flex items-center justify-center gap-2"
                            >
                                <i class="fas fa-shopping-cart"></i>
                                <span>Purchase Stock</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Empty State -->
            @if ($stockPlans->isEmpty())
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-dark-100 mb-4">
                    <i class="fas fa-chart-candlestick text-3xl dark:text-gray-600 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold dark:text-white text-dark mb-2">No Stock Plans Available</h3>
                <p class="text-sm dark:text-gray-400 text-gray-600">Check back later for new stock investment opportunities.</p>
            </div>
            @endif

            <!-- No Results Message -->
            <div id="noResults" class="hidden text-center py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-dark-100 mb-4">
                    <i class="fas fa-search text-3xl dark:text-gray-600 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold dark:text-white text-dark mb-2">No Stocks Found</h3>
                <p class="text-sm dark:text-gray-400 text-gray-600">Try adjusting your search terms.</p>
            </div>
        </div>
    </div>

    <!-- Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('stockSearch');
            const stockCards = document.querySelectorAll('.grid > div');
            const noResultsMessage = document.getElementById('noResults');
            const stockGrid = document.querySelector('.grid');

            console.log('Total stock cards:', stockCards.length);

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    stockCards.forEach((card, index) => {
                        // Get all text content from the card
                        const cardText = card.textContent.toLowerCase();

                        if (cardText.includes(searchTerm) || searchTerm === '') {
                            card.style.display = 'block';
                            card.style.visibility = 'visible';
                            card.style.opacity = '1';
                            visibleCount++;
                            console.log(`Card ${index} visible:`, card.querySelector('.text-xl.font-bold')?.textContent);
                        } else {
                            card.style.display = 'none';
                            card.style.visibility = 'hidden';
                            card.style.opacity = '0';
                        }
                    });

                    console.log('Search term:', searchTerm, 'Visible cards:', visibleCount);

                    // Show/hide no results message
                    if (visibleCount === 0 && searchTerm !== '') {
                        noResultsMessage.classList.remove('hidden');
                        stockGrid.classList.add('hidden');
                    } else {
                        noResultsMessage.classList.add('hidden');
                        stockGrid.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
@endsection
