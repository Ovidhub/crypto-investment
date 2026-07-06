@extends('layouts.dash')

@section('title', 'Crypto Portfolio')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div class="min-w-0 flex-1">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">Crypto Portfolio</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">Overview of your cryptocurrency holdings</p>
        </div>
        <a href="{{ route('crypto-trading.index') }}" class="bg-primary-500 hover:bg-primary-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm sm:text-base flex items-center space-x-2 transition-colors duration-150 whitespace-nowrap flex-shrink-0">
            <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            <span>Back to Trading</span>
        </a>
    </div>

    <!-- Portfolio Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider">Total Value</p>
                    <p class="text-2xl font-bold text-white mt-1">${{ number_format($totalPortfolioValue, 2) }}</p>
                </div>
                <div class="bg-primary-500/10 p-3 rounded-lg">
                    <i data-lucide="trending-up" class="w-6 h-6 text-primary-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider">Total Invested</p>
                    <p class="text-2xl font-bold text-white mt-1">${{ number_format(collect($holdings)->sum('total_cost'), 2) }}</p>
                </div>
                <div class="bg-blue-500/10 p-3 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider">Total P/L</p>
                    @php
                        $totalPL = collect($holdings)->sum('profit_loss');
                    @endphp
                    <p class="text-2xl font-bold {{ $totalPL >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                        {{ $totalPL >= 0 ? '+' : '' }}${{ number_format($totalPL, 2) }}
                    </p>
                    @php
                        $totalInvested = collect($holdings)->sum('total_cost');
                        $totalROI = $totalInvested > 0 ? ($totalPL / $totalInvested) * 100 : 0;
                    @endphp
                    <p class="text-sm {{ $totalROI >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                        {{ number_format($totalROI, 2) }}% ROI
                    </p>
                </div>
                <div class="bg-{{ $totalPL >= 0 ? 'green' : 'red' }}-500/10 p-3 rounded-lg">
                    <i data-lucide="{{ $totalPL >= 0 ? 'arrow-up' : 'arrow-down' }}" class="w-6 h-6 text-{{ $totalPL >= 0 ? 'green' : 'red' }}-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm uppercase tracking-wider">Holdings</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ count($holdings) }}</p>
                    <p class="text-sm text-gray-400 mt-1">Cryptocurrencies</p>
                </div>
                <div class="bg-purple-500/10 p-3 rounded-lg">
                    <i data-lucide="coins" class="w-6 h-6 text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Holdings Table -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-5 shadow-xl">
        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
            <i data-lucide="briefcase" class="w-5 h-5 mr-2 text-primary-500"></i>
            Your Holdings
        </h2>
        
        @if(empty($holdings))
        <div class="text-center py-12">
            <i data-lucide="inbox" class="w-16 h-16 text-gray-600 mx-auto mb-4"></i>
            <p class="text-gray-400 text-lg mb-4">No holdings yet</p>
            <a href="{{ route('crypto-trading.index') }}" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors duration-150">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Start Trading</span>
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left text-gray-400 text-sm font-semibold py-3 px-3">Cryptocurrency</th>
                        <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Holdings</th>
                        <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Avg Cost</th>
                        <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Current Price</th>
                        <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Current Value</th>
                        <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">P/L</th>
                        <th class="text-right text-gray-400 text-sm font-semibold py-3 px-3">Concentration</th>
                        <th class="text-center text-gray-400 text-sm font-semibold py-3 px-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($holdings as $holding)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/30 transition-colors duration-150">
                        <td class="py-4 px-3">
                            <div class="flex items-center space-x-3">
                                @if($holding['crypto']->logo)
                                <img src="{{ $holding['crypto']->logo }}" alt="{{ $holding['crypto']->name }}" class="w-10 h-10 rounded-full">
                                @else
                                <div class="w-10 h-10 rounded-full bg-primary-500/10 flex items-center justify-center">
                                    <i data-lucide="bitcoin" class="w-6 h-6 text-primary-500"></i>
                                </div>
                                @endif
                                <div>
                                    <p class="text-white font-semibold">{{ $holding['crypto']->name }}</p>
                                    <p class="text-gray-400 text-sm">{{ $holding['crypto']->coin_symbol }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-3 text-right">
                            <p class="text-white font-semibold">{{ number_format($holding['quantity'], 8) }}</p>
                            <p class="text-gray-400 text-sm">{{ $holding['crypto']->coin_symbol }}</p>
                        </td>
                        <td class="py-4 px-3 text-right text-white">${{ number_format($holding['avg_cost'], 2) }}</td>
                        <td class="py-4 px-3 text-right">
                            <p class="text-white font-semibold">${{ number_format($holding['crypto']->price_usd, 2) }}</p>
                            <p class="text-sm {{ $holding['crypto']->percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $holding['crypto']->percent_change_24h >= 0 ? '+' : '' }}{{ number_format($holding['crypto']->percent_change_24h, 2) }}%
                            </p>
                        </td>
                        <td class="py-4 px-3 text-right text-white font-semibold">${{ number_format($holding['current_value'], 2) }}</td>
                        <td class="py-4 px-3 text-right">
                            <p class="font-semibold {{ $holding['profit_loss'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $holding['profit_loss'] >= 0 ? '+' : '' }}${{ number_format($holding['profit_loss'], 2) }}
                            </p>
                            <p class="text-sm {{ $holding['profit_loss_percent'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $holding['profit_loss_percent'] >= 0 ? '+' : '' }}{{ number_format($holding['profit_loss_percent'], 2) }}%
                            </p>
                        </td>
                        <td class="py-4 px-3 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <div class="w-16 bg-gray-700 rounded-full h-2">
                                    <div class="bg-primary-500 h-2 rounded-full" style="width: {{ min($holding['concentration'], 100) }}%"></div>
                                </div>
                                <span class="text-white text-sm">{{ number_format($holding['concentration'], 1) }}%</span>
                            </div>
                        </td>
                        <td class="py-4 px-3 text-center">
                            <a href="{{ route('crypto-trading.trade', $holding['crypto']->coin_symbol) }}" class="bg-primary-500 hover:bg-primary-600 text-white px-3 py-1 rounded-lg text-sm inline-flex items-center space-x-1 transition-colors duration-150">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                <span>Trade</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-700">
                        <td colspan="4" class="py-4 px-3 text-right text-gray-400 font-semibold">Total Portfolio Value:</td>
                        <td class="py-4 px-3 text-right text-white font-bold text-lg">${{ number_format($totalPortfolioValue, 2) }}</td>
                        <td class="py-4 px-3 text-right">
                            @php
                                $totalPL = collect($holdings)->sum('profit_loss');
                            @endphp
                            <p class="font-bold text-lg {{ $totalPL >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $totalPL >= 0 ? '+' : '' }}${{ number_format($totalPL, 2) }}
                            </p>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection
