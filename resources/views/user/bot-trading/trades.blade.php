@extends('layouts.dash')

@section('content')
    <div class="relative min-h-screen dark:bg-gradient-to-br dark:from-dark dark:via-dark-50 dark:to-dark bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-16">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-primary/5 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-secondary/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center shadow-lg">
                            <i data-lucide="activity" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold bg-gradient-to-r from-primary via-secondary to-primary bg-clip-text text-transparent">
                                My Trade History
                            </h1>
                            <p class="text-sm dark:text-gray-400 text-gray-600 mt-1">
                                <i data-lucide="trending-up" class="w-3 h-3 inline mr-1"></i>
                                Track all your bot trading activities
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('user.bot-trading.subscriptions') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-secondary hover:bg-secondary/90 text-white transition-colors shadow-md">
                        <i data-lucide="list" class="w-4 h-4"></i>
                        <span>Subscriptions</span>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="rounded-2xl p-4 bg-white/50 dark:bg-dark-50/50 backdrop-blur-xl border border-primary/10 dark:border-primary/20">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                <i data-lucide="bar-chart" class="w-4 h-4 text-primary"></i>
                            </div>
                            <span class="text-xs dark:text-gray-400 text-gray-600 font-medium">Total Trades</span>
                        </div>
                        <p class="text-2xl font-bold dark:text-white text-dark">{{ $trades->total() }}</p>
                    </div>
                    <div class="rounded-2xl p-4 bg-white/50 dark:bg-dark-50/50 backdrop-blur-xl border border-secondary/10 dark:border-secondary/20">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-4 h-4 text-secondary"></i>
                            </div>
                            <span class="text-xs dark:text-gray-400 text-gray-600 font-medium">Wins</span>
                        </div>
                        <p class="text-2xl font-bold text-secondary dark:text-secondary">{{ $totalWins }}</p>
                    </div>
                    <div class="rounded-2xl p-4 bg-white/50 dark:bg-dark-50/50 backdrop-blur-xl border border-red-500/10 dark:border-red-500/20">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
                                <i data-lucide="x-circle" class="w-4 h-4 text-red-500"></i>
                            </div>
                            <span class="text-xs dark:text-gray-400 text-gray-600 font-medium">Losses</span>
                        </div>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $totalLosses }}</p>
                    </div>
                    <div class="rounded-2xl p-4 bg-white/50 dark:bg-dark-50/50 backdrop-blur-xl border border-primary/10 dark:border-primary/20">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                <i data-lucide="dollar-sign" class="w-4 h-4 text-primary"></i>
                            </div>
                            <span class="text-xs dark:text-gray-400 text-gray-600 font-medium">Net Profit</span>
                        </div>
                        <p class="text-2xl font-bold {{ $totalProfit >= 0 ? 'text-secondary dark:text-secondary' : 'text-red-600 dark:text-red-400' }}">
                            {{ $totalProfit >= 0 ? '+' : '' }}${{ number_format($totalProfit, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <x-danger-alert />
            <x-success-alert />

            @if($trades->count() > 0)
            <div class="space-y-4">
                @foreach($trades as $trade)
                <div class="rounded-2xl overflow-hidden bg-white dark:bg-dark-50 border border-gray-200 dark:border-dark-200 shadow-md hover:shadow-lg transition-all">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-start gap-4">
                                @if($trade->logo)
                                <img src="{{ $trade->logo }}" alt="{{ $trade->asset_symbol }}" class="w-12 h-12 rounded-lg">
                                @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-dark-100 flex items-center justify-center">
                                    <i data-lucide="coins" class="w-6 h-6 text-gray-400"></i>
                                </div>
                                @endif
                                <div>
                                    <h3 class="font-bold text-lg dark:text-white text-dark mb-1">{{ $trade->asset_name }}</h3>
                                    <div class="flex items-center gap-3 text-sm">
                                        <span class="dark:text-gray-400 text-gray-600">{{ $trade->asset_symbol }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded {{ $trade->outcome == 'win' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                            {{ ucfirst($trade->outcome) }}
                                        </span>
                                        <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-dark-100 dark:text-gray-400 text-gray-600">
                                            {{ ucfirst($trade->trade_type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold {{ $trade->profit_loss >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $trade->profit_loss >= 0 ? '+' : '' }}${{ number_format($trade->profit_loss, 2) }}
                                </div>
                                <div class="text-sm {{ $trade->profit_loss >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $trade->profit_percentage >= 0 ? '+' : '' }}{{ number_format($trade->profit_percentage, 2) }}%
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Entry Price</div>
                                <div class="font-semibold dark:text-white text-dark">${{ number_format($trade->entry_price, 2) }}</div>
                            </div>
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Exit Price</div>
                                <div class="font-semibold dark:text-white text-dark">${{ number_format($trade->exit_price, 2) }}</div>
                            </div>
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Amount</div>
                                <div class="font-semibold dark:text-white text-dark">{{ number_format($trade->amount, 6) }}</div>
                            </div>
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-dark-100">
                                <div class="text-xs dark:text-gray-400 text-gray-600 mb-1">Bot</div>
                                <div class="font-semibold dark:text-white text-dark truncate">{{ $trade->tradingBot->name }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs dark:text-gray-400 text-gray-600">
                            <span>
                                <i data-lucide="clock" class="w-3 h-3 inline"></i>
                                {{ $trade->created_at->format('M d, Y \a\t h:i A') }}
                            </span>
                            @if($trade->duration)
                            <span>Duration: {{ $trade->duration }} hours</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $trades->links() }}
            </div>
            @else
            <div class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-dark-100 flex items-center justify-center">
                    <i data-lucide="activity" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold dark:text-white text-dark mb-2">No Trades Yet</h3>
                <p class="text-sm dark:text-gray-400 text-gray-600 mb-6">Subscribe to a trading bot to start automated trading</p>
                <a href="{{ route('user.bot-trading.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-gradient-to-r from-primary to-secondary text-white font-semibold hover:from-primary/90 hover:to-secondary/90 transition-all shadow-lg">
                    <i data-lucide="bot" class="w-4 h-4"></i>
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
