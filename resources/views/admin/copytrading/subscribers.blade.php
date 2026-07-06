@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Subscribers for {{ $trader->name }}</h1>
            </div>

            <div class="mb-3">
                <a href="{{ route('admin.copytrading.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Traders
                </a>
                <a href="{{ route('admin.copytrading.distribute', $trader->id) }}" class="btn btn-success">
                    <i class="fa fa-dollar-sign"></i> Distribute Profit
                </a>
            </div>

            <div class="mb-3 row">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Subscribers</h6>
                            <h2 class="text-primary">{{ $subscribers->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Active</h6>
                            <h2 class="text-success">{{ $subscribers->where('status', 'Active')->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Investment</h6>
                            <h2 class="text-info">{{ $settings->currency }}{{ number_format($subscribers->sum('amount_invested'), 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Profit Distributed</h6>
                            <h2 class="text-success">{{ $settings->currency }}{{ number_format($subscribers->sum('current_profit'), 2) }}</h2>
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
                                    <th>Email</th>
                                    <th>Amount Invested</th>
                                    <th>Current Profit</th>
                                    <th>Total Value</th>
                                    <th>ROI</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Last Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscribers as $subscription)
                                <tr>
                                    <td>{{ $subscription->user->name }}</td>
                                    <td>{{ $subscription->user->email }}</td>
                                    <td>{{ $settings->currency }}{{ number_format($subscription->amount_invested, 2) }}</td>
                                    <td>{{ $settings->currency }}{{ number_format($subscription->current_profit, 2) }}</td>
                                    <td>{{ $settings->currency }}{{ number_format($subscription->getTotalReturn(), 2) }}</td>
                                    <td>
                                        @php
                                        $roi = $subscription->getProfitPercentage();
                                        @endphp
                                        <span class="badge {{ $roi > 0 ? 'badge-success' : 'badge-secondary' }}">
                                            {{ number_format($roi, 2) }}%
                                        </span>
                                    </td>
                                    <td>{{ $subscription->start_date->format('M d, Y') }}</td>
                                    <td>{{ $subscription->end_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($subscription->status == 'Active')
                                        <span class="badge badge-success">Active</span>
                                        @elseif($subscription->status == 'Completed')
                                        <span class="badge badge-primary">Completed</span>
                                        @else
                                        <span class="badge badge-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->last_profit_added)
                                        {{ $subscription->last_profit_added->diffForHumans() }}
                                        @else
                                        <span class="text-muted">Never</span>
                                        @endif
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#subscribersTable').DataTable({
            "order": [[ 6, "desc" ]]
        });
    });
</script>
@endpush
@endsection
