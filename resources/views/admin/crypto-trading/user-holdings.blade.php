@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel bg-light">
    <div class="content">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">{{ $user->name }}'s Crypto Holdings</h1>
                <p class="text-muted">Detailed portfolio breakdown for {{ $user->email }}</p>
            </div>

            <!-- Back Button -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('admin.crypto-trading.user-trades', $user->id) }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Trades
                    </a>
                </div>
            </div>

            <!-- Holdings Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Portfolio Holdings</h4>
                                <div>
                                    <h5 class="mb-0">Total Portfolio Value: <strong class="text-primary">${{ number_format($totalPortfolioValue, 2) }}</strong></h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(empty($detailedHoldings))
                            <div class="text-center py-5">
                                <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No holdings found for this user.</p>
                            </div>
                            @else
                            <div class="table-responsive">
                                <table id="holdingsTable" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cryptocurrency</th>
                                            <th>Holdings</th>
                                            <th>Avg Cost</th>
                                            <th>Current Price</th>
                                            <th>Current Value</th>
                                            <th>Profit/Loss</th>
                                            <th>P/L %</th>
                                            <th>24h Change</th>
                                            <th>Concentration</th>
                                            <th>Trades</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detailedHoldings as $holding)
                                        <tr class="{{ $holding['concentration'] > 30 ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($holding['logo'])
                                                        <img src="{{ $holding['logo'] }}" alt="{{ $holding['name'] }}" style="width: 32px; height: 32px; border-radius: 50%; margin-right: 10px;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $holding['name'] }}</strong><br>
                                                        <small class="text-muted">{{ $holding['symbol'] }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($holding['quantity'], 8) }}</strong><br>
                                                <small class="text-muted">{{ $holding['symbol'] }}</small>
                                            </td>
                                            <td>${{ number_format($holding['average_cost'], 2) }}</td>
                                            <td>${{ number_format($holding['current_price'], 2) }}</td>
                                            <td><strong>${{ number_format($holding['current_value'], 2) }}</strong></td>
                                            <td class="text-{{ $holding['profit_loss'] >= 0 ? 'success' : 'danger' }}">
                                                <strong>{{ $holding['profit_loss'] >= 0 ? '+' : '' }}${{ number_format($holding['profit_loss'], 2) }}</strong>
                                            </td>
                                            <td class="text-{{ $holding['profit_loss_percent'] >= 0 ? 'success' : 'danger' }}">
                                                <strong>{{ $holding['profit_loss_percent'] >= 0 ? '+' : '' }}{{ number_format($holding['profit_loss_percent'], 2) }}%</strong>
                                            </td>
                                            <td class="text-{{ $holding['price_change_24h'] >= 0 ? 'success' : 'danger' }}">
                                                {{ $holding['price_change_24h'] >= 0 ? '+' : '' }}{{ number_format($holding['price_change_24h'], 2) }}%
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $holding['concentration'] > 30 ? 'warning' : 'primary' }}" role="progressbar" style="width: {{ min($holding['concentration'], 100) }}%">
                                                        {{ number_format($holding['concentration'], 1) }}%
                                                    </div>
                                                </div>
                                                @if($holding['concentration'] > 30)
                                                    <small class="text-warning"><i class="fa fa-exclamation-triangle"></i> High concentration</small>
                                                @endif
                                            </td>
                                            <td>{{ $holding['trades_count'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-dark">
                                            <td colspan="4" class="text-right"><strong>Total Portfolio Value:</strong></td>
                                            <td><strong>${{ number_format($totalPortfolioValue, 2) }}</strong></td>
                                            <td colspan="5"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#holdingsTable').DataTable({
        "pageLength": 25,
        "order": [[4, 'desc']], // Sort by current value descending
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>
@endpush
