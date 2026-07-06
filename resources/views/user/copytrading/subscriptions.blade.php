@extends('layouts.dash')

@section('content')
    <div class="relative min-h-screen dark:bg-dark bg-light pb-16">
        <!-- Header Section -->
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between flex-wrap gap-3">
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold dark:text-white text-dark">My Copy Trading</h1>
                    <p class="mt-2 text-xs sm:text-sm dark:text-gray-400 text-gray-600">Manage your expert trader subscriptions</p>
                </div>
                <a href="{{ route('copytrading') }}" class="flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm sm:text-base bg-primary hover:bg-primary/90 text-white transition-colors whitespace-nowrap flex-shrink-0">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Browse Traders</span>
                </a>
            </div>

            <!-- Feedback Messages -->
            <x-danger-alert />
            <x-success-alert />

            <!-- Subscriptions List -->
            <div class="space-y-4">
                @forelse ($subscriptions as $subscription)
                <div class="rounded-xl overflow-hidden border border-light-200/50 dark:border-dark-200/50 bg-gradient-to-br from-white to-white/80 dark:from-dark-50 dark:to-dark-50/80 shadow-lg">
                    <div class="p-5">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <!-- Trader Info -->
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    @if ($subscription->expertTrader->photo)
                                        <img src="{{ asset('storage/app/public/expert_traders/' . $subscription->expertTrader->photo) }}" 
                                             alt="{{ $subscription->expertTrader->name }}" 
                                             class="w-16 h-16 rounded-full object-cover border-2 border-primary shadow-lg">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-white text-xl font-bold border-2 border-primary shadow-lg">
                                            {{ substr($subscription->expertTrader->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white dark:border-dark
                                        {{ $subscription->status === 'active' ? 'bg-green-500' : '' }}
                                        {{ $subscription->status === 'cancelled' ? 'bg-red-500' : '' }}
                                        {{ $subscription->status === 'completed' ? 'bg-blue-500' : '' }}">
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg dark:text-white text-dark">{{ $subscription->expertTrader->name }}</h3>
                                    <p class="text-sm dark:text-gray-400 text-gray-600">
                                        Started {{ $subscription->start_date->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="px-4 py-2 rounded-lg text-sm font-medium
                                {{ $subscription->status === 'active' ? 'bg-green-500/20 text-green-600 dark:text-green-400' : '' }}
                                {{ $subscription->status === 'cancelled' ? 'bg-red-500/20 text-red-600 dark:text-red-400' : '' }}
                                {{ $subscription->status === 'completed' ? 'bg-blue-500/20 text-blue-600 dark:text-blue-400' : '' }}">
                                {{ ucfirst($subscription->status) }}
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                            <div class="rounded-lg p-3 bg-primary/10 border border-primary/20">
                                <p class="text-xs dark:text-gray-400 text-gray-600 mb-1">Invested</p>
                                <p class="text-base font-bold text-primary">{{ $settings->currency }}{{ number_format($subscription->amount_invested, 2) }}</p>
                            </div>
                            <div class="rounded-lg p-3 bg-green-500/10 border border-green-500/20">
                                <p class="text-xs dark:text-gray-400 text-gray-600 mb-1">Current Profit</p>
                                <p class="text-base font-bold text-green-600 dark:text-green-400">{{ $settings->currency }}{{ number_format($subscription->current_profit, 2) }}</p>
                            </div>
                            <div class="rounded-lg p-3 bg-secondary/10 border border-secondary/20">
                                <p class="text-xs dark:text-gray-400 text-gray-600 mb-1">Total Value</p>
                                <p class="text-base font-bold text-secondary">{{ $settings->currency }}{{ number_format($subscription->getTotalReturn(), 2) }}</p>
                            </div>
                            <div class="rounded-lg p-3 bg-blue-500/10 border border-blue-500/20">
                                <p class="text-xs dark:text-gray-400 text-gray-600 mb-1">Profit %</p>
                                <p class="text-base font-bold text-blue-600 dark:text-blue-400">{{ number_format($subscription->getProfitPercentage(), 2) }}%</p>
                            </div>
                        </div>

                        <!-- Duration Info -->
                        <div class="mt-4 flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-primary"></i>
                                <span class="dark:text-gray-300 text-gray-700">
                                    Ends {{ $subscription->end_date->format('M d, Y') }}
                                    @if ($subscription->status === 'active')
                                        ({{ $subscription->end_date->diffForHumans() }})
                                    @endif
                                </span>
                            </div>
                            @if ($subscription->last_profit_added)
                            <div class="flex items-center gap-2 text-xs dark:text-gray-400 text-gray-600">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                <span>Last profit: {{ $subscription->last_profit_added->diffForHumans() }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        @if ($subscription->status === 'active')
                        <div class="mt-4 pt-4 border-t border-light-200 dark:border-dark-200">
                            <form action="{{ route('copytrading.cancel', $subscription->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this subscription? Your invested amount and profits will be returned to your balance.')">
                                @csrf
                                <button type="submit" class="w-full md:w-auto px-6 py-2 rounded-lg border border-red-500 text-red-600 dark:text-red-400 hover:bg-red-500/10 transition-colors flex items-center justify-center gap-2">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    <span>Cancel Subscription</span>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-16 rounded-xl border border-light-200/50 dark:border-dark-200/50 bg-gradient-to-br from-white to-white/80 dark:from-dark-50 dark:to-dark-50/80">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-dark-100 mb-4">
                        <i data-lucide="copy" class="w-8 h-8 dark:text-gray-600 text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold dark:text-white text-dark mb-2">No Active Subscriptions</h3>
                    <p class="text-sm dark:text-gray-400 text-gray-600 mb-4">Start copying expert traders to grow your wealth automatically</p>
                    <a href="{{ route('copytrading') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-gradient-to-r from-primary to-secondary text-white font-medium hover:from-primary/90 hover:to-secondary/90 transition-all">
                        <i data-lucide="users-2" class="w-4 h-4"></i>
                        <span>Browse Expert Traders</span>
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
