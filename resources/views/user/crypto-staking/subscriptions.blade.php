@extends('layouts.dash')
@section('content')
<div class="content">
    <div class="page-inner">
        <div class="mb-5">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">My Staking Subscriptions</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-2">Track and manage your active crypto stakes</p>
        </div>

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('crypto-staking.index') }}" class="inline-flex items-center text-primary hover:text-primary-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Staking Plans
            </a>
        </div>

        @if(session()->has('message'))
        <div class="bg-green-900/50 border border-green-500 text-green-200 p-4 rounded-lg mb-6 flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('message') }}</span>
        </div>
        @endif

        @if(session()->has('error'))
        <div class="bg-red-900/50 border border-red-500 text-red-200 p-4 rounded-lg mb-6 flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- Portfolio Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="dark:text-gray-400 text-gray-600 text-sm">Total Staked</div>
                    <svg class="w-8 h-8 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold dark:text-white text-dark">{{ $settings->currency }}{{ number_format($totalStaked, 2) }}</div>
                <div class="text-xs dark:text-gray-500 text-gray-400 mt-1">Including compounded: {{ $settings->currency }}{{ number_format($totalCompounded, 2) }}</div>
            </div>

            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="dark:text-gray-400 text-gray-600 text-sm">Total Rewards</div>
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-secondary">{{ $settings->currency }}{{ number_format($totalRewards, 2) }}</div>
                <div class="text-xs dark:text-gray-500 text-gray-400 mt-1">Lifetime earnings from staking</div>
            </div>

            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="dark:text-gray-400 text-gray-600 text-sm">Active Stakes</div>
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold dark:text-white text-dark">{{ $activeCount }}</div>
                <div class="text-xs dark:text-gray-500 text-gray-400 mt-1">{{ $subscriptions->where('status', '!=', 'active')->count() }} completed/cancelled</div>
            </div>

            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="dark:text-gray-400 text-gray-600 text-sm">Avg APR</div>
                    <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-accent">{{ number_format($avgApr, 2) }}%</div>
                <div class="text-xs dark:text-gray-500 text-gray-400 mt-1">Across all active stakes</div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2 mb-6">
            <button onclick="filterSubscriptions('all')" class="filter-btn active px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium">
                All ({{ $subscriptions->count() }})
            </button>
            <button onclick="filterSubscriptions('active')" class="filter-btn px-4 py-2 dark:bg-dark-100 bg-light-200 dark:text-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:border-primary">
                Active ({{ $subscriptions->where('status', 'active')->count() }})
            </button>
            <button onclick="filterSubscriptions('completed')" class="filter-btn px-4 py-2 dark:bg-dark-100 bg-light-200 dark:text-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:border-primary">
                Completed ({{ $subscriptions->where('status', 'completed')->count() }})
            </button>
            <button onclick="filterSubscriptions('cancelled')" class="filter-btn px-4 py-2 dark:bg-dark-100 bg-light-200 dark:text-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:border-primary">
                Cancelled ({{ $subscriptions->where('status', 'cancelled')->count() }})
            </button>
        </div>

        <!-- Subscriptions Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($subscriptions as $subscription)
            <div class="subscription-card dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg overflow-hidden hover:border-primary transition duration-300" 
                 data-status="{{ $subscription->status }}">
                
                <!-- Card Header -->
                <div class="p-6 border-b dark:border-dark-100 border-light-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <img src="{{ $subscription->stakingPlan->cryptoPrice->logo }}" 
                                 alt="{{ $subscription->stakingPlan->coin_symbol }}" 
                                 class="w-12 h-12 rounded-full mr-3">
                            <div>
                                <h3 class="text-lg font-bold dark:text-white text-dark">{{ $subscription->stakingPlan->name }}</h3>
                                <p class="text-sm dark:text-gray-400 text-gray-600">{{ $subscription->stakingPlan->coin_symbol }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($subscription->status == 'active')
                            <span class="px-3 py-1 bg-secondary/20 text-secondary text-xs rounded-full border border-secondary">Active</span>
                            @elseif($subscription->status == 'completed')
                            <span class="px-3 py-1 bg-tertiary/20 text-tertiary text-xs rounded-full border border-tertiary">Completed</span>
                            @elseif($subscription->status == 'cancelled')
                            <span class="px-3 py-1 dark:bg-gray-700 bg-gray-200 dark:text-gray-400 text-gray-600 text-xs rounded-full">Cancelled</span>
                            @elseif($subscription->status == 'force_completed')
                            <span class="px-3 py-1 bg-danger/20 text-danger text-xs rounded-full border border-danger">Force Completed</span>
                            @endif
                            @if($subscription->is_compound_enabled)
                            <span class="block mt-1 px-3 py-1 bg-secondary/20 text-secondary text-xs rounded-full border border-secondary">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Compounding
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Amounts -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="dark:bg-dark-100 bg-light-200 rounded-lg p-3 dark:border-dark-100 border-light-200 border">
                            <div class="text-xs dark:text-gray-400 text-gray-600">Staked</div>
                            <div class="text-lg font-bold dark:text-white text-dark">{{ $settings->currency }}{{ number_format($subscription->amount_staked, 2) }}</div>
                        </div>
                        @if($subscription->compounded_amount > 0)
                        <div class="dark:bg-dark-100 bg-light-200 rounded-lg p-3 dark:border-dark-100 border-light-200 border">
                            <div class="text-xs dark:text-gray-400 text-gray-600">Compounded</div>
                            <div class="text-lg font-bold text-secondary">+{{ $settings->currency }}{{ number_format($subscription->compounded_amount, 2) }}</div>
                        </div>
                        @endif
                        <div class="dark:bg-dark-100 bg-light-200 rounded-lg p-3 dark:border-dark-100 border-light-200 border">
                            <div class="text-xs dark:text-gray-400 text-gray-600">Rewards</div>
                            <div class="text-lg font-bold text-accent">{{ $settings->currency }}{{ number_format($subscription->current_rewards, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6">
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-sm dark:text-gray-400 text-gray-600">Locked APR</div>
                            <div class="text-xl font-bold text-tertiary">{{ $subscription->apr_at_subscription }}%</div>
                        </div>
                        <div>
                            <div class="text-sm dark:text-gray-400 text-gray-600">Total Staked</div>
                            <div class="text-xl font-bold dark:text-white text-dark">{{ $settings->currency }}{{ number_format($subscription->totalStaked, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-sm dark:text-gray-400 text-gray-600">Start Date</div>
                            <div class="text-sm dark:text-white text-dark">{{ $subscription->start_date->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm dark:text-gray-400 text-gray-600">End Date</div>
                            <div class="text-sm dark:text-white text-dark">
                                @if($subscription->lock_type == 'flexible')
                                <span class="text-secondary">Flexible</span>
                                @else
                                {{ $subscription->end_date->format('M d, Y') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar (for active locked stakes) -->
                    @if($subscription->status == 'active' && $subscription->lock_type != 'flexible')
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="dark:text-gray-400 text-gray-600">Progress</span>
                            <span class="dark:text-white text-dark">
                                @if($subscription->daysRemaining > 0)
                                {{ $subscription->daysRemaining }} days left
                                @else
                                <span class="text-secondary">Unlocked</span>
                                @endif
                            </span>
                        </div>
                        <div class="w-full dark:bg-gray-700 bg-gray-300 rounded-full h-3">
                            <div class="bg-tertiary h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ $subscription->progressPercentage }}%"></div>
                        </div>
                        <div class="text-xs text-center dark:text-gray-400 text-gray-600 mt-1">{{ round($subscription->progressPercentage) }}% complete</div>
                    </div>
                    @endif

                    <!-- ROI Stats -->
                    <div class="dark:bg-dark-100 bg-light-200 rounded-lg p-4 dark:border-dark-100 border-light-200 border mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm dark:text-gray-400 text-gray-600">Total Return</div>
                                <div class="text-xl font-bold dark:text-white text-dark">{{ $settings->currency }}{{ number_format($subscription->totalReturn, 2) }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm dark:text-gray-400 text-gray-600">Profit %</div>
                                <div class="text-xl font-bold text-secondary">+{{ number_format($subscription->profitPercentage, 2) }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($subscription->status == 'active')
                    <div class="flex gap-3">
                        <a href="{{ route('crypto-staking.show', $subscription->stakingPlan->id) }}" 
                           class="flex-1 text-center px-4 py-2 dark:bg-dark-100 bg-light-200 hover:border-primary dark:text-white text-dark rounded-lg transition">
                            View Plan
                        </a>
                        @if($subscription->canWithdraw())
                        <form action="{{ route('crypto-staking.cancel', $subscription->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to cancel this stake? @if($subscription->calculatePenalty() > 0)A penalty of {{ $settings->currency }}{{ number_format($subscription->calculatePenalty(), 2) }} will be applied.@endif');"
                              class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-danger hover:bg-danger/80 text-white rounded-lg transition">
                                Cancel Stake
                            </button>
                        </form>
                        @else
                        <button disabled class="flex-1 px-4 py-2 dark:bg-gray-600 bg-gray-300 dark:text-gray-400 text-gray-500 rounded-lg cursor-not-allowed">
                            Locked
                        </button>
                        @endif
                    </div>
                    
                    <!-- Penalty Warning -->
                    @if($subscription->lock_type != 'flexible' && !$subscription->hasExpired() && $subscription->stakingPlan->early_withdrawal_penalty > 0)
                    <p class="text-xs text-danger mt-2 text-center">
                        Early cancellation penalty: {{ $subscription->stakingPlan->early_withdrawal_penalty }}% on rewards
                    </p>
                    @endif
                    @endif

                    <!-- Force Completed Info -->
                    @if($subscription->wasForceCompleted())
                    <div class="bg-danger/10 border border-danger rounded-lg p-3 mt-3">
                        <p class="text-xs text-danger">
                            <strong>Force Completed:</strong> {{ $subscription->force_complete_reason }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">No Subscriptions Yet</h3>
                    <p class="text-gray-400 mb-4">Start staking to earn passive rewards on your crypto</p>
                    <a href="{{ route('crypto-staking.index') }}" 
                       class="inline-block px-6 py-3 bg-primary hover:bg-primary-700 text-white rounded-lg transition">
                        Browse Staking Plans
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterSubscriptions(status) {
    const cards = document.querySelectorAll('.subscription-card');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Update button styles
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-primary', 'text-white');
        btn.classList.add('bg-gray-700', 'text-gray-300');
    });
    event.target.classList.add('active', 'bg-primary', 'text-white');
    event.target.classList.remove('bg-gray-700', 'text-gray-300');
    
    // Filter cards
    cards.forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endpush
@endsection
