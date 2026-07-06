@extends('layouts.app')
@section('content')
@include('admin.topmenu')
@include('admin.sidebar')
<div class="main-panel bg-light">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Crypto Staking Plans</h1>
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

            <!-- Global Emergency Control -->
            @if($settings->pause_all_staking_distributions === 'on')
            <div class="alert alert-danger shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="mb-1"><strong>⚠️ ALL STAKING DISTRIBUTIONS PAUSED</strong></h5>
                        <p class="mb-0">Global emergency pause is active. No rewards are being distributed across all plans.</p>
                    </div>
                    <a href="{{ route('admin.settings.preference') }}" class="btn btn-warning">Manage Settings</a>
                </div>
            </div>
            @endif

            <div class="mb-3">
                <a href="{{ route('admin.crypto-staking.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Staking Plan
                </a>
                <a href="{{ route('admin.crypto-staking.leaderboard') }}" class="btn btn-info ml-2">
                    <i class="fas fa-trophy"></i> View Leaderboard
                </a>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>Crypto</th>
                                            <th>Plan Name</th>
                                            <th>APR</th>
                                            <th>Lock Duration</th>
                                            <th>Reward Interval</th>
                                            <th>Capacity</th>
                                            <th>Active Subscribers</th>
                                            <th>Penalty</th>
                                            <th>Status</th>
                                            <th>Distribution</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($plans as $plan)
                                        <tr>
                                            <td>
                                                @if($plan->cryptoPrice)
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $plan->cryptoPrice->logo }}" alt="{{ $plan->cryptoPrice->name }}" style="width: 24px; height: 24px; border-radius: 50%;" class="mr-2">
                                                        <div>
                                                            <strong>{{ $plan->cryptoPrice->coin_symbol }}</strong>
                                                            <br><small class="text-muted">{{ $plan->cryptoPrice->name }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge badge-secondary">{{ $plan->coin_symbol }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $plan->name }}</td>
                                            <td>
                                                <strong class="text-success">{{ number_format($plan->apr_percentage, 2) }}%</strong>
                                                <br><small class="text-muted">Suggested: {{ $plan->recommended_apr }}</small>
                                            </td>
                                            <td>
                                                @if($plan->lock_duration === 'Flexible')
                                                    <span class="badge badge-success">{{ $plan->lock_duration }}</span>
                                                @else
                                                    <span class="badge badge-primary">{{ $plan->lock_duration }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $plan->reward_interval }}</td>
                                            <td>
                                                @if($plan->max_total_staked)
                                                    <div class="progress" style="height: 20px;">
                                                        @php
                                                            $percentage = ($plan->current_total_staked / $plan->max_total_staked) * 100;
                                                        @endphp
                                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                            {{ number_format($percentage, 1) }}%
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ $settings->currency }}{{ number_format($plan->current_total_staked, 2) }} / {{ $settings->currency }}{{ number_format($plan->max_total_staked, 2) }}</small>
                                                @else
                                                    <span class="badge badge-info">Unlimited</span>
                                                    <br><small class="text-muted">{{ $settings->currency }}{{ number_format($plan->current_total_staked, 2) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $plan->activeSubscriptions->count() }}</span>
                                            </td>
                                            <td>{{ number_format($plan->early_withdrawal_penalty, 1) }}%</td>
                                            <td>
                                                @if($plan->status === 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($settings->pause_all_staking_distributions === 'on')
                                                    <span class="badge badge-danger">Globally Paused</span>
                                                @elseif($plan->distribution_paused === 'on')
                                                    <span class="badge badge-warning">Paused</span>
                                                    <form action="{{ route('admin.crypto-staking.toggle-pause', $plan->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success mt-1" title="Resume">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge badge-success">Active</span>
                                                    <form action="{{ route('admin.crypto-staking.toggle-pause', $plan->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning mt-1" title="Pause">
                                                            <i class="fas fa-pause"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('admin.crypto-staking.edit', $plan->id) }}">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('admin.crypto-staking.subscribers', $plan->id) }}">
                                                            <i class="fas fa-users"></i> Subscribers ({{ $plan->activeSubscriptions->count() }})
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('admin.crypto-staking.leaderboard', $plan->id) }}">
                                                            <i class="fas fa-trophy"></i> Leaderboard
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure? This will delete the plan if no active subscriptions exist.')) document.getElementById('delete-form-{{ $plan->id }}').submit();">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                        @if($plan->activeSubscriptions->count() > 0)
                                                        <a class="dropdown-item text-warning" href="#" data-toggle="modal" data-target="#forceCompleteModal{{ $plan->id }}">
                                                            <i class="fas fa-exclamation-triangle"></i> Force Complete All
                                                        </a>
                                                        @endif
                                                    </div>
                                                </div>

                                                <form id="delete-form-{{ $plan->id }}" action="{{ route('admin.crypto-staking.destroy', $plan->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <!-- Force Complete Modal -->
                                                @if($plan->activeSubscriptions->count() > 0)
                                                <div class="modal fade" id="forceCompleteModal{{ $plan->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.crypto-staking.force-complete-all', $plan->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title">Force Complete All Subscriptions</h5>
                                                                    <button type="button" class="close text-white" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>This will affect {{ $plan->activeSubscriptions->count() }} active subscriptions.</strong></p>
                                                                    <p>All subscribers will have their stakes returned immediately.</p>
                                                                    
                                                                    <div class="form-group">
                                                                        <label>Apply Early Withdrawal Penalty?</label>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="apply_penalty" value="0" id="noPenalty{{ $plan->id }}" checked>
                                                                            <label class="form-check-label" for="noPenalty{{ $plan->id }}">
                                                                                No - Return full rewards
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="apply_penalty" value="1" id="yesPenalty{{ $plan->id }}">
                                                                            <label class="form-check-label" for="yesPenalty{{ $plan->id }}">
                                                                                Yes - Apply {{ number_format($plan->early_withdrawal_penalty, 1) }}% penalty on rewards
                                                                            </label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="reason{{ $plan->id }}">Reason <span class="text-danger">*</span></label>
                                                                        <textarea name="reason" id="reason{{ $plan->id }}" class="form-control" rows="3" required placeholder="Enter reason for force completion..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger">Force Complete All</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="11" class="text-center">
                                                <p class="text-muted">No staking plans found. <a href="{{ route('admin.crypto-staking.create') }}">Create one now</a></p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
