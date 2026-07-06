@extends('layouts.dash')

@section('content')
<div class="content" x-data="{
    amount: {{ $plan->min_stake }},
    days: {{ $maxDays }},
    compound: false,
    apr: {{ $plan->apr_percentage }},
    
    get dailyReward() {
        return (this.amount * this.apr / 365 / 100);
    },
    
    get totalReward() {
        if (this.compound) {
            const rate = this.apr / 100;
            const compoundedAmount = this.amount * Math.pow((1 + rate / 365), this.days);
            return compoundedAmount - this.amount;
        } else {
            return this.dailyReward * this.days;
        }
    },
    
    get totalReturn() {
        return parseFloat(this.amount) + this.totalReward;
    },
    
    get roi() {
        return (this.totalReward / this.amount * 100);
    }
}">
    <div class="page-inner">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('crypto-staking.index') }}" class="inline-flex items-center text-blue-400 hover:text-blue-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Staking Plans
            </a>
        </div>

        @if(session()->has('message'))
        <div class="bg-secondary/10 border border-secondary text-secondary p-4 rounded-lg mb-6 flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('message') }}</span>
        </div>
        @endif

        @if(session()->has('error'))
        <div class="bg-danger/10 border border-danger text-danger p-4 rounded-lg mb-6 flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- Hero Section -->
        <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg overflow-hidden mb-6">
            <div class="p-8">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                    <!-- Crypto Icon & Info -->
                    <div class="flex-shrink-0">
                        <img src="{{ $plan->cryptoPrice->logo }}" alt="{{ $plan->coin_symbol }}" class="w-24 h-24 rounded-full border-4 border-gray-700">
                    </div>

                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $plan->name }}</h1>
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-4">
                            <span class="text-xl text-gray-400">{{ $plan->cryptoPrice->coin_symbol }} - {{ $plan->cryptoPrice->name }}</span>
                            @if($plan->status == 'active')
                            <span class="px-3 py-1 bg-green-900/50 text-green-400 text-sm rounded-full border border-green-500">Active</span>
                            @endif
                        </div>

                        @if($plan->description)
                        <p class="text-gray-400 mb-4">{{ $plan->description }}</p>
                        @endif

                        <!-- Live Price Info -->
                        <div class="flex flex-wrap gap-4">
                            <div class="dark:bg-dark-100 bg-light-200 rounded-lg px-4 py-2 dark:border-dark-100 border-light-200 border">
                                <div class="text-xs text-gray-400">Current Price</div>
                                <div class="text-lg font-bold text-white">{{ $settings->currency }}{{ number_format($plan->cryptoPrice->price_usd, 2) }}</div>
                            </div>
                            <div class="bg-gray-900 rounded-lg px-4 py-2 border border-gray-700">
                                <div class="text-xs text-gray-400">24h Change</div>
                                <div class="text-lg font-bold {{ $plan->cryptoPrice->percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ number_format($plan->cryptoPrice->percent_change_24h, 2) }}%
                                </div>
                            </div>
                            <div class="bg-gray-900 rounded-lg px-4 py-2 border border-gray-700">
                                <div class="text-xs text-gray-400">Market Cap</div>
                                <div class="text-lg font-bold text-white">{{ $settings->currency }}{{ number_format($plan->cryptoPrice->market_cap, 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Stats & Calculator -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-tertiary mb-1">{{ $plan->apr_percentage }}%</div>
                        <div class="text-sm text-gray-400">APR</div>
                    </div>
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-1">{{ $plan->lock_duration }}</div>
                        <div class="text-sm text-gray-400">Lock Period</div>
                    </div>
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-1">{{ $plan->reward_interval }}</div>
                        <div class="text-sm text-gray-400">Rewards</div>
                    </div>
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-400 mb-1">{{ $plan->activeSubscriptions->count() }}</div>
                        <div class="text-sm text-gray-400">Stakers</div>
                    </div>
                </div>

                <!-- APR Calculator -->
                <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-6">
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Staking Calculator
                    </h2>

                    <div class="space-y-4">
                        <!-- Amount Slider -->
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Stake Amount ({{ $settings->currency }})</label>
                            <input type="range" 
                                   x-model="amount" 
                                   min="{{ $plan->min_stake }}" 
                                   max="{{ $plan->max_stake }}" 
                                   step="10"
                                   class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer slider">
                            <div class="flex justify-between mt-2">
                                <span class="text-xs text-gray-500">{{ $settings->currency }}{{ number_format($plan->min_stake, 0) }}</span>
                                <input type="number" 
                                       x-model="amount" 
                                       min="{{ $plan->min_stake }}" 
                                       max="{{ $plan->max_stake }}"
                                       class="bg-gray-900 border border-gray-700 text-white rounded px-3 py-1 w-32 text-center">
                                <span class="text-xs text-gray-500">{{ $settings->currency }}{{ number_format($plan->max_stake, 0) }}</span>
                            </div>
                        </div>

                        <!-- Duration Slider (if applicable) -->
                        @if($plan->lock_duration != 'Flexible')
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Staking Period</label>
                            <input type="range" 
                                   x-model="days" 
                                   min="1" 
                                   max="{{ $maxDays }}" 
                                   step="1"
                                   class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer slider">
                            <div class="flex justify-between mt-2">
                                <span class="text-xs text-gray-500">1 day</span>
                                <span class="text-sm font-medium text-white" x-text="days + ' days'"></span>
                                <span class="text-xs text-gray-500">{{ $maxDays }} days</span>
                            </div>
                        </div>
                        @endif

                        <!-- Compound Toggle -->
                        @if($plan->compound_enabled == 'on')
                        <div class="flex items-center justify-between bg-gray-900 rounded-lg p-4 border border-gray-700">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <div>
                                    <div class="text-white font-medium">Auto-Compound Rewards</div>
                                    <div class="text-xs text-gray-400">Automatically restake rewards for higher returns</div>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="compound" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>
                        @endif

                        <!-- Results -->
                        <div class="bg-primary-900/20 border border-primary-500/50 rounded-lg p-6 mt-6">
                            <h3 class="text-lg font-bold text-white mb-4">Estimated Returns</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-gray-400">Daily Rewards</div>
                                    <div class="text-2xl font-bold text-green-400" x-text="'{{ $settings->currency }}' + dailyReward.toFixed(2)"></div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-400">Total Rewards</div>
                                    <div class="text-2xl font-bold text-green-400" x-text="'{{ $settings->currency }}' + totalReward.toFixed(2)"></div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-400">Total Return</div>
                                    <div class="text-2xl font-bold text-white" x-text="'{{ $settings->currency }}' + totalReturn.toFixed(2)"></div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-400">ROI</div>
                                    <div class="text-2xl font-bold text-yellow-400" x-text="roi.toFixed(2) + '%'"></div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-4">
                                * Estimates based on {{ $plan->apr_percentage }}% APR. Actual returns may vary.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Warnings -->
                @if($plan->early_withdrawal_penalty > 0 && $plan->lock_duration != 'Flexible')
                <div class="bg-red-900/20 border border-red-500 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h4 class="text-red-400 font-bold mb-1">Early Withdrawal Penalty</h4>
                            <p class="text-sm text-red-200">
                                If you cancel your stake before {{ $plan->lock_duration }}, a {{ $plan->early_withdrawal_penalty }}% penalty will be applied to your accumulated rewards. 
                                Your principal amount is always returned in full.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column: Subscribe Form -->
            <div class="lg:col-span-1">
                <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-white mb-4">Start Staking</h2>

                    <form action="{{ route('crypto-staking.subscribe', $plan->id) }}" method="POST" id="stakeForm">
                        @csrf

                        <!-- Amount Input -->
                        <div class="mb-4">
                            <label class="block text-sm text-gray-400 mb-2">Stake Amount <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">{{ $settings->currency }}</span>
                                <input type="number" 
                                       name="amount" 
                                       required 
                                       min="{{ $plan->min_stake }}" 
                                       max="{{ $plan->max_stake }}" 
                                       step="0.01"
                                       x-model="amount"
                                       class="w-full dark:bg-dark-100 bg-light-200 dark:border-dark-100 border-light-200 border dark:text-white text-dark rounded-lg pl-12 pr-4 py-3 focus:outline-none focus:border-primary"
                                       placeholder="Enter amount">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Min: {{ $settings->currency }}{{ number_format($plan->min_stake, 2) }} | 
                                Max: {{ $settings->currency }}{{ number_format($plan->max_stake, 2) }}
                            </p>
                        </div>

                        <!-- Your Balance -->
                        <div class="bg-gray-900 rounded-lg p-3 mb-4 border border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-400">Your Balance</span>
                                <span class="text-lg font-bold text-white">{{ $settings->currency }}{{ number_format(auth()->user()->account_bal, 2) }}</span>
                            </div>
                        </div>

                        <!-- Compound Option -->
                        @if($plan->compound_enabled == 'on')
                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer bg-gray-900 rounded-lg p-4 border border-gray-700 hover:border-green-500 transition">
                                <input type="checkbox" name="compound_enabled" value="1" x-model="compound" class="w-5 h-5 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500">
                                <div class="ml-3">
                                    <div class="text-white font-medium">Enable Auto-Compound</div>
                                    <div class="text-xs text-gray-400">Automatically restake rewards for maximum returns</div>
                                </div>
                            </label>
                        </div>
                        @endif

                        <!-- Capacity Check -->
                        @if($plan->max_total_staked)
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-gray-400 mb-2">
                                <span>Plan Capacity</span>
                                <span>{{ number_format(($plan->current_total_staked / $plan->max_total_staked) * 100, 1) }}% filled</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" 
                                     style="width: {{ min(($plan->current_total_staked / $plan->max_total_staked) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        @endif

                        <!-- Summary -->
                        <div class="bg-gray-900 rounded-lg p-4 mb-4 border border-gray-700 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Lock Duration</span>
                                <span class="text-white font-medium">{{ $plan->lock_duration }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Fixed APR</span>
                                <span class="text-green-400 font-bold">{{ $plan->apr_percentage }}%</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Reward Interval</span>
                                <span class="text-white">{{ $plan->reward_interval }}</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" 
                                @click="
                                    if (!amount || parseFloat(amount) <= 0) {
                                        swal({title: 'Invalid Amount', text: 'Please enter a valid stake amount.', icon: 'error', button: 'OK'});
                                        return;
                                    }
                                    if (parseFloat(amount) > {{ auth()->user()->account_bal }}) {
                                        swal({title: 'Insufficient Balance', text: 'You don\'t have enough balance to stake this amount.', icon: 'warning', button: 'OK'});
                                        return;
                                    }
                                    swal({
                                        title: 'Confirm Stake',
                                        text: 'Are you sure you want to stake {{ $settings->currency }}' + parseFloat(amount).toFixed(2) + '? This amount will be locked for {{ $plan->lock_duration }}.',
                                        icon: 'warning',
                                        buttons: {
                                            cancel: {text: 'Cancel', visible: true, closeModal: true},
                                            confirm: {text: 'Yes, Stake Now', closeModal: false}
                                        },
                                        dangerMode: true
                                    }).then((willStake) => {
                                        if (willStake) {
                                            swal({title: 'Processing...', text: 'Please wait while we process your stake.', icon: 'info', buttons: false, closeOnClickOutside: false, closeOnEsc: false});
                                            document.getElementById('stakeForm').submit();
                                        }
                                    });
                                "
                                class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Stake Now
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-3">
                            By staking, you agree that your APR rate ({{ $plan->apr_percentage }}%) will be locked at subscription.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
