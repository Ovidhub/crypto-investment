@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Stock Trading Management</h1>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="flaticon-coins"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Total Trades</p>
                                    <h4 class="card-title">{{ $stats['total_trades'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                    <i class="flaticon-graph"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Buy Trades</p>
                                    <h4 class="card-title">{{ $stats['buy_trades'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-danger bubble-shadow-small">
                                    <i class="flaticon-loss"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Sell Trades</p>
                                    <h4 class="card-title">{{ $stats['sell_trades'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="flaticon-list"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Total Volume</p>
                                    <h4 class="card-title">${{ number_format($stats['total_volume'], 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Trades Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">All Stock Trades</h4>
                            <a href="{{ route('admin.stock-trading.settings') }}" class="btn btn-primary btn-round ml-auto">
                                <i class="fa fa-cog"></i>
                                Settings
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
                            <table id="stockTradesTable" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
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
                                            <a href="{{ route('admin.stock-trading.user-trades', $trade->user_id) }}">
                                                {{ $trade->user->name ?? 'Unknown' }}
                                            </a>
                                        </td>
                                        <td>
                                            <strong>{{ $trade->stock_symbol }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $trade->trade_type === 'buy' ? 'success' : 'danger' }}">
                                                {{ strtoupper($trade->trade_type) }}
                                            </span>
                                        </td>
                                        <td>{{ $trade->quantity }}</td>
                                        <td>${{ number_format($trade->price, 2) }}</td>
                                        <td>${{ number_format($trade->fee_amount, 2) }} ({{ $trade->fee_percent }}%)</td>
                                        <td>${{ number_format($trade->net_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $trade->status === 'completed' ? 'info' : ($trade->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($trade->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $trade->created_at->format('M d, Y H:i') }}</td>
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
    $('#stockTradesTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
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
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            }
        ]
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection

