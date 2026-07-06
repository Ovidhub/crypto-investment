@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Holdings for {{ $user->name }}</h1>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-stats card-primary">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon-big text-center">
                                        <i class="flaticon-graph text-white"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-stats">
                                    <div class="numbers">
                                        <p class="card-category text-white">Portfolio Value</p>
                                        <h4 class="card-title text-white">${{ number_format($totalPortfolioValue, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card card-stats card-info">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon-big text-center">
                                        <i class="flaticon-coins text-white"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-stats">
                                    <div class="numbers">
                                        <p class="card-category text-white">Total Invested</p>
                                        <h4 class="card-title text-white">${{ number_format($totalInvested, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card card-stats card-{{ $totalProfitLoss >= 0 ? 'success' : 'danger' }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon-big text-center">
                                        <i class="flaticon-{{ $totalProfitLoss >= 0 ? 'up-arrow' : 'down-arrow' }} text-white"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-stats">
                                    <div class="numbers">
                                        <p class="card-category text-white">Total P/L</p>
                                        <h4 class="card-title text-white">{{ $totalProfitLoss >= 0 ? '+' : '' }}${{ number_format($totalProfitLoss, 2) }}</h4>
                                        <small class="text-white">({{ $totalProfitLoss >= 0 ? '+' : '' }}{{ number_format($totalProfitLossPercent, 2) }}%)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card card-stats card-secondary">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon-big text-center">
                                        <i class="flaticon-list text-white"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-stats">
                                    <div class="numbers">
                                        <p class="card-category text-white">Holdings</p>
                                        <h4 class="card-title text-white">{{ $holdingsCount }}</h4>
                                        <small class="text-white">Unique stocks</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Holdings Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Current Positions</h4>
                                <a href="{{ route('admin.stock-trading.user-trades', $user->id) }}" class="btn btn-secondary btn-round ml-auto">
                                    <i class="fa fa-arrow-left"></i>
                                    Back to Trades
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(count($holdings) > 0)
                            <div class="table-responsive">
                                <table id="holdingsTable" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Symbol</th>
                                            <th class="text-right">Quantity</th>
                                            <th class="text-right">Avg Cost</th>
                                            <th class="text-right">Current Price</th>
                                            <th class="text-right">Total Cost</th>
                                            <th class="text-right">Current Value</th>
                                            <th class="text-right">Unrealized P/L</th>
                                            <th class="text-right">Today's Change</th>
                                            <th class="text-right">Concentration</th>
                                            <th class="text-center">Trades</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($holdings as $holding)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($holding['logo_url'])
                                                    <img src="{{ $holding['logo_url'] }}" alt="{{ $holding['symbol'] }}" 
                                                         class="rounded-circle mr-2" style="width: 32px; height: 32px;" 
                                                         onerror="this.style.display='none'">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $holding['symbol'] }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $holding['name'] }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-right">{{ number_format($holding['quantity'], 4) }}</td>
                                            <td class="text-right">${{ number_format($holding['avg_cost'], 2) }}</td>
                                            <td class="text-right"><strong>${{ number_format($holding['current_price'], 2) }}</strong></td>
                                            <td class="text-right">${{ number_format($holding['total_cost'], 2) }}</td>
                                            <td class="text-right"><strong>${{ number_format($holding['current_value'], 2) }}</strong></td>
                                            <td class="text-right">
                                                <span class="badge badge-{{ $holding['profit_loss'] >= 0 ? 'success' : 'danger' }}">
                                                    {{ $holding['profit_loss'] >= 0 ? '+' : '' }}${{ number_format($holding['profit_loss'], 2) }}
                                                </span>
                                                <br>
                                                <small class="text-{{ $holding['profit_loss_percent'] >= 0 ? 'success' : 'danger' }}">
                                                    {{ $holding['profit_loss_percent'] >= 0 ? '+' : '' }}{{ number_format($holding['profit_loss_percent'], 2) }}%
                                                </small>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-{{ $holding['percent_change'] >= 0 ? 'success' : 'danger' }}">
                                                    {{ $holding['percent_change'] >= 0 ? '+' : '' }}{{ number_format($holding['percent_change'], 2) }}%
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $holding['price_change'] >= 0 ? '+' : '' }}${{ number_format($holding['price_change'], 2) }}
                                                </small>
                                            </td>
                                            <td class="text-right">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $holding['concentration'] > 30 ? 'warning' : 'info' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $holding['concentration'] }}%"
                                                         aria-valuenow="{{ $holding['concentration'] }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($holding['concentration'], 1) }}%
                                                    </div>
                                                </div>
                                                @if($holding['concentration'] > 30)
                                                <small class="text-warning">
                                                    <i class="fa fa-exclamation-triangle"></i> High concentration
                                                </small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $holding['trades_count'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <th><strong>TOTAL</strong></th>
                                            <th class="text-right">-</th>
                                            <th class="text-right">-</th>
                                            <th class="text-right">-</th>
                                            <th class="text-right"><strong>${{ number_format($totalInvested, 2) }}</strong></th>
                                            <th class="text-right"><strong>${{ number_format($totalPortfolioValue, 2) }}</strong></th>
                                            <th class="text-right">
                                                <strong class="text-{{ $totalProfitLoss >= 0 ? 'success' : 'danger' }}">
                                                    {{ $totalProfitLoss >= 0 ? '+' : '' }}${{ number_format($totalProfitLoss, 2) }}
                                                </strong>
                                            </th>
                                            <th class="text-right">-</th>
                                            <th class="text-right"><strong>100%</strong></th>
                                            <th class="text-center">-</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <strong>No Holdings</strong> - This user has no current stock positions.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/af-2.3.5/b-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/r-2.2.5/datatables.min.js"></script>
<script>
$(document).ready(function() {
    $('#holdingsTable').DataTable({
        "pageLength": 25,
        "order": [[5, "desc"]], // Sort by current value
        "responsive": true,
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                },
                orientation: 'landscape'
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            }
        ]
    });
});
</script>
@endsection
