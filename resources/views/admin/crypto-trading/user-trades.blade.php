@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel bg-light">
    <div class="content">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">{{ $user->name }}'s Crypto Trades</h1>
                <p class="text-muted">{{ $user->email }}</p>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endif

            <!-- User Stats -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fa fa-wallet"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Invested</p>
                                        <h4 class="card-title">${{ number_format($totalInvested, 2) }}</h4>
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
                                        <i class="fa fa-chart-line"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Current Value</p>
                                        <h4 class="card-title">${{ number_format($currentValue, 2) }}</h4>
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
                                    <div class="icon-big text-center icon-{{ $profitLoss >= 0 ? 'success' : 'danger' }} bubble-shadow-small">
                                        <i class="fa fa-{{ $profitLoss >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Profit/Loss</p>
                                        <h4 class="card-title text-{{ $profitLoss >= 0 ? 'success' : 'danger' }}">
                                            {{ $profitLoss >= 0 ? '+' : '' }}${{ number_format($profitLoss, 2) }}
                                        </h4>
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
                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                        <i class="fa fa-coins"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Holdings</p>
                                        <h4 class="card-title">{{ $holdingsCount }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('admin.crypto-trading.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to All Trades
                    </a>
                    <a href="{{ route('admin.crypto-trading.user-holdings', $user->id) }}" class="btn btn-info">
                        <i class="fa fa-briefcase"></i> View Holdings Breakdown
                    </a>
                    <a href="{{ route('admin.crypto-trading.create') }}?user_id={{ $user->id }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create Manual Trade
                    </a>
                </div>
            </div>

            <!-- Trades Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">All Trades</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tradesTable" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
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
                                        <tr>
                                            <td>{{ $trade->id }}</td>
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
    $('#tradesTable').DataTable({
        "pageLength": 25,
        "order": [[0, 'desc']],
        "columnDefs": [
            { "orderable": false, "targets": 9 }
        ]
    });

    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
