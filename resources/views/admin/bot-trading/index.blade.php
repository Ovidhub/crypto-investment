@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Trading Bots</h1>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="mb-5 row">
                <div class="col-12">
                    <a href="{{ route('admin.bot-trading.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create New Bot
                    </a>
                </div>
            </div>

            <div class="mb-5 row">
                <div class="col-12 card p-4 shadow">
                    <div class="table-responsive">
                        <table class="table table-hover" id="botsTable">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Strategy</th>
                                    <th>Subscribers</th>
                                    <th>ROI</th>
                                    <th>Win Rate</th>
                                    <th>Total Trades</th>
                                    <th>Total Profit</th>
                                    <th>Auto Trade</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bots as $bot)
                                <tr>
                                    <td>
                                        @if($bot->photo)
                                        <img src="{{ asset('storage/app/public/' . $bot->photo) }}" 
                                             alt="{{ $bot->name }}" 
                                             class="rounded-circle"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fa fa-robot text-white"></i>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $bot->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $bot->trade_interval }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $bot->strategy_type }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $bot->active_subscriptions_count }}</strong> active
                                        <br>
                                        <small class="text-muted">{{ $bot->subscriptions_count }} total</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ number_format($bot->roi_percentage, 2) }}%</span>
                                    </td>
                                    <td>{{ number_format($bot->win_rate, 2) }}%</td>
                                    <td>{{ number_format($bot->total_trades) }}</td>
                                    <td>
                                        @if($bot->total_profit >= 0)
                                        <span class="text-success">
                                            <i class="fa fa-arrow-up"></i> ${{ number_format($bot->total_profit, 2) }}
                                        </span>
                                        @else
                                        <span class="text-danger">
                                            <i class="fa fa-arrow-down"></i> ${{ number_format(abs($bot->total_profit), 2) }}
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($bot->auto_trade == 'on')
                                        <span class="badge badge-success">
                                            <i class="fa fa-check-circle"></i> Enabled
                                        </span>
                                        @else
                                        <span class="badge badge-secondary">
                                            <i class="fa fa-times-circle"></i> Disabled
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($bot->status == 'active')
                                        <span class="badge badge-primary">Active</span>
                                        @else
                                        <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.bot-trading.subscribers', $bot) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="View Subscribers">
                                                <i class="fa fa-users"></i>
                                            </a>
                                            <a href="{{ route('admin.bot-trading.edit', $bot) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Edit Bot">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.bot-trading.destroy', $bot) }}" 
                                                  method="POST" 
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this bot? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Delete Bot">
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
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#botsTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25,
            "responsive": true
        });
    });
</script>
@endsection
