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
                            <i data-lucide="trending-up" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold bg-gradient-to-r from-primary via-secondary to-primary bg-clip-text text-transparent">
                                Expert Traders
                            </h1>
                            <p class="text-xs sm:text-sm dark:text-gray-400 text-gray-600 mt-1">
                                <i data-lucide="shield-check" class="w-3 h-3 inline mr-1"></i>
                                <span class="hidden sm:inline">Verified professionals • Copy trades automatically • Earn passive income</span>
                                <span class="sm:hidden">Copy trades • Earn passive income</span>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('copytrading.subscriptions') }}" class="flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm sm:text-base bg-primary hover:bg-primary/90 text-white transition-colors whitespace-nowrap flex-shrink-0">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                        <span>My Subscriptions</span>
                    </a>
                </div>

                <!-- Stats Banner -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="rounded-lg p-4 bg-gray-800 border border-gray-700 hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                <i data-lucide="users" class="w-4 h-4 text-primary"></i>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">Active Traders</span>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ count($expertTraders) }}</p>
                    </div>
                    <div class="rounded-lg p-4 bg-gray-800 border border-gray-700 hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-4 h-4 text-green-500"></i>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">Avg ROI</span>
                        </div>
                        <p class="text-2xl font-bold text-green-400">{{ $expertTraders->avg('roi_percentage') ? number_format($expertTraders->avg('roi_percentage'), 1) : '0' }}%</p>
                    </div>
                    <div class="rounded-lg p-4 bg-gray-800 border border-gray-700 hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                <i data-lucide="target" class="w-4 h-4 text-blue-500"></i>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">Win Rate</span>
                        </div>
                        <p class="text-2xl font-bold text-blue-400">{{ $expertTraders->avg('win_rate') ? number_format($expertTraders->avg('win_rate'), 0) : '0' }}%</p>
                    </div>
                    <div class="rounded-lg p-4 bg-gray-800 border border-gray-700 hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center">
                                <i data-lucide="activity" class="w-4 h-4 text-secondary"></i>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">Active Trades</span>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ $expertTraders->sum('active_trades') }}</p>
                    </div>
                </div>
            </div>

            <!-- Feedback Messages -->
            <x-danger-alert />
            <x-success-alert />

            <!-- Search & Filter Bar -->
            <div class="mb-8 flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="traderSearch" 
                        placeholder="Search traders by name, risk level, or performance..." 
                        class="w-full px-5 py-4 pl-14 rounded-2xl border border-gray-200/50 dark:border-dark-200/50 bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl text-dark dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary/50 transition-all shadow-sm hover:shadow-md"
                    >
                    <i data-lucide="search" class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                </div>
                <div class="flex gap-2">
                    <button onclick="filterByRisk('all')" class="filter-btn active px-5 py-4 rounded-2xl font-medium text-sm transition-all bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl border border-gray-200/50 dark:border-dark-200/50 hover:shadow-md" data-filter="all">
                        All Levels
                    </button>
                    <button onclick="filterByRisk('Low')" class="filter-btn px-5 py-4 rounded-2xl font-medium text-sm transition-all bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl border border-gray-200/50 dark:border-dark-200/50 hover:shadow-md" data-filter="Low">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block mr-1"></span> Low Risk
                    </button>
                    <button onclick="filterByRisk('Medium')" class="filter-btn px-5 py-4 rounded-2xl font-medium text-sm transition-all bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl border border-gray-200/50 dark:border-dark-200/50 hover:shadow-md" data-filter="Medium">
                        <span class="w-2 h-2 rounded-full bg-yellow-500 inline-block mr-1"></span> Medium Risk
                    </button>
                    <button onclick="filterByRisk('High')" class="filter-btn px-5 py-4 rounded-2xl font-medium text-sm transition-all bg-white/70 dark:bg-dark-50/70 backdrop-blur-xl border border-gray-200/50 dark:border-dark-200/50 hover:shadow-md" data-filter="High">
                        <span class="w-2 h-2 rounded-full bg-red-500 inline-block mr-1"></span> High Risk
                    </button>
                </div>
            </div>

            <!-- Expert Traders Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="tradersGrid">
                @forelse ($expertTraders as $trader)
                <div class="trader-card group rounded-2xl overflow-hidden bg-white dark:bg-dark-50 border border-gray-200 dark:border-dark-200 hover:border-primary dark:hover:border-primary shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1" data-risk="{{ $trader->risk_level }}">
                    
                    <!-- Verified Badge -->
                    @if($trader->win_rate >= 75)
                    <div class="absolute top-4 right-4 z-10 px-2.5 py-1 rounded-lg bg-blue-600 flex items-center gap-1.5 shadow-md">
                        <i data-lucide="badge-check" class="w-3.5 h-3.5 text-white"></i>
                        <span class="text-xs font-semibold text-white">Top</span>
                    </div>
                    @endif

                    <!-- Trader Header -->
                    <div class="relative p-6 bg-gray-50 dark:bg-dark-100 border-b border-gray-200 dark:border-dark-200">
                        <div class="relative flex items-start gap-4">
                            <div class="relative flex-shrink-0">
                                @if ($trader->photo)
                                    <img src="{{ asset('storage/app/public/expert_traders/' . $trader->photo) }}" 
                                         alt="{{ $trader->name }}" 
                                         class="w-16 h-16 rounded-xl object-cover border-2 border-gray-200 dark:border-dark-200 shadow-sm">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-primary flex items-center justify-center text-white text-xl font-bold border-2 border-gray-200 dark:border-dark-200 shadow-sm">
                                        {{ strtoupper(substr($trader->name, 0, 2)) }}
                                    </div>
                                @endif
                                <!-- Online Status -->
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-dark-50"></div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-lg dark:text-white text-dark trader-name truncate mb-2">
                                    {{ $trader->name }}
                                </h3>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="flex items-center gap-1.5 text-xs dark:text-gray-400 text-gray-600">
                                        <i data-lucide="users-2" class="w-3.5 h-3.5"></i>
                                        <span class="font-medium">{{ $trader->preset_followers + $trader->total_followers }} followers</span>
                                    </div>
                                </div>
                                <div class="inline-flex px-2.5 py-1 rounded-md text-xs font-semibold
                                    {{ $trader->risk_level === 'Low' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                    {{ $trader->risk_level === 'Medium' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                    {{ $trader->risk_level === 'High' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                    trader-risk">
                                    {{ $trader->risk_level }} Risk
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trader Details -->
                    <div class="p-6">
                        @if ($trader->bio)
                        <p class="text-sm dark:text-gray-400 text-gray-600 mb-5 line-clamp-2 leading-relaxed">{{ $trader->bio }}</p>
                        @endif

                        <!-- Key Performance Metrics -->
                        <div class="grid grid-cols-3 gap-3 mb-5">
                            <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-dark-100 border border-gray-200 dark:border-dark-200">
                                <p class="text-xs dark:text-gray-400 text-gray-600 mb-1 font-medium">ROI</p>
                                <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($trader->roi_percentage, 1) }}%</p>
                            </div>
                            <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-dark-100 border border-gray-200 dark:border-dark-200">
                                <p class="text-xs dark:text-gray-400 text-gray-600 mb-1 font-medium">Win Rate</p>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($trader->win_rate, 0) }}%</p>
                            </div>
                            <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-dark-100 border border-gray-200 dark:border-dark-200">
                                <p class="text-xs dark:text-gray-400 text-gray-600 mb-1 font-medium">Trades</p>
                                <p class="text-xl font-bold dark:text-white text-dark">{{ $trader->active_trades }}</p>
                            </div>
                        </div>

                        <!-- Investment Range Card -->
                        <div class="rounded-lg p-4 mb-5 bg-gray-50 dark:bg-dark-100 border border-gray-200 dark:border-dark-200">
                            <div class="flex items-center gap-2 mb-3">
                                <i data-lucide="wallet" class="w-4 h-4 text-gray-600 dark:text-gray-400"></i>
                                <span class="text-xs dark:text-gray-400 text-gray-600 font-semibold uppercase tracking-wider">Investment Range</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs dark:text-gray-500 text-gray-500 mb-1">Minimum</p>
                                    <p class="text-base font-bold dark:text-white text-dark">{{ $settings->currency }}{{ number_format($trader->min_capital, 0) }}</p>
                                </div>
                                <div class="flex-1 mx-4 h-px bg-gray-300 dark:bg-dark-200"></div>
                                <div class="text-right">
                                    <p class="text-xs dark:text-gray-500 text-gray-500 mb-1">Maximum</p>
                                    <p class="text-base font-bold dark:text-white text-dark">{{ $settings->currency }}{{ number_format($trader->max_capital, 0) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info Row -->
                        <div class="flex items-center gap-2 text-sm mb-5 pb-5 border-b border-gray-200 dark:border-dark-200">
                            <i data-lucide="calendar-clock" class="w-4 h-4 text-gray-500 dark:text-gray-400"></i>
                            <div>
                                <p class="text-xs dark:text-gray-500 text-gray-500">Duration</p>
                                <p class="font-semibold dark:text-white text-dark">{{ $trader->duration }}</p>
                            </div>
                        </div>

                        <!-- Capacity Indicator -->
                        @if ($trader->getAvailableSlots() > 0)
                        <div class="mb-4 p-3 rounded-xl bg-green-500/10 border border-green-500/20 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                <span class="text-sm font-medium text-green-700 dark:text-green-400">
                                    {{ $trader->getAvailableSlots() }} slots available
                                </span>
                            </div>
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                        </div>
                        @else
                        <div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-sm font-medium text-red-700 dark:text-red-400">
                                    Capacity reached
                                </span>
                            </div>
                            <i data-lucide="x-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                        </div>
                        @endif

                        <!-- Copy Button with Enhanced Design -->
                        @php
                            $isSubscribed = in_array($trader->id, $userSubscriptions ?? []);
                        @endphp
                        
                        @if($isSubscribed)
                            <!-- Stop Copy Button -->
                            <form action="{{ route('copytrading.cancel', $subscriptionIds[$trader->id] ?? 0) }}" method="POST" onsubmit="return confirm('Are you sure you want to stop copying this trader? Your capital will be returned to your account.');">
                                @csrf
                                <button 
                                    type="submit"
                                    class="w-full py-4 rounded-2xl font-bold text-base shadow-lg shadow-red-500/25 hover:shadow-xl hover:shadow-red-500/40 transition-all duration-300 flex items-center justify-center gap-3 group/btn bg-gradient-to-r from-red-500 via-red-600 to-red-500 bg-size-200 hover:bg-pos-100 text-white">
                                    <i data-lucide="x-circle" class="w-5 h-5 group-hover/btn:scale-110 transition-transform"></i>
                                    <span>Stop Copy Trader</span>
                                    <i data-lucide="arrow-right" class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform"></i>
                                </button>
                            </form>
                        @else
                            <!-- Copy Trader Button -->
                            <button 
                                onclick="openCopyModal({{ $trader->id }}, '{{ $trader->name }}', {{ $trader->min_capital }}, {{ $trader->max_capital }})"
                                class="w-full py-4 rounded-2xl font-bold text-base shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/40 transition-all duration-300 flex items-center justify-center gap-3 group/btn
                                {{ $trader->isAtCapacity() ? 'bg-gray-300 dark:bg-dark-100 text-gray-500 dark:text-gray-600 cursor-not-allowed' : 'bg-gradient-to-r from-primary via-secondary to-primary bg-size-200 hover:bg-pos-100 text-white' }}"
                                {{ $trader->isAtCapacity() ? 'disabled' : '' }}>
                                @if(!$trader->isAtCapacity())
                                    <i data-lucide="copy" class="w-5 h-5 group-hover/btn:scale-110 transition-transform"></i>
                                    <span>Copy This Trader</span>
                                    <i data-lucide="arrow-right" class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform"></i>
                                @else
                                    <i data-lucide="lock" class="w-5 h-5"></i>
                                    <span>Capacity Full</span>
                                @endif
                            </button>
                        @endif
                    </div>
                </div>
                @empty
               
                <div class="col-span-full">
                    <div class="text-center py-20 px-4">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-gradient-to-br from-primary/10 to-secondary/10 mb-6 shadow-lg">
                            <i data-lucide="users" class="w-12 h-12 text-primary"></i>
                        </div>
                        <h3 class="text-2xl font-bold dark:text-white text-dark mb-3">No Expert Traders Available</h3>
                        <p class="text-sm dark:text-gray-400 text-gray-600 max-w-md mx-auto">
                            We're currently onboarding expert traders. Check back soon to start copying successful strategies!
                        </p>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- No Search Results Message -->
            <div id="noResults" class="hidden">
                <div class="text-center py-20 px-4">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-dark-100 dark:to-dark-200 mb-6 shadow-lg">
                        <i data-lucide="search-x" class="w-12 h-12 dark:text-gray-600 text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold dark:text-white text-dark mb-3">No Traders Found</h3>
                    <p class="text-sm dark:text-gray-400 text-gray-600 max-w-md mx-auto mb-6">
                        Try adjusting your search terms or filters to find the perfect trader for you.
                    </p>
                    <button onclick="resetSearch()" class="px-6 py-3 rounded-xl bg-primary text-white font-medium hover:bg-primary/90 transition-all inline-flex items-center gap-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Reset Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Copy Modal - Mobile Responsive -->
    <div id="copyModal" class="fixed inset-0 bg-black/70 backdrop-blur-md z-50 hidden flex items-center justify-center p-3 sm:p-4 animate-in fade-in duration-300">
        <div class="dark:bg-dark-50 bg-white rounded-2xl sm:rounded-3xl max-w-lg w-full shadow-2xl animate-in zoom-in-95 border border-gray-200/50 dark:border-dark-200/50 overflow-hidden max-h-[95vh] overflow-y-auto">
            <!-- Modal Header with Gradient - Sticky on scroll -->
            <div class="sticky top-0 z-10 bg-gradient-to-r from-primary/10 via-secondary/5 to-primary/10 border-b border-gray-200/50 dark:border-dark-200/50 p-4 sm:p-6 backdrop-blur-sm">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center shadow-lg flex-shrink-0">
                            <i data-lucide="copy" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg sm:text-xl font-bold dark:text-white text-dark truncate">Copy Trader</h3>
                            <p class="text-xs dark:text-gray-400 text-gray-600 truncate">Start copying profitable trades</p>
                        </div>
                    </div>
                    <button onclick="closeCopyModal()" class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl hover:bg-gray-100 dark:hover:bg-dark-100 dark:text-gray-400 text-gray-600 hover:text-dark dark:hover:text-white transition-all flex items-center justify-center flex-shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('copytrading.subscribe') }}" method="POST" class="p-4 sm:p-6">
                @csrf
                <input type="hidden" name="expert_trader_id" id="modal_trader_id">
                
                <!-- Trader Info Card -->
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-gradient-to-r from-primary/5 to-secondary/5 border border-primary/20">
                    <div class="flex items-center gap-2 sm:gap-3 mb-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="user-check" class="w-5 h-5 sm:w-6 sm:h-6 text-primary"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs dark:text-gray-400 text-gray-600 mb-1">Selected Trader</p>
                            <p class="font-bold text-base sm:text-lg dark:text-white text-dark truncate" id="modal_trader_name"></p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-2 p-2 sm:p-3 rounded-lg sm:rounded-xl bg-white/50 dark:bg-dark-100/50">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs dark:text-gray-500 text-gray-500 mb-1">Min Investment</p>
                            <p class="font-bold text-sm sm:text-base dark:text-white text-dark truncate" id="modal_min"></p>
                        </div>
                        <div class="w-px h-8 bg-gray-300 dark:bg-dark-200"></div>
                        <div class="flex-1 min-w-0 text-right">
                            <p class="text-xs dark:text-gray-500 text-gray-500 mb-1">Max Investment</p>
                            <p class="font-bold text-sm sm:text-base dark:text-white text-dark truncate" id="modal_max"></p>
                        </div>
                    </div>
                </div>

                <!-- Investment Amount Input -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-bold dark:text-gray-300 text-gray-700 mb-2 sm:mb-3 flex items-center gap-2">
                        <i data-lucide="wallet" class="w-4 h-4 text-primary"></i>
                        Investment Amount
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 sm:left-5 top-1/2 -translate-y-1/2 dark:text-gray-400 text-gray-600 font-bold text-base sm:text-lg">{{ $settings->currency }}</span>
                        <input 
                            type="number" 
                            name="amount" 
                            id="modal_amount"
                            step="0.01"
                            required
                            class="w-full pl-12 sm:pl-16 pr-4 sm:pr-6 py-3 sm:py-4 rounded-xl sm:rounded-2xl dark:bg-dark-100 bg-gray-50 border-2 border-gray-200 dark:border-dark-200 dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base sm:text-lg font-bold transition-all"
                            placeholder="0.00"
                            oninput="updateModalPreview()">
                    </div>
                    <div class="mt-2 sm:mt-3 p-2.5 sm:p-3 rounded-lg sm:rounded-xl bg-blue-500/10 border border-blue-500/20 hidden" id="amountPreview">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="dark:text-gray-400 text-gray-600">Your Balance:</span>
                            <span class="font-bold dark:text-white text-dark">{{ $settings->currency }}{{ number_format(auth()->user()->account_bal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm mt-2">
                            <span class="dark:text-gray-400 text-gray-600">After Investment:</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400" id="balanceAfter">-</span>
                        </div>
                    </div>
                </div>



                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button 
                        type="button"
                        onclick="closeCopyModal()"
                        class="w-full sm:flex-1 py-3 sm:py-4 rounded-xl sm:rounded-2xl border-2 border-gray-200 dark:border-dark-200 dark:text-gray-300 text-gray-700 hover:bg-gray-50 dark:hover:bg-dark-100 transition-all font-medium text-sm sm:text-base">
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="w-full sm:flex-1 py-3 sm:py-4 rounded-xl sm:rounded-2xl bg-gradient-to-r from-primary to-secondary text-white font-bold hover:shadow-lg hover:shadow-primary/50 transition-all flex items-center justify-center gap-2 group text-sm sm:text-base">
                        <i data-lucide="check-circle" class="w-4 h-4 sm:w-5 sm:h-5 group-hover:scale-110 transition-transform"></i>
                        <span>Confirm & Copy</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .bg-size-200 { background-size: 200% 100%; }
        .bg-pos-100 { background-position: 100% 0; }
        .animate-in { animation: fadeIn 0.3s ease-out; }
        .zoom-in-95 { animation: zoomIn 0.3s ease-out; }
        .fade-in { animation: fadeIn 0.3s ease-out; }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes zoomIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .filter-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
        }

        /* Smooth scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(var(--primary-rgb), 0.3);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(var(--primary-rgb), 0.5);
        }
    </style>

    <script>
        const userBalance = {{ auth()->user()->account_bal }};
        const currencySymbol = "{{ $settings->currency }}";

        // Enhanced search functionality
        document.getElementById('traderSearch').addEventListener('input', function() {
            performSearch();
        });

        function performSearch() {
            const searchTerm = document.getElementById('traderSearch').value.toLowerCase();
            const traderCards = document.querySelectorAll('.trader-card');
            const noResults = document.getElementById('noResults');
            const grid = document.getElementById('tradersGrid');
            let visibleCount = 0;

            traderCards.forEach(card => {
                const name = card.querySelector('.trader-name')?.textContent.toLowerCase() || '';
                const risk = card.querySelector('.trader-risk')?.textContent.toLowerCase() || '';
                const currentFilter = document.querySelector('.filter-btn.active')?.dataset.filter || 'all';
                const cardRisk = card.dataset.risk;

                const matchesSearch = name.includes(searchTerm) || risk.includes(searchTerm);
                const matchesFilter = currentFilter === 'all' || cardRisk === currentFilter;

                if (matchesSearch && matchesFilter) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
                grid.classList.add('hidden');
            } else {
                noResults.classList.add('hidden');
                grid.classList.remove('hidden');
            }
        }

        // Risk filter functionality
        function filterByRisk(level) {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.filter-btn').classList.add('active');
            performSearch();
        }

        function resetSearch() {
            document.getElementById('traderSearch').value = '';
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelector('.filter-btn[data-filter="all"]').classList.add('active');
            performSearch();
        }

        // Enhanced modal functions
        function openCopyModal(traderId, traderName, minCapital, maxCapital) {
            document.getElementById('modal_trader_id').value = traderId;
            document.getElementById('modal_trader_name').textContent = traderName;
            document.getElementById('modal_min').textContent = currencySymbol + minCapital.toLocaleString();
            document.getElementById('modal_max').textContent = currencySymbol + maxCapital.toLocaleString();
            document.getElementById('modal_amount').min = minCapital;
            document.getElementById('modal_amount').max = maxCapital;
            document.getElementById('modal_amount').value = '';
            document.getElementById('amountPreview').classList.add('hidden');
            document.getElementById('copyModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            setTimeout(() => {
                lucide.createIcons();
                document.getElementById('modal_amount').focus();
            }, 100);
        }

        function closeCopyModal() {
            document.getElementById('copyModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function updateModalPreview() {
            const amount = parseFloat(document.getElementById('modal_amount').value) || 0;
            const preview = document.getElementById('amountPreview');
            const balanceAfter = document.getElementById('balanceAfter');

            if (amount > 0) {
                preview.classList.remove('hidden');
                const remaining = userBalance - amount;
                balanceAfter.textContent = currencySymbol + remaining.toFixed(2);
                balanceAfter.className = remaining >= 0 
                    ? 'font-bold text-green-600 dark:text-green-400' 
                    : 'font-bold text-red-600 dark:text-red-400';
            } else {
                preview.classList.add('hidden');
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCopyModal();
            }
        });

        // Initialize Lucide icons after page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => lucide.createIcons(), 100);
        });
    </script>
@endsection
