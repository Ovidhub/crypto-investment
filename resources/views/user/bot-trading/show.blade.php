@extends('layouts.dash')

@section('content')
    <div class="relative min-h-screen dark:bg-gradient-to-br dark:from-dark dark:via-dark-50 dark:to-dark bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-16">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-primary/5 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-secondary/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="mb-8">
                <a href="{{ route('user.bot-trading.index') }}" class="inline-flex items-center gap-2 text-sm dark:text-gray-400 text-gray-600 hover:text-primary dark:hover:text-primary mb-4">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Back to Bots</span>
                </a>

                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        <div class="relative flex-shrink-0">
                            @if($bot->photo)
                                <img src="{{ asset('storage/app/public/' . $bot->photo) }}" 
                                     alt="{{ $bot->name }}" 
                                     class="w-20 h-20 rounded-2xl object-cover border-2 border-gray-200 dark:border-dark-200 shadow-lg">
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-primary flex items-center justify-center shadow-lg">
                                    <i data-lucide="bot" class="w-10 h-10 text-white"></i>
                                </div>
                            @endif
                            <div class="absolute -bottom-2 -right-2 px-2 py-1 rounded-lg bg-green-500 shadow-lg">
                                <i data-lucide="zap" class="w-3 h-3 text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold dark:text-white text-dark mb-2">{{ $bot->name }}</h1>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="inline-flex px-3 py-1 rounded-lg text-sm font-semibold bg-primary/10 text-primary dark:bg-primary/20">
                                    {{ $bot->strategy_type }}
                                </span>
                                <span class="text-sm dark:text-gray-400 text-gray-600">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 inline"></i>
                                    {{ $bot->trade_interval }}
                                </span>
                                <span class="text-sm dark:text-gray-400 text-gray-600">
                                    <i data-lucide="users" class="w-3.5 h-3.5 inline"></i>
                                    {{ ($bot->active_subscriptions_count ?? 0) + ($bot->fake_active_users ?? 0) }} active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-danger-alert />
            <x-success-alert />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-2xl bg-white dark:bg-dark-50 border border-gray-200 dark:border-dark-200 shadow-lg p-6">
                        <h2 class="text-xl font-bold dark:text-white text-dark mb-4">Performance Metrics</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 rounded-xl bg-secondary/10 dark:bg-secondary/20">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">ROI</div>
                                <p class="text-2xl font-bold text-secondary dark:text-secondary">{{ number_format($bot->roi_percentage, 1) }}%</p>
                            </div>
                            <div class="text-center p-4 rounded-xl bg-primary/10 dark:bg-primary/20">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Win Rate</div>
                                <p class="text-2xl font-bold text-primary dark:text-primary">{{ number_format($bot->win_rate, 0) }}%</p>
                            </div>
                            <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-dark-100">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Total Trades</div>
                                <p class="text-2xl font-bold dark:text-white text-dark">{{ number_format($bot->total_trades) }}</p>
                            </div>
                            <div class="text-center p-4 rounded-xl bg-secondary/10 dark:bg-secondary/20">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Total Profit</div>
                                <p class="text-2xl font-bold text-secondary dark:text-secondary">${{ number_format($bot->total_profit, 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white dark:bg-dark-50 border border-gray-200 dark:border-dark-200 shadow-lg p-6">
                        <h2 class="text-xl font-bold dark:text-white text-dark mb-4">About This Bot</h2>
                        <p class="text-sm dark:text-gray-300 text-gray-700 leading-relaxed mb-4">{{ $bot->description }}</p>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Min Investment</div>
                                <div class="font-semibold dark:text-white text-dark">${{ number_format($bot->min_capital) }}</div>
                            </div>
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Max Investment</div>
                                <div class="font-semibold dark:text-white text-dark">${{ number_format($bot->max_capital) }}</div>
                            </div>
                        </div>
                    </div>

                    @if($recentTrades->count() > 0)
                    <div class="rounded-2xl bg-white dark:bg-dark-50 border border-gray-200 dark:border-dark-200 shadow-lg p-6">
                        <h2 class="text-xl font-bold dark:text-white text-dark mb-4">Recent Trades</h2>
                        <div class="space-y-3">
                            @foreach($recentTrades->take(5) as $trade)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <div class="flex items-center gap-3">
                                    @if($trade->asset_logo)
                                    <img src="{{ $trade->asset_logo }}" alt="{{ $trade->asset_symbol }}" class="w-8 h-8 rounded-full">
                                    @endif
                                    <div>
                                        <div class="font-semibold dark:text-white text-dark">{{ $trade->asset_symbol }}</div>
                                        <div class="text-xs dark:text-gray-400 text-gray-600">{{ $trade->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold {{ $trade->outcome == 'win' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $trade->outcome == 'win' ? '+' : '' }}${{ number_format($trade->profit_loss, 2) }}
                                    </div>
                                    <div class="text-xs {{ $trade->outcome == 'win' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ number_format($trade->profit_percentage, 2) }}%
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="lg:col-span-1">
                    <div class="rounded-2xl bg-white dark:bg-dark-50 border border-gray-200 dark:border-dark-200 shadow-lg p-6 sticky top-6">
                        <h2 class="text-xl font-bold dark:text-white text-dark mb-4">Subscribe to Bot</h2>
                        <form action="{{ route('user.bot-trading.subscribe', $bot) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                                        Investment Amount
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                        <input type="number" 
                                               name="amount" 
                                               class="w-full pl-8 pr-4 py-3 rounded-lg border border-gray-200 dark:border-dark-200 bg-white dark:bg-dark-100 dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary" 
                                               min="{{ $bot->min_capital }}" 
                                               max="{{ $bot->max_capital }}" 
                                               step="0.01" 
                                               required>
                                    </div>
                                    <p class="text-xs dark:text-gray-400 text-gray-600 mt-1">
                                        Min: ${{ number_format($bot->min_capital) }} • Max: ${{ number_format($bot->max_capital) }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                                        Duration
                                    </label>
                                    <select name="duration" 
                                            class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-dark-200 bg-white dark:bg-dark-100 dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary" 
                                            required>
                                        <option value="1 Days">1 Day</option>
                                        <option value="2 Days">2 Days</option>
                                        <option value="3 Days">3 Days</option>
                                        <option value="4 Days">4 Days</option>
                                        <option value="5 Days">5 Days</option>
                                        <option value="6 Days">6 Days</option>
                                        <option value="7 Days">7 Days</option>
                                        <option value="30 Days">30 Days</option>
                                        <option value="60 Days">60 Days</option>
                                        <option value="90 Days">90 Days</option>
                                    </select>
                                </div>

                                <div class="p-4 rounded-lg bg-primary/10 dark:bg-primary/20 border border-primary/20 dark:border-primary/30">
                                    <div class="flex items-start gap-2">
                                        <i data-lucide="info" class="w-4 h-4 text-primary dark:text-primary mt-0.5"></i>
                                        <div class="text-xs dark:text-gray-300 text-gray-700">
                                            Your funds will be automatically traded by the bot. You can cancel anytime and receive your investment + current profit.
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" 
                                        class="w-full px-6 py-3 rounded-lg font-semibold text-white bg-primary hover:bg-primary/90 transition-all shadow-lg hover:shadow-xl">
                                    <i data-lucide="zap" class="w-4 h-4 inline mr-2"></i>
                                    Subscribe Now
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
