@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Stock Trades for {{ $user->name }}</h1>
            </div>

            <!-- User Info Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h5>User Information</h5>
                                <p class="mb-1"><strong>Name:</strong> {{ $user->name }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                                <p class="mb-1"><strong>Balance:</strong> ${{ number_format($user->account_bal, 2) }}</p>
                            </div>
                            <div class="col-md-3">
                                <h5>Portfolio</h5>
                                <p class="mb-1"><strong>Total Trades:</strong> {{ $totalTrades }}</p>
                                <p class="mb-1"><strong>Buy Trades:</strong> {{ $buyTrades }}</p>
                                <p class="mb-1"><strong>Sell Trades:</strong> {{ $sellTrades }}</p>
                            </div>
                            <div class="col-md-3">
                                <h5>Statistics</h5>
                                <p class="mb-1"><strong>Portfolio Value:</strong> ${{ number_format($portfolioValue, 2) }}</p>
                                <p class="mb-1"><strong>Total Invested:</strong> ${{ number_format($totalInvested, 2) }}</p>
                            </div>
                            <div class="col-md-3">
                                <h5>Profit/Loss</h5>
                                <p class="mb-1">
                                    <strong>Total P/L:</strong> 
                                    <span class="text-{{ $totalProfitLoss >= 0 ? 'success' : 'danger' }}">
                                        {{ $totalProfitLoss >= 0 ? '+' : '' }}${{ number_format($totalProfitLoss, 2) }}
                                    </span>
                                </p>
                                <a href="{{ route('admin.stock-trading.user-holdings', $user->id) }}" class="btn btn-sm btn-info mt-2">
                                    <i class="fa fa-pie-chart"></i> View Holdings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trades Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">All Trades</h4>
                            <a href="{{ route('admin.stock-trading.create-for-user', $user->id) }}" class="btn btn-success btn-round ml-auto mr-2">
                                <i class="fa fa-plus"></i>
                                Create Manual Trade
                            </a>
                            <a href="{{ route('admin.stock-trading.index') }}" class="btn btn-secondary btn-round">
                                <i class="fa fa-arrow-left"></i>
                                Back to All Trades
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="userStockTradesTable" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Symbol</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Fee</th>
                                        <th>Net Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trades as $trade)
                                    <tr>
                                        <td>{{ $trade->id }}</td>
                                        <td>
                                            <strong>{{ $trade->stock_symbol }}</strong>
                                            @if($trade->stockPrice)
                                            <br>
                                            <small class="text-muted">{{ $trade->stockPrice->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $trade->trade_type === 'buy' ? 'success' : 'danger' }}">
                                                {{ strtoupper($trade->trade_type) }}
                                            </span>
                                        </td>
                                        <td>{{ $trade->quantity }}</td>
                                        <td>${{ number_format($trade->price, 2) }}</td>
                                        <td>
                                            ${{ number_format($trade->fee_amount, 2) }}
                                            <br>
                                            <small class="text-muted">({{ $trade->fee_percent }}%)</small>
                                        </td>
                                        <td>
                                            <strong>${{ number_format($trade->net_amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $trade->status === 'completed' ? 'info' : ($trade->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($trade->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $trade->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $trade->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                @if($trade->status === 'pending')
                                                <form action="{{ route('admin.stock-trading.cancel', $trade->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link btn-warning btn-lg" 
                                                        data-toggle="tooltip" title="Cancel Trade"
                                                        onclick="return confirm('Cancel this trade?')">
                                                        <i class="fa fa-ban"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                
                                                <a href="{{ route('admin.stock-trading.edit', $trade->id) }}" 
                                                   class="btn btn-link btn-primary btn-lg" 
                                                   data-toggle="tooltip" title="Edit Trade">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('admin.stock-trading.delete', $trade->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link btn-danger btn-lg" 
                                                        data-toggle="tooltip" title="Delete Trade"
                                                        onclick="return confirm('Delete this trade? This cannot be undone.')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
    $('#userStockTradesTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
        "responsive": true,
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            }
        ]
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
