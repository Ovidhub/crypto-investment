@extends('layouts.dash')
@section('content')
<div class="content">
    <div class="page-inner">
        <div class="mb-5">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">Crypto Staking</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-2">Stake your crypto assets and earn passive rewards</p>
        </div>

        <!-- Global Pause Warning -->
        @if($settings->pause_all_staking_distributions == 'on')
        <div class="bg-danger/10 border border-danger text-danger dark:text-red-200 p-4 rounded-lg mb-6 flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <strong class="font-bold">Staking Distributions Paused</strong>
                <p class="text-sm">All staking reward distributions are temporarily paused. You can still view plans but cannot subscribe at this time.</p>
            </div>
        </div>
        @endif

        <!-- Stats Cards -->
        @if($myActiveStakes > 0)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4">
                <div class="dark:text-gray-400 text-gray-600 text-sm">Total Staked</div>
                <div class="text-2xl font-bold dark:text-white text-dark mt-1">{{ $settings->currency }}{{ number_format($myTotalStaked, 2) }}</div>
            </div>
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4">
                <div class="dark:text-gray-400 text-gray-600 text-sm">Total Rewards</div>
                <div class="text-2xl font-bold text-secondary mt-1">{{ $settings->currency }}{{ number_format($myTotalRewards, 2) }}</div>
            </div>
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4">
                <div class="dark:text-gray-400 text-gray-600 text-sm">Active Stakes</div>
                <div class="text-2xl font-bold text-tertiary mt-1">{{ $myActiveStakes }}</div>
            </div>
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4">
                <div class="dark:text-gray-400 text-gray-600 text-sm">Average APR</div>
                <div class="text-2xl font-bold text-accent mt-1">{{ number_format($myAvgApr, 2) }}%</div>
            </div>
        </div>
        @endif

          <!-- Search and Filter -->
        <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <input type="text" id="searchPlans" placeholder="Search staking plans..." 
                           class="w-full dark:bg-dark-100 bg-light-200 dark:border-dark-100 border-light-200 border dark:text-white text-dark rounded-lg px-3 sm:px-4 py-2 text-sm focus:outline-none focus:border-primary">
                </div>
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                    <select id="filterDuration" class="flex-1 sm:flex-initial dark:bg-dark-100 bg-light-200 dark:border-dark-100 border-light-200 border dark:text-white text-dark rounded-lg px-3 sm:px-4 py-2 text-sm focus:outline-none focus:border-primary">
                        <option value="">All Durations</option>
                        <option value="Flexible">Flexible</option>
                        <option value="7 Days">7 Days</option>
                        <option value="30 Days">30 Days</option>
                        <option value="90 Days">90 Days</option>
                        <option value="180 Days">180 Days</option>
                    </select>
                    <select id="sortBy" class="flex-1 sm:flex-initial dark:bg-dark-100 bg-light-200 dark:border-dark-100 border-light-200 border dark:text-white text-dark rounded-lg px-3 sm:px-4 py-2 text-sm focus:outline-none focus:border-primary">
                        <option value="apr-high">APR: High to Low</option>
                        <option value="apr-low">APR: Low to High</option>
                        <option value="duration-short">Duration: Short to Long</option>
                        <option value="duration-long">Duration: Long to Short</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="{{ route('crypto-staking.subscriptions') }}" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-500 text-white rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                My Subscriptions
            </a>
            @if($settings->show_staking_leaderboard == 'on')
            <a href="{{ route('crypto-staking.leaderboard') }}" class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-600 text-white rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                Leaderboard
            </a>
            @endif
        </div>

        <!-- Staking Plans Grid -->
        <div id="plansGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($plans as $plan)
            <div class="staking-plan-card dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg overflow-hidden hover:border-primary transition duration-300"
                 data-duration="{{ $plan->lock_duration }}"
                 data-apr="{{ $plan->apr_percentage }}"
                 data-name="{{ strtolower($plan->name) }}"
                 data-symbol="{{ strtolower($plan->coin_symbol) }}">
                
                <!-- Card Header with Crypto Info -->
                <div class="p-6 border-b dark:border-dark-100 border-light-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <img src="{{ $plan->cryptoPrice->logo }}" alt="{{ $plan->coin_symbol }}" class="w-12 h-12 rounded-full mr-3">
                            <div>
                                <h3 class="text-xl font-bold text-white">{{ $plan->coin_symbol }}</h3>
                                <p class="text-sm text-gray-400">{{ $plan->cryptoPrice->name }}</p>
                            </div>
                        </div>
                        @if($plan->status == 'active')
                        <span class="px-3 py-1 bg-green-900/50 text-green-400 text-xs rounded-full border border-green-500">Active</span>
                        @else
                        <span class="px-3 py-1 bg-gray-700 text-gray-400 text-xs rounded-full">Inactive</span>
                        @endif
                    </div>

                    <!-- Live Price -->
                    <div class="flex items-center justify-between dark:bg-dark-100 bg-light-200 rounded-lg p-3">
                        <div>
                            <div class="text-xs text-gray-400">Current Price</div>
                            <div class="text-lg font-bold text-white">{{ $settings->currency }}{{ number_format($plan->cryptoPrice->price_usd, 2) }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-400">24h Change</div>
                            <div class="text-lg font-bold {{ $plan->cryptoPrice->percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ number_format($plan->cryptoPrice->percent_change_24h, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Details -->
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-white mb-4">{{ $plan->name }}</h4>
                    
                    @if($plan->description)
                    <p class="text-sm text-gray-400 mb-4 line-clamp-2">{{ $plan->description }}</p>
                    @endif

                    <!-- APR Badge (Prominent) -->
                    <div class="bg-primary/10 border border-primary rounded-lg p-4 mb-4 text-center">
                        <div class="text-sm text-primary mb-1">Annual Percentage Rate</div>
                        <div class="text-3xl font-bold text-primary">{{ $plan->apr_percentage }}%</div>
                        <div class="text-xs text-gray-400 mt-1">Fixed APR at subscription</div>
                    </div>

                    <!-- Plan Features -->
                    <div class="space-y-3 mb-4">
                        <!-- Lock Duration -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Lock Duration</span>
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $plan->lock_duration == 'Flexible' ? 'bg-secondary/20 text-secondary border border-secondary' : 
                                   'bg-tertiary/20 text-tertiary border border-tertiary' }}">
                                {{ $plan->lock_duration }}
                            </span>
                        </div>

                        <!-- Min/Max Stake -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Stake Range</span>
                            <span class="text-sm text-white font-medium">
                                {{ $settings->currency }}{{ number_format($plan->min_stake, 0) }} - {{ $settings->currency }}{{ number_format($plan->max_stake, 0) }}
                            </span>
                        </div>

                        <!-- Reward Interval -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Rewards</span>
                            <span class="text-sm text-white">{{ $plan->reward_interval }}</span>
                        </div>

                        <!-- Early Withdrawal Penalty -->
                        @if($plan->early_withdrawal_penalty > 0 && $plan->lock_duration != 'Flexible')
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Early Withdrawal</span>
                            <span class="px-2 py-1 bg-red-900/50 text-red-400 text-xs rounded border border-red-500">
                                {{ $plan->early_withdrawal_penalty }}% penalty
                            </span>
                        </div>
                        @endif

                        <!-- Compound Option -->
                        @if($plan->compound_enabled == 'on')
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Auto-Compound</span>
                            <span class="flex items-center text-sm text-green-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Available
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Capacity Bar -->
                    {{-- @if($plan->max_total_staked)
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                            <span>Capacity</span>
                            <span>{{ number_format(($plan->current_total_staked / $plan->max_total_staked) * 100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ min(($plan->current_total_staked / $plan->max_total_staked) * 100, 100) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $settings->currency }}{{ number_format($plan->current_total_staked, 0) }} / {{ $settings->currency }}{{ number_format($plan->max_total_staked, 0) }}
                        </div>
                    </div>
                    @endif --}}

                    <!-- Action Button -->
                    <a href="{{ route('crypto-staking.show', $plan->id) }}" 
                       class="block w-full text-center px-4 py-3 bg-primary hover:bg-primary-700 text-white font-medium rounded-lg transition">
                        View Details & Stake
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">No Staking Plans Available</h3>
                    <p class="text-gray-400">Check back later for new staking opportunities.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchPlans');
    const filterDuration = document.getElementById('filterDuration');
    const sortBy = document.getElementById('sortBy');
    const planCards = document.querySelectorAll('.staking-plan-card');

    function filterAndSortPlans() {
        const searchTerm = searchInput.value.toLowerCase();
        const durationFilter = filterDuration.value;
        let visibleCards = Array.from(planCards).filter(card => {
            const name = card.dataset.name;
            const symbol = card.dataset.symbol;
            const duration = card.dataset.duration;
            
            const matchesSearch = name.includes(searchTerm) || symbol.includes(searchTerm);
            const matchesDuration = !durationFilter || duration === durationFilter;
            
            if (matchesSearch && matchesDuration) {
                card.style.display = '';
                return true;
            } else {
                card.style.display = 'none';
                return false;
            }
        });

        // Sort visible cards
        const sortValue = sortBy.value;
        visibleCards.sort((a, b) => {
            const aprA = parseFloat(a.dataset.apr);
            const aprB = parseFloat(b.dataset.apr);
            
            if (sortValue === 'apr-high') return aprB - aprA;
            if (sortValue === 'apr-low') return aprA - aprB;
            
            return 0;
        });

        // Reorder in DOM
        const grid = document.getElementById('plansGrid');
        visibleCards.forEach(card => grid.appendChild(card));
    }

    searchInput.addEventListener('input', filterAndSortPlans);
    filterDuration.addEventListener('change', filterAndSortPlans);
    sortBy.addEventListener('change', filterAndSortPlans);
});
</script>
@endpush
@endsection
