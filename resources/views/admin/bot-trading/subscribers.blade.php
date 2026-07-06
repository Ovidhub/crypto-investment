@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">{{ $bot->name }} - Subscribers</h1>
                <p class="text-muted">Manage subscriptions and adjust profits manually</p>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="mb-4 row">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Total Subscribers</h5>
                            <h2 class="text-primary">{{ $subscriptions->total() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Active</h5>
                            <h2 class="text-success">{{ $bot->activeSubscriptions()->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Total Invested</h5>
                            <h2 class="text-info">${{ number_format($bot->subscriptions()->sum('amount_invested'), 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Total Profit</h5>
                            <h2 class="text-success">${{ number_format($bot->subscriptions()->sum('current_profit'), 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5 row">
                <div class="col-12 card p-4 shadow">
                    <div class="table-responsive">
                        <table class="table table-hover" id="subscribersTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Amount Invested</th>
                                    <th>Current Profit</th>
                                    <th>Manual Adjustment</th>
                                    <th>Total Return</th>
                                    <th>Profit %</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        <strong>{{ $subscription->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $subscription->user->email }}</small>
                                    </td>
                                    <td>${{ number_format($subscription->amount_invested, 2) }}</td>
                                    <td>
                                        @if($subscription->current_profit >= 0)
                                        <span class="text-success">
                                            <i class="fa fa-arrow-up"></i> ${{ number_format($subscription->current_profit, 2) }}
                                        </span>
                                        @else
                                        <span class="text-danger">
                                            <i class="fa fa-arrow-down"></i> ${{ number_format(abs($subscription->current_profit), 2) }}
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->manual_profit_adjustment)
                                        <span class="badge badge-warning">
                                            ${{ number_format($subscription->manual_profit_adjustment, 2) }}
                                        </span>
                                        @else
                                        <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($subscription->total_return, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($subscription->profit_percentage >= 0)
                                        <span class="badge badge-success">+{{ number_format($subscription->profit_percentage, 2) }}%</span>
                                        @else
                                        <span class="badge badge-danger">{{ number_format($subscription->profit_percentage, 2) }}%</span>
                                        @endif
                                    </td>
                                    <td>{{ $subscription->start_date->format('M d, Y') }}</td>
                                    <td>{{ $subscription->end_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($subscription->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                        @elseif($subscription->status == 'completed')
                                        <span class="badge badge-secondary">Completed</span>
                                        @else
                                        <span class="badge badge-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.bot-trading.trades', $subscription) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="View Trades">
                                                <i class="fa fa-chart-line"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    data-toggle="modal" 
                                                    data-target="#adjustProfitModal{{ $subscription->id }}"
                                                    title="Adjust Profit">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="{{ route('admin.bot-trading.create-trade', $subscription) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Create Manual Trade">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Adjust Profit Modal -->
                                <div class="modal fade" id="adjustProfitModal{{ $subscription->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.bot-trading.adjust-profit', $subscription) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Adjust Profit - {{ $subscription->user->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="adjustment">Profit Adjustment ($)</label>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               name="adjustment" 
                                                               step="0.01" 
                                                               value="{{ $subscription->manual_profit_adjustment ?? 0 }}" 
                                                               required>
                                                        <small class="form-text text-muted">
                                                            Positive = Add profit, Negative = Deduct profit<br>
                                                            Current: ${{ number_format($subscription->manual_profit_adjustment ?? 0, 2) }}
                                                        </small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="notes">Admin Notes (optional)</label>
                                                        <textarea class="form-control" 
                                                                  name="notes" 
                                                                  rows="3" 
                                                                  placeholder="Reason for adjustment..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> Save Adjustment
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $subscriptions->links() }}
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <a href="{{ route('admin.bot-trading.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Bots
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#subscribersTable').DataTable({
            "order": [[6, "desc"]],
            "pageLength": 25,
            "responsive": true,
            "paging": false,
            "searching": true
        });
    });
</script>
@endsection
