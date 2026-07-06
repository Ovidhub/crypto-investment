@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Trade History</h1>
                <p class="text-muted">
                    Bot: <strong>{{ $subscription->tradingBot->name }}</strong> | 
                    User: <strong>{{ $subscription->user->name }}</strong>
                </p>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endif

            <div class="mb-4 row">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Total Trades</h5>
                            <h2 class="text-primary">{{ $trades->total() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Wins</h5>
                            <h2 class="text-success">{{ $subscription->trades()->where('outcome', 'win')->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Losses</h5>
                            <h2 class="text-danger">{{ $subscription->trades()->where('outcome', 'loss')->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Net Profit</h5>
                            <h2 class="{{ $subscription->trades()->sum('profit_loss') >= 0 ? 'text-success' : 'text-danger' }}">
                                ${{ number_format($subscription->trades()->sum('profit_loss'), 2) }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <a href="{{ route('admin.bot-trading.create-trade', $subscription) }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> Create Manual Trade
                </a>
            </div>

            <div class="mb-5 row">
                <div class="col-12 card p-4 shadow">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tradesTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Asset</th>
                                    <th>Type</th>
                                    <th>Entry Price</th>
                                    <th>Exit Price</th>
                                    <th>Amount</th>
                                    <th>Profit/Loss</th>
                                    <th>Profit %</th>
                                    <th>Outcome</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trades as $trade)
                                <tr>
                                    <td>
                                        {{ $trade->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $trade->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        @if($trade->asset_logo)
                                        <img src="{{ $trade->asset_logo }}" 
                                             alt="{{ $trade->asset_symbol }}" 
                                             class="mr-2"
                                             style="width: 24px; height: 24px;">
                                        @endif
                                        <strong>{{ $trade->asset_symbol }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $trade->asset_name }}</small>
                                    </td>
                                    <td>
                                        @if($trade->trade_type == 'automated')
                                        <span class="badge badge-info">
                                            <i class="fa fa-robot"></i> Automated
                                        </span>
                                        @else
                                        <span class="badge badge-warning">
                                            <i class="fa fa-user"></i> Manual
                                        </span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($trade->entry_price, 2) }}</td>
                                    <td>${{ number_format($trade->exit_price, 2) }}</td>
                                    <td>{{ number_format($trade->amount, 6) }}</td>
                                    <td>
                                        @if($trade->profit_loss >= 0)
                                        <span class="text-success font-weight-bold">
                                            +${{ number_format($trade->profit_loss, 2) }}
                                        </span>
                                        @else
                                        <span class="text-danger font-weight-bold">
                                            -${{ number_format(abs($trade->profit_loss), 2) }}
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($trade->profit_percentage >= 0)
                                        <span class="badge badge-success">+{{ number_format($trade->profit_percentage, 2) }}%</span>
                                        @else
                                        <span class="badge badge-danger">{{ number_format($trade->profit_percentage, 2) }}%</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($trade->outcome == 'win')
                                        <span class="badge badge-success">
                                            <i class="fa fa-check-circle"></i> Win
                                        </span>
                                        @else
                                        <span class="badge badge-danger">
                                            <i class="fa fa-times-circle"></i> Loss
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($trade->duration)
                                        {{ $trade->duration }}h
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.bot-trading.edit-trade', $trade) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Edit Trade">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.bot-trading.delete-trade', $trade) }}" 
                                                  method="POST" 
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Delete this trade? Profit will be reversed.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Delete Trade">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $trades->links() }}
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <a href="{{ route('admin.bot-trading.subscribers', $subscription->tradingBot) }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Subscribers
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tradesTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25,
            "responsive": true,
            "paging": false
        });
    });
</script>
@endsection
