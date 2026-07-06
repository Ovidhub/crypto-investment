@extends('layouts.app')
@section('content')
@include('admin.topmenu')
@include('admin.sidebar')
<div class="main-panel bg-light">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Staking Subscribers</h1>
                <p class="text-muted">{{ $plan->name }} - {{ $plan->cryptoPrice->coin_symbol }}</p>
            </div>

            @if(session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- Plan Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h2 class="text-primary mb-0">{{ $subscriptions->where('status', 'active')->count() }}</h2>
                            <p class="text-muted mb-0">Active Subscriptions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h2 class="text-success mb-0">{{ $settings->currency }}{{ number_format($plan->current_total_staked, 2) }}</h2>
                            <p class="text-muted mb-0">Total Staked</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h2 class="text-warning mb-0">{{ $settings->currency }}{{ number_format($subscriptions->sum('current_rewards'), 2) }}</h2>
                            <p class="text-muted mb-0">Total Rewards</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h2 class="text-info mb-0">{{ $plan->apr_percentage }}%</h2>
                            <p class="text-muted mb-0">Current APR</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscribers Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="card-title">All Subscribers ({{ $subscriptions->count() }})</h4>
                            <div class="card-tools">
                                <a href="{{ route('admin.crypto-staking.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to Plans
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($subscriptions->count() > 0)
                            <div class="table-responsive">
                                <table id="subscribersTable" class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Amount Staked</th>
                                            <th>Compounded</th>
                                            <th>Total Staked</th>
                                            <th>Current Rewards</th>
                                            <th>Locked APR</th>
                                            <th>Status</th>
                                            <th>Start Date</th>
                                            <th>Days Left</th>
                                            <th>Progress</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subscriptions as $subscription)
                                        <tr>
                                            <td>
                                                <strong>{{ $subscription->user->name }}</strong>
                                                @if($subscription->is_compound_enabled)
                                                <span class="badge badge-success badge-sm ml-1" title="Compound enabled">
                                                    <i class="fas fa-sync"></i>
                                                </span>
                                                @endif
                                            </td>
                                            <td>{{ $subscription->user->email }}</td>
                                            <td>{{ $settings->currency }}{{ number_format($subscription->amount_staked, 2) }}</td>
                                            <td>
                                                @if($subscription->compounded_amount > 0)
                                                <span class="text-success">
                                                    +{{ $settings->currency }}{{ number_format($subscription->compounded_amount, 2) }}
                                                </span>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $settings->currency }}{{ number_format($subscription->totalStaked, 2) }}</strong>
                                            </td>
                                            <td class="text-warning">
                                                <strong>{{ $settings->currency }}{{ number_format($subscription->current_rewards, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($subscription->apr_at_subscription != $plan->apr_percentage)
                                                <span class="badge badge-warning" title="APR was {{ $subscription->apr_at_subscription }}% at subscription, plan now {{ $plan->apr_percentage }}%">
                                                    {{ $subscription->apr_at_subscription }}%
                                                </span>
                                                @else
                                                <span class="badge badge-info">
                                                    {{ $subscription->apr_at_subscription }}%
                                                </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subscription->status == 'active')
                                                <span class="badge badge-success">Active</span>
                                                @elseif($subscription->status == 'completed')
                                                <span class="badge badge-primary">Completed</span>
                                                @elseif($subscription->status == 'cancelled')
                                                <span class="badge badge-secondary">Cancelled</span>
                                                @elseif($subscription->status == 'force_completed')
                                                <span class="badge badge-danger" title="Force completed by {{ $subscription->forceCompletedBy->name ?? 'admin' }}">
                                                    Force Completed
                                                </span>
                                                @endif
                                            </td>
                                            <td>{{ $subscription->start_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($subscription->status == 'active')
                                                    @if($subscription->lock_type == 'flexible')
                                                    <span class="badge badge-info">Flexible</span>
                                                    @elseif($subscription->daysRemaining > 0)
                                                    <span class="badge badge-warning">{{ $subscription->daysRemaining }} days</span>
                                                    @else
                                                    <span class="badge badge-success">Unlocked</span>
                                                    @endif
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subscription->status == 'active' && $subscription->lock_type != 'flexible')
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $subscription->progressPercentage }}%">
                                                        {{ round($subscription->progressPercentage) }}%
                                                    </div>
                                                </div>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subscription->status == 'active')
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#adjustRewardModal{{ $subscription->id }}">
                                                            <i class="fas fa-adjust"></i> Adjust Reward
                                                        </a>
                                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#forceCompleteModal{{ $subscription->id }}">
                                                            <i class="fas fa-stop-circle"></i> Force Complete
                                                        </a>
                                                    </div>
                                                </div>
                                                @else
                                                <span class="text-muted">No actions</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Adjust Reward Modal -->
                                        <div class="modal fade" id="adjustRewardModal{{ $subscription->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title">Adjust Reward</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('admin.crypto-staking.adjust-reward', $subscription->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p><strong>User:</strong> {{ $subscription->user->name }}</p>
                                                            <p><strong>Current Rewards:</strong> {{ $settings->currency }}{{ number_format($subscription->current_rewards, 2) }}</p>

                                                            <div class="form-group">
                                                                <label>Action</label>
                                                                <select name="action" class="form-control" required>
                                                                    <option value="add">Add to Rewards</option>
                                                                    <option value="subtract">Subtract from Rewards</option>
                                                                </select>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Amount ({{ $settings->currency }})</label>
                                                                <input type="number" name="amount" class="form-control" required min="0.01" step="0.01" placeholder="0.00">
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Reason</label>
                                                                <textarea name="reason" class="form-control" rows="2" required placeholder="Explain the adjustment..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Apply Adjustment</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Force Complete Modal -->
                                        <div class="modal fade" id="forceCompleteModal{{ $subscription->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Force Complete Subscription</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('admin.crypto-staking.force-complete-single', $subscription->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p class="text-danger"><strong>Warning:</strong> This will immediately complete this subscription.</p>
                                                            <p><strong>User:</strong> {{ $subscription->user->name }}</p>
                                                            <p><strong>Total to Return:</strong> {{ $settings->currency }}{{ number_format($subscription->totalStaked + $subscription->current_rewards, 2) }}</p>

                                                            <div class="form-group">
                                                                <label>Apply Early Withdrawal Penalty?</label>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="apply_penalty" value="yes" id="penalty_yes_{{ $subscription->id }}">
                                                                    <label class="form-check-label" for="penalty_yes_{{ $subscription->id }}">
                                                                        Yes - Apply {{ $plan->early_withdrawal_penalty }}% penalty
                                                                    </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="apply_penalty" value="no" id="penalty_no_{{ $subscription->id }}" checked>
                                                                    <label class="form-check-label" for="penalty_no_{{ $subscription->id }}">
                                                                        No - Return full rewards
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Reason <span class="text-danger">*</span></label>
                                                                <textarea name="reason" class="form-control" rows="2" required placeholder="Why are you force completing this subscription?"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Force Complete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <p class="text-muted">No subscriptions yet for this plan.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#subscribersTable').DataTable({
        order: [[8, 'desc']], // Sort by start date descending
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> Export CSV',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-info btn-sm'
            }
        ]
    });
});
</script>
@endpush
@endsection
