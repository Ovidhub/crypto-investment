@extends('layouts.dash')
@section('title', $title)
@section('content')
<!-- Alert Messages -->
<x-danger-alert />
<x-success-alert />


@if (!empty(Auth::user()->notify))
<div class="mb-6 relative rounded-xl border border-primary/20 dark:border-primary/30 bg-gradient-to-r from-primary/5 to-secondary/5 dark:from-primary/10 dark:to-secondary/10 backdrop-blur-sm p-4 shadow-lg animate-in slide-in-from-top" id="userNotification">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-primary/10 dark:bg-primary/20 flex items-center justify-center">
                <i data-lucide="bell" class="h-5 w-5 text-primary"></i>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <h4 class="font-semibold dark:text-white text-dark text-sm">Important Notification</h4>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                    <i data-lucide="info" class="h-3 w-3 mr-1"></i>
                    New
                </span>
            </div>
            <p class="text-sm dark:text-gray-300 text-gray-700 leading-relaxed">{{ Auth::user()->notify }}</p>
        </div>
        <button onclick="this.closest('#userNotification').style.display='none'" class="flex-shrink-0 ml-2 p-1.5 rounded-lg hover:bg-primary/10 dark:hover:bg-primary/20 transition-colors group">
            <i data-lucide="x" class="h-4 w-4 dark:text-gray-400 text-gray-600 group-hover:text-primary"></i>
        </button>
    </div>
</div>
@endif

<!-- Announcements (if enabled) -->
{{-- @if ($settings->enable_annoc == 'on' and !empty($settings->newupdate))
<div class="mb-6 relative rounded-xl border border-secondary/20 dark:border-secondary/30 bg-gradient-to-r from-secondary/5 to-accent/5 dark:from-secondary/10 dark:to-accent/10 backdrop-blur-sm p-4 shadow-lg animate-in slide-in-from-top" id="platformAnnouncement">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-secondary/10 dark:bg-secondary/20 flex items-center justify-center">
                <i data-lucide="megaphone" class="h-5 w-5 text-secondary"></i>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <h4 class="font-semibold dark:text-white text-dark text-sm">Platform Announcement</h4>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-secondary/10 text-secondary">
                    <i data-lucide="sparkles" class="h-3 w-3 mr-1"></i>
                    Latest
                </span>
            </div>
            <p class="text-sm dark:text-gray-300 text-gray-700 leading-relaxed">{{ $settings->newupdate }}</p>
        </div>
        <button onclick="this.closest('#platformAnnouncement').style.display='none'" class="flex-shrink-0 ml-2 p-1.5 rounded-lg hover:bg-secondary/10 dark:hover:bg-secondary/20 transition-colors group">
            <i data-lucide="x" class="h-4 w-4 dark:text-gray-400 text-gray-600 group-hover:text-secondary"></i>
        </button>
    </div>
</div>
@endif --}}

<div class="relative mb-8 overflow-hidden rounded-2xl border border-slate-200/60 dark:border-slate-800/50 bg-white dark:bg-[#0f172a] shadow-sm">
    
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/10 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-secondary/10 rounded-full blur-[100px]"></div>
        <div class="absolute inset-0 dark:opacity-[0.03] opacity-[0.05]" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 24px 24px;"></div>
    </div>

    <div class="relative z-10 p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight dark:text-white text-slate-900">
                        Hello, {{ Auth::user()->name }}
                    </h1>
                    <span class="px-2 py-1 rounded-md bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">Active</span>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium flex items-center gap-2 text-sm">
                    <i data-lucide="calendar" class="h-4 w-4 text-primary"></i>
                    {{ now()->format('l, F j, Y') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('wallet.connect') }}" class="group relative flex items-center gap-2 overflow-hidden rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-5 py-3 text-sm font-bold text-slate-900 dark:text-white shadow-lg transition-all hover:scale-[1.02] active:scale-95">
                    <i data-lucide="wallet" class="h-4 w-4 text-primary relative z-10"></i>
                    <span class="relative z-10">Connect Wallet</span>
                    <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </a>
            
                @if(Auth::user()->account_verify != 'Verified')    
                <a href="{{ route('kycform') }}" class="group relative flex items-center gap-2 overflow-hidden rounded-xl bg-slate-900 dark:bg-white px-5 py-3 text-sm font-bold text-white dark:text-slate-900 shadow-xl transition-all hover:scale-[1.02] active:scale-95">
                    <i data-lucide="shield-check" class="h-4 w-4 relative z-10"></i>
                    <span class="relative z-10">Verify Identity</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-secondary to-primary opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </a>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            
            <div class="xl:col-span-5 relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-2xl blur opacity-50 group-hover:opacity-100 transition duration-500"></div>
                <div class="relative h-full flex flex-col rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 shadow-sm">
                    <div class="flex justify-between items-start mb-8">
                        <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center">
                            <i data-lucide="wallet" class="h-6 w-6 text-primary"></i>
                        </div>
                        <button id="toggleBalanceVisibility" class="h-10 w-10 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                            <i data-lucide="eye" class="h-5 w-5" id="visibilityIcon"></i>
                        </button>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Net Portfolio Balance</p>
                        <div class="flex items-baseline gap-2">
                            <h3 id="balanceAmount" class="text-4xl sm:text-5xl font-black dark:text-white text-slate-900 tracking-tighter">
                                {{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2, '.', ',') }}
                            </h3>
                            <h3 id="hiddenBalance" class="text-4xl sm:text-5xl font-black dark:text-white text-slate-900 tracking-tighter hidden">••••••</h3>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-2 text-emerald-500 font-bold text-xs">
                        <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        Available for Withdrawal
                    </div>

                    <div class="mt-auto pt-8 grid grid-cols-2 gap-3">
                        <a href="{{ route('deposits') }}" class="flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3 text-xs font-black uppercase tracking-widest text-white hover:shadow-lg hover:shadow-primary/30 transition-all">
                            <i data-lucide="plus-circle" class="h-4 w-4"></i> Deposit
                        </a>
                        @if ($mod['investment'] || $mod['cryptoswap'])
                        <a href="{{ route('withdrawalsdeposits') }}" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-xs font-black uppercase tracking-widest dark:text-white text-slate-900 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                            <i data-lucide="arrow-up-right" class="h-4 w-4 text-primary"></i> Withdraw
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="xl:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                @if ($mod['investment'])
                <div class="group rounded-2xl bg-slate-50/50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-800/60 p-5 transition-all hover:bg-white dark:hover:bg-slate-900">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-10 w-10 rounded-lg bg-secondary/10 flex items-center justify-center">
                            <i data-lucide="trending-up" class="h-5 w-5 text-secondary"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Profit</p>
                            <h4 class="text-xl font-bold dark:text-white text-slate-900">{{ $settings->currency }}{{ number_format(Auth::user()->roi, 2, '.', ',') }}</h4>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/50">
                        @if($profitGrowth >= 0)
                            <span class="flex items-center gap-1.5 text-xs font-bold text-secondary">
                                <i data-lucide="arrow-up-right" class="h-3 w-3"></i> +{{ number_format(abs($profitGrowth), 1) }}% <span class="text-slate-400 font-medium">Growth</span>
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-xs font-bold text-rose-500">
                                <i data-lucide="arrow-down-right" class="h-3 w-3"></i> {{ number_format($profitGrowth, 1) }}% <span class="text-slate-400 font-medium">Performance</span>
                            </span>
                        @endif
                    </div>
                </div>
                @endif

                <div class="group rounded-2xl bg-slate-50/50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-800/60 p-5 transition-all hover:bg-white dark:hover:bg-slate-900">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-10 w-10 rounded-lg bg-accent/10 flex items-center justify-center">
                            <i data-lucide="gift" class="h-5 w-5 text-accent"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rewards</p>
                            <h4 class="text-xl font-bold dark:text-white text-slate-900">{{ $settings->currency }}{{ number_format(Auth::user()->bonus, 2, '.', ',') }}</h4>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/50">
                        <span class="flex items-center gap-1.5 text-xs font-bold text-accent">
                            <i data-lucide="award" class="h-3 w-3"></i> Rewards & Promotions
                        </span>
                    </div>
                </div>

                <div class="group rounded-2xl bg-slate-50/50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-800/60 p-5 transition-all hover:bg-white dark:hover:bg-slate-900">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <i data-lucide="arrow-down-left" class="h-5 w-5"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Deposit</p>
                            <h4 class="text-xl font-bold dark:text-white text-slate-900">{{ $settings->currency }}{{ number_format($deposited, 2, '.', ',') }}</h4>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/50 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Lifetime Deposit</span>
                        <i data-lucide="database" class="h-3 w-3 text-slate-300"></i>
                    </div>
                </div>

                @if ($mod['investment'] || $mod['cryptoswap'])
                <div class="group rounded-2xl bg-slate-50/50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-800/60 p-5 transition-all hover:bg-white dark:hover:bg-slate-900">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-10 w-10 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-500">
                            <i data-lucide="arrow-up-right" class="h-5 w-5"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Withdrawal</p>
                            <h4 class="text-xl font-bold dark:text-white text-slate-900">{{ $settings->currency }}{{ number_format($total_withdrawal, 2, '.', ',') }}</h4>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/50 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Settled Requests</span>
                        <i data-lucide="check-circle-2" class="h-3 w-3 text-slate-300"></i>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Left Column - Active Plans and Recent Transactions -->
    <div class="lg:col-span-2 space-y-6">
        @if($settings->copy_trading_auto_distribute == 'on' && count($featuredTraders) > 0)
        <div class="mb-8 w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 px-1">
                <div>
                    <h3 class="text-xl font-black dark:text-white text-slate-900 flex items-center gap-2 tracking-tight">
                        <span class="h-8 w-1 bg-primary rounded-full"></span>
                        Featured Expert Traders
                    </h3>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest">Copy profitable traders automatically</p>
                </div>
                <a href="{{ route('copytrading') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-bold hover:bg-primary hover:text-white transition-all group">
                    <span>View all</span>
                    <i data-lucide="chevron-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach($featuredTraders as $trader)
                <div class="group relative rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0f172a] p-5 shadow-sm hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 overflow-hidden">
                    <div class="absolute -top-10 -right-10 h-32 w-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
                    
                    <div class="relative flex items-center gap-4 mb-5">
                        @if($trader->photo)
                            <img src="{{ asset('storage/app/public/expert_traders/' . $trader->photo) }}" alt="{{ $trader->name }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 dark:ring-slate-800 shadow-md">
                        @else
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white text-lg font-black shadow-lg shadow-primary/20">
                                {{ strtoupper(substr($trader->name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="font-black dark:text-white text-slate-900 text-base truncate tracking-tight">{{ $trader->name }}</h4>
                            <div class="flex flex-wrap gap-1.5 mt-1.5">
                                <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-tighter
                                    {{ $trader->risk_level === 'Low' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
                                    {{ $trader->risk_level === 'Medium' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                    {{ $trader->risk_level === 'High' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' : '' }}">
                                    {{ $trader->risk_level }} Risk
                                </span>
                                @if($trader->win_rate >= 75)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-tighter bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    <i data-lucide="award" class="w-2.5 h-2.5"></i> Elite
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
        
                    <div class="grid grid-cols-3 gap-2 py-3 px-1 rounded-xl bg-slate-50 dark:bg-slate-800/50 mb-4 border border-slate-100 dark:border-slate-800">
                        <div class="text-center border-r border-slate-200 dark:border-slate-700 last:border-0">
                            <p class="text-[9px] font-black dark:text-slate-500 text-slate-400 uppercase tracking-widest mb-1">ROI</p>
                            <p class="text-sm font-black text-emerald-500 tracking-tight">{{ number_format($trader->roi_percentage, 1) }}%</p>
                        </div>
                        <div class="text-center border-r border-slate-200 dark:border-slate-700 last:border-0">
                            <p class="text-[9px] font-black dark:text-slate-500 text-slate-400 uppercase tracking-widest mb-1">Win Rate</p>
                            <p class="text-sm font-black text-blue-500 tracking-tight">{{ number_format($trader->win_rate, 0) }}%</p>
                        </div>
                        <div class="text-center">
                            <p class="text-[9px] font-black dark:text-slate-500 text-slate-400 uppercase tracking-widest mb-1">Trades</p>
                            <p class="text-sm font-black dark:text-white text-slate-900 tracking-tight">{{ $trader->active_trades }}</p>
                        </div>
                    </div>
        
                    <div class="flex items-center justify-between text-[11px] font-bold dark:text-slate-400 text-slate-500 mb-5 px-1">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-primary"></i>
                            <span>{{ $trader->preset_followers + $trader->total_followers }} following</span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-slate-100">{{ $trader->getAvailableSlots() }} slots left</span>
                    </div>
        
                    @php $isSubscribed = in_array($trader->id, $userSubscriptions ?? []); @endphp
        
                    @if($isSubscribed)
                        <div class="w-full py-3 rounded-xl bg-emerald-500 text-white text-xs font-black uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Currently Copying
                        </div>
                    @else
                        <a href="{{ route('copytrading') }}" class="w-full py-3 rounded-xl bg-slate-900 dark:bg-white dark:text-slate-900 text-white text-xs font-black uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 shadow-xl shadow-slate-900/10">
                            <i data-lucide="zap" class="w-4 h-4 text-primary"></i> Copy Trader
                        </a>
                    @endif
                </div>
                @endforeach
            </div>
        
            <div class="mt-6 flex items-start gap-4 p-4 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 dark:from-slate-800 dark:to-slate-900 border border-slate-700/50 shadow-lg">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-5 h-5 text-primary"></i>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-white tracking-tight">Automated Profit Distribution</h5>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">Expert trader returns are calculated and distributed automatically. Performance history is based on real-time market verified execution.</p>
                </div>
            </div>
        </div>
        @endif
        
        @if($settings->enable_stock_trading == 'on')
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0f172a] shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-black dark:text-white text-slate-900 flex items-center gap-2 tracking-tight">
                        <i data-lucide="bar-chart-3" class="h-5 w-5 text-secondary"></i>
                        Stock Trading Portfolio
                    </h3>
                </div>
                <a href="{{ route('stock-trading.index') }}" class="text-xs font-black uppercase tracking-widest text-primary hover:text-primary/80 flex items-center gap-1 transition-colors">
                    View Marketplace <i data-lucide="arrow-right-circle" class="h-4 w-4"></i>
                </a>
            </div>
        
            <div class="p-6">
                @php
                    $stockTrades = \App\Models\StockTrade::where('user_id', Auth::id())->where('status', 'completed')->get();
                    $stockPositions = [];
                    foreach($stockTrades as $trade) {
                        $symbol = $trade->stock_symbol;
                        if(!isset($stockPositions[$symbol])) { $stockPositions[$symbol] = ['quantity' => 0, 'total_cost' => 0]; }
                        if($trade->trade_type === 'buy') {
                            $stockPositions[$symbol]['quantity'] += $trade->quantity;
                            $stockPositions[$symbol]['total_cost'] += $trade->net_amount;
                        } else { $stockPositions[$symbol]['quantity'] -= $trade->quantity; }
                    }
                    $activePositions = collect($stockPositions)->filter(fn($pos) => $pos['quantity'] > 0);
                    $totalStockValue = 0; $totalInvestedStocks = 0;
                    foreach($activePositions as $symbol => $position) {
                        $stock = \App\Models\StockPrice::where('symbol', $symbol)->first();
                        if($stock && $position['quantity'] > 0) {
                            $totalStockValue += $position['quantity'] * $stock->price;
                            $totalInvestedStocks += $position['total_cost'];
                        }
                    }
                @endphp
        
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Portfolio Value</p>
                        <p class="text-xl font-black dark:text-white text-slate-900 tracking-tight">${{ number_format($totalStockValue, 2) }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Cost Basis</p>
                        <p class="text-xl font-black dark:text-white text-slate-900 tracking-tight">${{ number_format($totalInvestedStocks, 2) }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Profit/Loss</p>
                        <p class="text-xl font-black tracking-tight {{ ($totalStockValue - $totalInvestedStocks) >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                            {{ ($totalStockValue - $totalInvestedStocks) >= 0 ? '+' : '' }}${{ number_format($totalStockValue - $totalInvestedStocks, 2) }}
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Active Assets</p>
                        <p class="text-xl font-black dark:text-white text-slate-900 tracking-tight">{{ count($activePositions) }} Symbols</p>
                    </div>
                </div>
        
                @if(count($activePositions) > 0)
                <div class="space-y-3">
                    <h4 class="text-xs font-black dark:text-slate-500 text-slate-400 uppercase tracking-widest ml-1">Top Holdings</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($activePositions->take(3) as $symbol => $position)
                        @php
                            $stock = \App\Models\StockPrice::where('symbol', $symbol)->first();
                            if(!$stock) continue;
                            $currentValue = $position['quantity'] * $stock->price;
                            $profitLoss = $currentValue - $position['total_cost'];
                            $profitLossPercent = $position['total_cost'] > 0 ? ($profitLoss / $position['total_cost']) * 100 : 0;
                        @endphp
                        <div class="group flex items-center justify-between p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-all shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <img src="{{ $stock->logo_url }}" alt="{{ $stock->symbol }}" class="w-10 h-10 rounded-full ring-2 ring-slate-100 dark:ring-slate-800" onerror="this.src='data:image/svg+xml;base64,{{ base64_encode($stock->getPlaceholderSvg()) }}'">
                                    <div class="absolute -bottom-1 -right-1 h-3 w-3 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="font-black dark:text-white text-slate-900 tracking-tighter">{{ $stock->symbol }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">{{ number_format($position['quantity'], 2) }} units</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black dark:text-white text-slate-900">${{ number_format($currentValue, 2) }}</p>
                                <p class="text-[10px] font-black {{ $profitLoss >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $profitLoss >= 0 ? '▲' : '▼' }} {{ number_format($profitLossPercent, 2) }}%
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center py-12 px-6 rounded-2xl border-2 border-dashed border-slate-100 dark:border-slate-800">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center">
                        <i data-lucide="pie-chart" class="h-10 w-10 text-slate-300 dark:text-slate-600"></i>
                    </div>
                    <h5 class="text-lg font-black dark:text-white text-slate-900">Portfolio Empty</h5>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 mb-6 max-w-xs mx-auto">Start trading global stocks to build your wealth and track performance here.</p>
                    <a href="{{ route('stock-trading.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-white text-sm font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">
                        <i data-lucide="plus" class="w-4 h-4"></i> Open First Position
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($settings->enable_crypto_trading == 'on')
        <div class="mb-8 w-full">
            <div class="flex items-center justify-between mb-5 px-1">
                <div>
                    <h3 class="text-xl font-black dark:text-white text-slate-900 flex items-center gap-2 tracking-tight uppercase">
                        <i data-lucide="bitcoin" class="h-6 w-6 text-primary"></i>
                        Crypto Portfolio
                    </h3>
                    <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-[0.2em]">Digital Asset Performance</p>
                </div>
                <a href="{{ route('crypto-trading.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-bold hover:bg-primary hover:text-white transition-all group shadow-sm">
                    <span>View Trading</span>
                    <i data-lucide="chevron-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0f172a] p-6 shadow-sm overflow-hidden relative">
                @php
                    $cryptoTrades = \App\Models\CryptoTrade::where('user_id', Auth::id())->where('status', 'completed')->get();
                    $cryptoPositions = [];
                    foreach($cryptoTrades as $trade) {
                        $symbol = $trade->coin_symbol;
                        if(!isset($cryptoPositions[$symbol])) { $cryptoPositions[$symbol] = ['quantity' => 0, 'total_cost' => 0]; }
                        if($trade->trade_type === 'buy') {
                            $cryptoPositions[$symbol]['quantity'] += $trade->quantity;
                            $cryptoPositions[$symbol]['total_cost'] += $trade->net_amount;
                        } else { $cryptoPositions[$symbol]['quantity'] -= $trade->quantity; }
                    }
                    $activeCryptoPositions = collect($cryptoPositions)->filter(fn($pos) => $pos['quantity'] > 0);
                    $totalCryptoValue = 0; $totalInvestedCrypto = 0;
                    foreach($activeCryptoPositions as $symbol => $position) {
                        $crypto = \App\Models\CryptoPrice::where('coin_symbol', $symbol)->first();
                        if($crypto && $position['quantity'] > 0) {
                            $totalCryptoValue += $position['quantity'] * $crypto->price_usd;
                            $totalInvestedCrypto += $position['total_cost'];
                        }
                    }
                @endphp
        
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Portfolio Value</p>
                        <p class="text-xl font-black dark:text-white text-slate-900 tracking-tight">${{ number_format($totalCryptoValue, 2) }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Invested</p>
                        <p class="text-xl font-black dark:text-white text-slate-900 tracking-tight">${{ number_format($totalInvestedCrypto, 2) }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Realized P/L</p>
                        <p class="text-xl font-black tracking-tight {{ ($totalCryptoValue - $totalInvestedCrypto) >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                            {{ ($totalCryptoValue - $totalInvestedCrypto) >= 0 ? '+' : '' }}${{ number_format($totalCryptoValue - $totalInvestedCrypto, 2) }}
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Assets</p>
                        <p class="text-xl font-black dark:text-white text-slate-900 tracking-tight">{{ count($activeCryptoPositions) }} Coins</p>
                    </div>
                </div>
        
                @if(count($activeCryptoPositions) > 0)
                <h4 class="text-xs font-black dark:text-slate-500 text-slate-400 uppercase tracking-widest mb-4 ml-1">Live Positions</h4>
                <div class="space-y-3">
                    @foreach($activeCryptoPositions->take(3) as $symbol => $position)
                        @php
                            $crypto = \App\Models\CryptoPrice::where('coin_symbol', $symbol)->first();
                            if(!$crypto) continue;
                            $currentValue = $position['quantity'] * $crypto->price_usd;
                            $profitLoss = $currentValue - $position['total_cost'];
                            $profitLossPercent = $position['total_cost'] > 0 ? ($profitLoss / $position['total_cost']) * 100 : 0;
                        @endphp
                        <div class="group flex items-center justify-between p-4 rounded-2xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-0.5 ring-1 ring-slate-200 dark:ring-slate-700">
                                    @if($crypto->logo)
                                        <img src="{{ $crypto->logo }}" alt="{{ $crypto->coin_symbol }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                        <i data-lucide="bitcoin" class="h-6 w-6 text-primary"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-black dark:text-white text-slate-900 tracking-tight uppercase">{{ $crypto->coin_symbol }}</p>
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500">BEP-20</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-400 tracking-tighter">{{ number_format($position['quantity'], 8) }} {{ $crypto->coin_symbol }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black dark:text-white text-slate-900 tracking-tight">${{ number_format($currentValue, 2) }}</p>
                                <p class="text-[10px] font-black {{ $profitLoss >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $profitLoss >= 0 ? '▲ +' : '▼' }} {{ number_format($profitLossPercent, 2) }}%
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-10 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-2xl">
                    <div class="w-16 h-16 mx-auto mb-4 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center">
                        <i data-lucide="bitcoin" class="h-8 w-8 text-slate-300 dark:text-slate-600"></i>
                    </div>
                    <p class="text-sm font-black dark:text-white text-slate-900 uppercase tracking-widest">No Crypto Positions</p>
                    <p class="text-xs text-slate-500 mt-2 mb-6">Your active trading positions will appear here.</p>
                    <a href="{{ route('crypto-trading.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-xs font-black uppercase tracking-widest hover:scale-[1.02] transition-transform">
                        <i data-lucide="plus" class="w-4 h-4"></i> Start Trading
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <div class="mb-8 w-full">
            <div class="flex items-center justify-between mb-5 px-1">
                <div>
                    <h3 class="text-xl font-black dark:text-white text-slate-900 tracking-tight uppercase flex items-center gap-2">
                        <span class="h-2 w-2 bg-primary animate-pulse rounded-full"></span>
                        Market Overview
                    </h3>
                    <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest">Real-time Asset Pairs</p>
                </div>
                <div class="flex gap-2">
                    <button class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-primary transition-all shadow-sm active:scale-90" id="prev-pair">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    </button>
                    <button class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-primary transition-all shadow-sm active:scale-90" id="next-pair">
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>
        
            <div class="overflow-hidden">
                <div class="crypto-pairs-carousel w-full">
                    <div class="flex w-full gap-4 transition-transform duration-300" id="pairs-container">
                        </div>
                </div>
            </div>
        </div>
        
        @if ($mod['investment'])
        <div class="w-full">
            <div class="flex items-center justify-between mb-5 px-1">
                <h3 class="text-xl font-black dark:text-white text-slate-900 tracking-tight uppercase flex items-center gap-2">
                    Active Plans
                    <span class="text-xs bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-2 py-0.5 rounded-md">{{ $plans ? count($plans) : '0' }}</span>
                </h3>
                @if (count($plans) > 0)
                <a href="{{ route('myplans', 'yes') }}" class="text-xs font-black uppercase tracking-widest text-primary flex items-center gap-1 hover:underline">
                    Manage Plans <i data-lucide="external-link" class="h-4 w-4"></i>
                </a>
                @endif
            </div>
        
            <div class="grid grid-cols-1 gap-4">
                @forelse ($plans as $plan)
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0f172a] p-5 hover:shadow-xl transition-all duration-300">
                    <div class="flex flex-col md:flex-row md:items-center gap-6 relative z-10">
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center ring-1 ring-primary/30">
                                    <i data-lucide="activity" class="h-6 w-6 text-primary"></i>
                                </div>
                                <div>
                                    <h4 class="font-black dark:text-white text-slate-900 text-lg tracking-tight">{{ $plan->dplan->name }}</h4>
                                    <p class="text-sm font-black text-primary tracking-tighter">{{ $settings->currency }}{{ number_format($plan->amount) }} Principal</p>
                                </div>
                            </div>
                        </div>
        
                        <div class="flex items-center gap-8 px-6 border-l border-slate-100 dark:border-slate-800 hidden md:flex">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Started</p>
                                <p class="text-sm font-bold dark:text-white text-slate-900 tracking-tight">{{ $plan->created_at->format('M d, Y') }}</p>
                            </div>
                            <i data-lucide="arrow-right-circle" class="h-5 w-5 text-slate-300"></i>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Terminating</p>
                                <p class="text-sm font-bold dark:text-white text-slate-900 tracking-tight">{{ \Carbon\Carbon::parse($plan->expire_date)->format('M d, Y') }}</p>
                            </div>
                        </div>
        
                        <div class="flex items-center justify-between md:justify-end gap-6 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                            <div class="text-right">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 block md:hidden">Status</p>
                                @if ($plan->active == 'yes')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-500 bg-emerald-500/10 rounded-full border border-emerald-500/20">
                                        <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Active
                                    </span>
                                @elseif($plan->active == 'expired')
                                    <span class="inline-flex items-center px-3 py-1 text-[10px] font-black uppercase tracking-widest text-rose-500 bg-rose-500/10 rounded-full border border-rose-500/20">Expired</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-400/10 rounded-full border border-slate-400/20">Inactive</span>
                                @endif
                            </div>
                            <a href="{{ route('plandetails', $plan->id) }}" class="h-12 w-12 flex items-center justify-center rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-primary dark:hover:bg-primary dark:hover:text-white transition-all shadow-lg group-hover:scale-110">
                                <i data-lucide="arrow-up-right" class="h-5 w-5"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-between md:hidden py-3 border-t border-slate-100 dark:border-slate-800">
                         <p class="text-xs font-bold text-slate-400">Ends: <span class="text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($plan->expire_date)->format('M d, Y') }}</span></p>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-16 bg-white dark:bg-[#0f172a] rounded-2xl border-2 border-dashed border-slate-100 dark:border-slate-800">
                    <div class="h-20 w-20 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-6">
                        <i data-lucide="layout-grid" class="h-10 w-10 text-slate-300 dark:text-slate-600"></i>
                    </div>
                    <h4 class="text-lg font-black dark:text-white text-slate-900 uppercase tracking-tight">No Active Assets</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 mb-8 text-center max-w-sm px-6">Your capital is sitting idle. Allocate it to an investment plan to start generating daily yield.</p>
                    <a href="{{ route('mplans') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-primary text-white rounded-xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-primary/20 hover:scale-[1.05] transition-all">
                        <i data-lucide="shield-plus" class="h-4 w-4"></i> Buy a plan
                    </a>
                </div>
                @endforelse
            </div>
        </div>
        @endif
        
        <!--Recent Transactions-->
        <div class="w-full mb-8">
            <div class="flex items-center justify-between mb-5 px-1">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="h-4 w-1 bg-primary rounded-full"></span>
                        <h3 class="text-xl font-black dark:text-white text-slate-900 tracking-tighter uppercase">
                            Recent Transaction Log
                        </h3>
                    </div>
                    <p class="text-[9px] font-black text-slate-500 mt-1 uppercase tracking-[0.3em] flex items-center gap-2">
                        <span class="text-primary">●</span> System Status: Nominal <span class="dark:text-slate-700 text-slate-300">//</span> Records 0{{ count($t_history) }}
                    </p>
                </div>
                <a href="{{ route('accounthistory') }}" class="h-10 px-4 rounded-lg bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-primary dark:hover:bg-primary dark:hover:text-white transition-all shadow-lg">
                    View History
                    <i data-lucide="arrow-right-left" class="h-3 w-3"></i>
                </a>
            </div>
        
            <div class="relative group">
                <div class="absolute -top-[1px] -left-[1px] w-4 h-4 border-t-2 border-l-2 border-primary z-10"></div>
                <div class="absolute -bottom-[1px] -right-[1px] w-4 h-4 border-b-2 border-r-2 border-primary z-10"></div>
        
                <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0f172a] overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800">
                                    <th class="text-left p-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Index / Date</th>
                                    <th class="text-left p-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Details</th>
                                    <th class="text-right p-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @forelse ($t_history as $index => $item)
                                @php
                                    $isCredit = stripos($item->type, 'deposit') !== false || stripos($item->type, 'profit') !== false || stripos($item->type, 'bonus') !== false;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-primary/5 transition-all group">
                                    <td class="p-4 whitespace-nowrap border-r border-slate-100 dark:border-slate-800/50">
                                        <div class="flex items-center gap-3">
                                            <span class="font-mono text-[10px] text-slate-400">0{{ $index + 1 }}</span>
                                            <div>
                                                <p class="text-xs font-black dark:text-white text-slate-900 uppercase tracking-tighter">{{ $item->created_at->format('d M y') }}</p>
                                                <p class="font-mono text-[9px] text-slate-500">{{ $item->created_at->format('H:i:s') }}</p>
                                            </div>
                                        </div>
                                    </td>
        
                                    <td class="p-4">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                <div class="h-9 w-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-700 group-hover:border-primary/50 transition-colors">
                                                    <i data-lucide="{{ $isCredit ? 'arrow-down-to-dot' : 'arrow-up-from-dot' }}" class="h-4 w-4 {{ $isCredit ? 'text-emerald-500' : 'text-primary' }}"></i>
                                                </div>
                                                <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isCredit ? 'bg-emerald-400' : 'bg-primary' }} opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $isCredit ? 'bg-emerald-500' : 'bg-primary' }}"></span>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm font-black dark:text-slate-200 text-slate-800 uppercase tracking-tight">{{ $item->type }}</p>
                                                    <span class="text-[8px] font-black px-1 border border-slate-200 dark:border-slate-700 rounded text-slate-400">STABLE</span>
                                                </div>
                                                <p class="text-[10px] font-bold text-slate-500/80 italic tracking-tight">{{ $item->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </td>
        
                                    <td class="p-4 text-right bg-slate-50/50 dark:bg-slate-900/30">
                                        <p class="text-lg font-black tracking-tighter dark:text-white text-slate-900 font-mono">
                                            {{ $settings->currency }}{{ number_format($item->amount, 2) }}
                                        </p>
                                        <p class="text-[9px] font-black uppercase tracking-widest {{ $isCredit ? 'text-emerald-500' : 'text-slate-500' }}">
                                            {{ $isCredit ? 'Settlement Inbound' : 'Settlement Outbound' }}
                                        </p>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="p-20 text-center">
                                        <i data-lucide="box-select" class="h-12 w-12 text-slate-200 dark:text-slate-800 mx-auto mb-4"></i>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">No data sequences found in ledger</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Right Column - Profile and Referral -->
    <div class="space-y-6">
    <div class="relative dark:bg-[#0f172a] bg-white border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm group">
        <div class="h-1.5 w-full bg-gradient-to-r from-primary via-secondary to-tertiary"></div>
        
        <div class="p-6 text-center">
            <div class="relative inline-flex mb-4">
                <div class="absolute inset-0 rounded-full border-2 border-dashed border-primary/30 animate-[spin_10s_linear_infinite]"></div>
                
                <div class="relative h-20 w-20 sm:h-24 sm:w-24 rounded-full bg-slate-100 dark:bg-slate-800 border-4 border-white dark:border-[#0f172a] flex items-center justify-center shadow-xl">
                    <span class="text-2xl font-black text-primary tracking-tighter">{{ substr(Auth::user()->name, 0, 2) }}</span>
                    
                    @if (Auth::user()->account_verify == 'Verified')
                    <div class="absolute bottom-0 right-0 bg-secondary h-7 w-7 rounded-full flex items-center justify-center border-4 border-white dark:border-[#0f172a] shadow-lg">
                        <i data-lucide="shield-check" class="h-3.5 w-3.5 text-white"></i>
                    </div>
                    @endif
                </div>
            </div>
            
            <h4 class="font-black dark:text-white text-slate-900 text-lg tracking-tight">{{ Auth::user()->name }}</h4>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 mt-2">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                <p class="text-[10px] font-bold dark:text-slate-400 text-slate-600 uppercase tracking-widest">Member since {{ Auth::user()->created_at->format('M Y') }}</p>
            </div>
        </div>

        <div class="px-6 pb-6">
            <div class="grid gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black dark:text-slate-500 text-slate-400 uppercase tracking-wider">Account Balance</span>
                    <span class="text-sm font-black dark:text-white text-slate-900 font-mono">{{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2, '.', ',') }}</span>
                </div>
                <div class="h-[1px] w-full bg-slate-200 dark:bg-slate-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black dark:text-slate-500 text-slate-400 uppercase tracking-wider">Bonus</span>
                    <span class="text-sm font-black dark:text-white text-slate-900 font-mono">{{ $settings->currency }}{{ number_format(Auth::user()->bonus, 2, '.', ',') }}</span>
                </div>
                <div class="h-[1px] w-full bg-slate-200 dark:bg-slate-800"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black dark:text-slate-500 text-slate-400 uppercase tracking-wider">Referral Bonus</span>
                    <span class="text-sm font-black dark:text-white text-slate-900 font-mono">{{ $settings->currency }}{{ number_format(Auth::user()->ref_bonus, 2, '.', ',') }}</span>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('deposits') }}" class="flex-1 flex items-center justify-center gap-2 py-3 bg-primary hover:bg-primary/90 rounded-xl text-white text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-primary/20">
                    <i data-lucide="plus-circle" class="h-4 w-4"></i>
                    <span>Deposit</span>
                </a>
                @if ($mod['investment'] || $mod['cryptoswap'])
                <a href="{{ route('withdrawalsdeposits') }}" class="flex-1 flex items-center justify-center gap-2 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:opacity-90 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-slate-900/10">
                    <i data-lucide="arrow-up-right" class="h-4 w-4"></i>
                    <span>Withdraw</span>
                </a>
                @endif
            </div>

            <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between bg-white dark:bg-slate-900 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                    <span class="text-[10px] font-black dark:text-slate-500 text-slate-400 uppercase tracking-[0.2em]">Account Status</span>
                    @if (Auth::user()->account_verify == 'Verified')
                    <span class="inline-flex items-center text-[10px] font-black text-secondary uppercase tracking-widest">
                        <i data-lucide="check-circle-2" class="h-3.5 w-3.5 mr-1.5"></i> Verified
                    </span>
                    @else
                    <span class="inline-flex items-center text-[10px] font-black text-primary uppercase tracking-widest">
                        <i data-lucide="alert-octagon" class="h-3.5 w-3.5 mr-1.5"></i> Unverified
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="dark:bg-[#0f172a] bg-white border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-black dark:text-white text-slate-900 text-xs uppercase tracking-[0.2em]">Refer & Earn</h3>
            <a href="{{ route('referuser') }}" class="h-7 w-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
                <i data-lucide="chevron-right" class="h-4 w-4"></i>
            </a>
        </div>
        <div class="p-6">
            <div class="flex gap-4 mb-6">
                <div class="h-12 w-12 shrink-0 rounded-2xl bg-accent/10 border border-accent/20 flex items-center justify-center">
                    <i data-lucide="users-2" class="h-6 w-6 text-accent"></i>
                </div>
                <div>
                    <h4 class="font-black dark:text-white text-slate-900 text-sm uppercase tracking-tight leading-tight">Earn Through Referrals</h4>
                    <p class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-tighter leading-relaxed">Earn commission when someone signs up using your link</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Your Referral Link</label>
                <div class="group relative flex items-center p-1.5 dark:bg-slate-900 bg-slate-50 border border-slate-200 dark:border-slate-800 rounded-xl focus-within:border-primary transition-all">
                    <input type="text" id="reflink" value="{{ Auth::user()->ref_link }}" readonly class="flex-1 bg-transparent border-0 px-3 text-xs font-mono dark:text-slate-300 text-slate-700 focus:outline-none truncate" />
                    <button onclick="copyRefLink()" class="h-9 px-4 bg-slate-900 dark:bg-slate-800 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-primary transition-all flex items-center gap-2">
                        <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                        <span>Copy</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Total Referrals</p>
                    <p class="text-2xl font-black dark:text-white text-slate-900 font-mono tracking-tighter">{{ Auth::user()->ref_count ?? 0 }}</p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Earnings</p>
                    <p class="text-2xl font-black dark:text-white text-slate-900 font-mono tracking-tighter">{{ $settings->currency }}{{ number_format(Auth::user()->ref_bonus, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="dark:bg-[#0f172a] bg-white border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h3 class="font-black dark:text-white text-slate-900 text-xs uppercase tracking-[0.2em] mb-6">Platform Stats</h3>
        
        <div class="space-y-5">
            <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-black dark:text-slate-400 text-slate-500 uppercase tracking-widest">Platform Activity</span>
                    <span class="flex items-center gap-1.5 text-[10px] font-black text-secondary uppercase tracking-widest">
                        <span class="h-1.5 w-1.5 rounded-full bg-secondary animate-pulse"></span> Active
                    </span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-secondary to-primary h-full rounded-full" style="width: 85%"></div>
                </div>
            </div>

            <div class="grid gap-4">
                <div class="flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-tertiary/10 flex items-center justify-center ring-1 ring-tertiary/20">
                            <i data-lucide="users" class="h-4 w-4 text-tertiary"></i>
                        </div>
                        <span class="text-[11px] font-black dark:text-slate-400 text-slate-500 uppercase tracking-widest">Total Users</span>
                    </div>
                    <span class="text-sm font-black dark:text-white text-slate-900 font-mono">12,458+</span>
                </div>

                <div class="flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-secondary/10 flex items-center justify-center ring-1 ring-secondary/20">
                            <i data-lucide="briefcase" class="h-4 w-4 text-secondary"></i>
                        </div>
                        <span class="text-[11px] font-black dark:text-slate-400 text-slate-500 uppercase tracking-widest">Total Investments</span>
                    </div>
                    <span class="text-sm font-black dark:text-white text-slate-900 font-mono">$9.5M+</span>
                </div>

                <div class="flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-primary/10 flex items-center justify-center ring-1 ring-primary/20">
                            <i data-lucide="activity" class="h-4 w-4 text-primary"></i>
                        </div>
                        <span class="text-[11px] font-black dark:text-slate-400 text-slate-500 uppercase tracking-widest">Server Uptime</span>
                    </div>
                    <span class="text-sm font-black dark:text-white text-slate-900 font-mono">99.9%</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function copyRefLink() {
    var copyText = document.getElementById("reflink");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");

    // Show a toast notification
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-secondary text-white py-2 px-4 rounded-md shadow-lg flex items-center animate-in slide-in-from-bottom z-50';
    toast.innerHTML = `
        <i data-lucide="check-circle" class="h-4 w-4 mr-2"></i>
        <span class="text-sm">Referral link copied!</span>
    `;
    document.body.appendChild(toast);
    lucide.createIcons();

    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Fetch crypto data from CoinGecko and build carousel cards
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('pairs-container');
    const prevBtn = document.getElementById('prev-pair');
    const nextBtn = document.getElementById('next-pair');
    let position = 0;
    let itemWidth = 0;
    let maxPosition = 0;
    let touchStartX = 0;
    let touchEndX = 0;

    // Fetch data from CoinGecko API (top 4 coins)
    fetch('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=4&page=1&sparkline=false')
        .then(response => response.json())
        .then(data => {
            let html = '';
            data.forEach(coin => {
                const isUp = coin.price_change_24h >= 0;
                html += `
                <div class="min-w-[100%] xs:min-w-[85%] sm:min-w-[240px] sm:w-auto rounded-xl dark:border-dark-100 border-light-200 border dark:bg-dark-100/50 bg-light-200/50 p-4 flex-shrink-0">
                    <div class="mb-3 flex items-center gap-2">
                        <img src="${coin.image}" alt="${coin.name} Logo" class="h-8 w-8 rounded-full" />
                        <div>
                            <div class="font-medium dark:text-white text-dark">${coin.name}</div>
                            <div class="text-xs dark:text-gray-400 text-gray-600">${coin.symbol.toUpperCase()}</div>
                        </div>
                        <span class="ml-auto ${isUp ? 'bg-secondary' : 'bg-primary'} text-white text-xs px-2 py-1 rounded-full flex items-center">
                            <i data-lucide="${isUp ? 'arrow-up' : 'arrow-down'}" class="h-3 w-3 mr-1"></i>
                            ${coin.price_change_percentage_24h.toFixed(2)}%
                        </span>
                    </div>
                    <div class="mb-2">
                        <div class="flex justify-between">
                            <span class="dark:text-gray-400 text-gray-600 text-xs">Price</span>
                            <span class="dark:text-gray-400 text-gray-600 text-xs">24h Change</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium dark:text-white text-dark">$${coin.current_price.toFixed(2)}</span>
                            <span class="${isUp ? 'text-secondary' : 'text-primary'}">
                                ${isUp ? '+' : '-'}$${Math.abs(coin.price_change_24h).toFixed(2)}
                            </span>
                        </div>
                    </div>
                    <div class="h-12 w-full">
                        <!-- Fallback sparkline SVG -->
                        <svg viewBox="0 0 100 30" preserveAspectRatio="none" class="w-full h-full">
                            <path d="M0,15 C20,5 40,25 60,15 S80,5 100,15" stroke="${isUp ? '#4A9D7F' : '#C88B65'}" stroke-width="1.5" fill="none"></path>
                        </svg>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
            lucide.createIcons();
            calculateSizes(); // recalc sizes after content load
        })
        .catch(error => {
            console.error('Error fetching crypto data:', error);
        });

    // Carousel navigation functions
    function calculateSizes() {
        const containerWidth = container.parentElement.offsetWidth;
        const isMobile = window.innerWidth < 640;
        if (isMobile) {
            const firstItem = container.children[0];
            if (firstItem) {
                itemWidth = firstItem.offsetWidth + 16;
            }
        } else {
            itemWidth = 240 + 16;
        }
        const totalItems = container.children.length;
        const visibleItems = Math.max(1, Math.floor(containerWidth / itemWidth));
        maxPosition = Math.max(0, totalItems - visibleItems);
        if (position > maxPosition) {
            position = maxPosition;
            updatePosition();
        }
    }

    function updatePosition() {
        container.style.transform = `translateX(-${position * itemWidth}px)`;
    }

    prevBtn.addEventListener('click', function() {
        if (position > 0) {
            position--;
            updatePosition();
        }
    });

    nextBtn.addEventListener('click', function() {
        if (position < maxPosition) {
            position++;
            updatePosition();
        }
    });

    container.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });

    container.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].clientX;
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchStartX - touchEndX;
        if (swipeDistance > swipeThreshold && position < maxPosition) {
            position++;
            updatePosition();
        } else if (swipeDistance < -swipeThreshold && position > 0) {
            position--;
            updatePosition();
        }
    }

    window.addEventListener('resize', function() {
        calculateSizes();
        updatePosition();
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Balance visibility toggle functionality
        const toggleBtn = document.getElementById('toggleBalanceVisibility');
        const balanceAmount = document.getElementById('balanceAmount');
        const hiddenBalance = document.getElementById('hiddenBalance');
        const visibilityIcon = document.getElementById('visibilityIcon');

        // Check if balance visibility preference exists in localStorage
        const balanceVisible = localStorage.getItem('balanceVisible') !== 'false';

        // Set initial state
        setBalanceVisibility(balanceVisible);

        toggleBtn.addEventListener('click', function() {
            // Toggle visibility
            const isCurrentlyVisible = balanceAmount.classList.contains('hidden') === false;
            setBalanceVisibility(!isCurrentlyVisible);

            // Save preference
            localStorage.setItem('balanceVisible', !isCurrentlyVisible);
        });

        function setBalanceVisibility(visible) {
            if (visible) {
                balanceAmount.classList.remove('hidden');
                hiddenBalance.classList.add('hidden');
                visibilityIcon.setAttribute('data-lucide', 'eye');
            } else {
                balanceAmount.classList.add('hidden');
                hiddenBalance.classList.remove('hidden');
                visibilityIcon.setAttribute('data-lucide', 'eye-off');
            }

            // Re-initialize the icon
            if (typeof lucide !== 'undefined') {
                lucide.createIcons({
                    elements: [visibilityIcon]
                });
            }
        }
    });
</script>
@endsection
