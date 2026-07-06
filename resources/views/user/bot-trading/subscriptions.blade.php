@extends('layouts.dash')

@section('content')
    <div class="relative min-h-screen dark:bg-gradient-to-br dark:from-dark dark:via-dark-50 dark:to-dark bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-16">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-primary/5 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-secondary/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-primary flex items-center justify-center shadow-lg flex-shrink-0">
                            <i data-lucide="list" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold dark:text-white text-dark">
                                My Bot Subscriptions
                            </h1>
                            <p class="text-xs sm:text-sm dark:text-gray-400 text-gray-600 mt-1">
                                <i data-lucide="zap" class="w-3 h-3 inline mr-1"></i>
                                <span class="hidden sm:inline">Monitor your active bot investments and performance</span>
                                <span class="sm:hidden">Monitor bot performance</span>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('user.bot-trading.index') }}" class="flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm sm:text-base bg-secondary hover:bg-secondary/90 text-white transition-colors shadow-md whitespace-nowrap flex-shrink-0">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Browse Bots</span>
                    </a>
                </div>
            </div>

            <x-danger-alert />
            <x-success-alert />

            @if($subscriptions->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($subscriptions as $subscription)
                <div class="rounded-2xl overflow-hidden bg-white dark:bg-dark-50 border border-gray-200 dark:border-dark-200 shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="relative p-6 bg-gray-50 dark:bg-dark-100 border-b border-gray-200 dark:border-dark-200">
                        <div class="flex items-start gap-4">
                            <div class="relative flex-shrink-0">
                                @if($subscription->tradingBot->photo)
                                    <img src="{{ asset('storage/app/public/' . $subscription->tradingBot->photo) }}" 
                                         alt="{{ $subscription->tradingBot->name }}" 
                                         class="w-16 h-16 rounded-xl object-cover border-2 border-gray-200 dark:border-dark-200">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-primary flex items-center justify-center">
                                        <i data-lucide="bot" class="w-8 h-8 text-white"></i>
                                    </div>
                                @endif
                                @if($subscription->status == 'active')
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-dark-50 animate-pulse"></div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-lg dark:text-white text-dark truncate mb-1">
                                    {{ $subscription->tradingBot->name }}
                                </h3>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-semibold
                                        {{ $subscription->status == 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                        {{ $subscription->status == 'completed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                        {{ $subscription->status == 'cancelled' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                    <span class="text-xs dark:text-gray-400 text-gray-600">
                                        <i data-lucide="clock" class="w-3 h-3 inline"></i>
                                        {{ $subscription->duration }}
                                    </span>
                                </div>
                                <div class="text-xs dark:text-gray-400 text-gray-600">
                                    Started: {{ $subscription->start_date->format('M d, Y') }}
                                    @if($subscription->days_remaining !== null)
                                    • {{ $subscription->days_remaining }} days left
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-dark-100">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Invested</div>
                                <p class="text-xl font-bold dark:text-white text-dark">
                                    ${{ number_format($subscription->amount_invested, 2) }}
                                </p>
                            </div>
                            <div class="text-center p-4 rounded-xl {{ $subscription->current_profit >= 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Current Profit</div>
                                <p class="text-xl font-bold {{ $subscription->current_profit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $subscription->current_profit >= 0 ? '+' : '' }}${{ number_format($subscription->current_profit, 2) }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-5">
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <span class="text-sm dark:text-gray-400 text-gray-600">Total Return</span>
                                <span class="text-sm font-bold dark:text-white text-dark">
                                    ${{ number_format($subscription->total_return, 2) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <span class="text-sm dark:text-gray-400 text-gray-600">Profit Percentage</span>
                                <span class="text-sm font-bold {{ $subscription->profit_percentage >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $subscription->profit_percentage >= 0 ? '+' : '' }}{{ number_format($subscription->profit_percentage, 2) }}%
                                </span>
                            </div>
                            @if($subscription->last_trade_at)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <span class="text-sm dark:text-gray-400 text-gray-600">Last Trade</span>
                                <span class="text-sm dark:text-gray-300 text-gray-700">
                                    {{ $subscription->last_trade_at->diffForHumans() }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('user.bot-trading.show', $subscription->tradingBot) }}" 
                               class="flex-1 text-center px-4 py-2.5 rounded-lg font-medium text-sm bg-gray-100 dark:bg-dark-100 dark:text-white text-dark hover:bg-gray-200 dark:hover:bg-dark-200 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                View Bot
                            </a>
                            @if($subscription->status == 'active')
                            <form action="{{ route('user.bot-trading.cancel', $subscription) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Cancel this subscription? Your invested amount and current profit will be returned to your balance.');">
                                @csrf
                                <button type="submit" 
                                        class="px-4 py-2.5 rounded-lg font-medium text-sm bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                                    <i data-lucide="x-circle" class="w-4 h-4 inline mr-1"></i>
                                    Cancel
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $subscriptions->links() }}
            </div>
            @else
            <div class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-dark-100 flex items-center justify-center">
                    <i data-lucide="bot" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold dark:text-white text-dark mb-2">No Active Subscriptions</h3>
                <p class="text-sm dark:text-gray-400 text-gray-600 mb-6">Start investing with trading bots to automate your profits</p>
                <a href="{{ route('user.bot-trading.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-primary text-white font-semibold hover:bg-primary/90 transition-all shadow-lg">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Browse Trading Bots</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
    @endpush
@endsection
