@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel bg-light">
    <div class="content">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Crypto Trading Management</h1>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fa fa-bitcoin"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Trades</p>
                                        <h4 class="card-title">{{ $trades->count() }}</h4>
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
                                        <i class="fa fa-arrow-up"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Buy Trades</p>
                                        <h4 class="card-title">{{ $trades->where('trade_type', 'buy')->count() }}</h4>
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
                                        <i class="fa fa-arrow-down"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Sell Trades</p>
                                        <h4 class="card-title">{{ $trades->where('trade_type', 'sell')->count() }}</h4>
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
                                        <i class="fa fa-dollar-sign"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Volume</p>
                                        <h4 class="card-title">${{ number_format($trades->sum('net_amount'), 0) }}</h4>
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
                                <h4 class="card-title">All Crypto Trades</h4>
                                <div class="ml-auto">
                                    <a href="{{ route('admin.crypto-trading.create') }}" class="btn btn-primary btn-round ml-auto">
                                        <i class="fa fa-plus"></i>
                                        Create Manual Trade
                                    </a>
                                    <a href="{{ route('admin.crypto-trading.manage-cryptos') }}" class="btn btn-info btn-round ml-auto">
                                        <i class="fa fa-bitcoin"></i>
                                        Manage Cryptos
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- User Filter -->
                            <div class="form-group">
                                <label>Filter by User:</label>
                                <select id="userFilter" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="table-responsive">
                                <table id="tradesTable" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Crypto</th>
                                            <th>Type</th>
                                            <th>Order Type</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trades as $trade)
                                        <tr data-user-id="{{ $trade->user_id }}">
                                            <td>{{ $trade->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.crypto-trading.user-trades', $trade->user_id) }}">
                                                    {{ $trade->user->name }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($trade->cryptoPrice)
                                                    @if($trade->cryptoPrice->logo)
                                                        <img src="{{ $trade->cryptoPrice->logo }}" alt="{{ $trade->coin_symbol }}" style="width: 24px; height: 24px; border-radius: 50%; vertical-align: middle; margin-right: 5px;">
                                                    @endif
                                                    {{ $trade->coin_symbol }}
                                                @else
                                                    {{ $trade->coin_symbol }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($trade->trade_type === 'buy')
                                                    <span class="badge badge-success">BUY</span>
                                                @else
                                                    <span class="badge badge-danger">SELL</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ strtoupper(str_replace('_', ' ', $trade->order_type)) }}</span>
                                            </td>
                                            <td>{{ number_format($trade->quantity, 8) }}</td>
                                            <td>${{ number_format($trade->price_usd, 2) }}</td>
                                            <td>${{ number_format($trade->net_amount, 2) }}</td>
                                            <td>
                                                @if($trade->status === 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($trade->status === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @else
                                                    <span class="badge badge-secondary">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>{{ $trade->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <div class="form-button-action">
                                                    <a href="{{ route('admin.crypto-trading.edit', $trade->id) }}" class="btn btn-link btn-primary btn-lg" data-toggle="tooltip" title="Edit Trade">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.crypto-trading.delete', $trade->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this trade? This will reverse the balance impact.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger" data-toggle="tooltip" title="Delete">
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

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#tradesTable').DataTable({
        "pageLength": 25,
        "order": [[0, 'desc']],
        "columnDefs": [
            { "orderable": false, "targets": 10 }
        ]
    });

    // User filter
    $('#userFilter').on('change', function() {
        var userId = $(this).val();
        if (userId) {
            table.column(1).search('^' + $('#userFilter option:selected').text() + '$', true, false).draw();
        } else {
            table.column(1).search('').draw();
        }
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
