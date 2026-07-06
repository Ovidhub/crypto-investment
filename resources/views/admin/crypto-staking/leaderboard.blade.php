@extends('layouts.app')
@section('content')
@include('admin.topmenu')
@include('admin.sidebar')
<div class="main-panel bg-light">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Staking Leaderboard</h1>
                @if(isset($plan))
                <p class="text-muted">{{ $plan->name }} - {{ $plan->cryptoPrice->coin_symbol }}</p>
                @else
                <p class="text-muted">Top stakers across all crypto staking plans</p>
                @endif
            </div>

            <!-- Filter Options -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="GET" action="{{ isset($plan) ? route('admin.crypto-staking.leaderboard', $plan->id) : route('admin.crypto-staking.leaderboard') }}" class="form-inline">
                                <label class="mr-2">View:</label>
                                <div class="btn-group mr-3" role="group">
                                    @if(isset($plan))
                                    <a href="{{ route('admin.crypto-staking.leaderboard') }}" class="btn btn-outline-primary">
                                        All Cryptos
                                    </a>
                                    <a href="{{ route('admin.crypto-staking.leaderboard', $plan->id) }}" class="btn btn-primary">
                                        {{ $plan->cryptoPrice->coin_symbol }} Only
                                    </a>
                                    @else
                                    <a href="{{ route('admin.crypto-staking.leaderboard') }}" class="btn btn-primary">
                                        All Cryptos
                                    </a>
                                    @endif
                                </div>

                                @if(!isset($plan))
                                <label class="mr-2">Filter by Crypto:</label>
                                <select name="crypto" class="form-control mr-2" onchange="this.form.submit()">
                                    <option value="">All Cryptocurrencies</option>
                                    @foreach($cryptos as $crypto)
                                    <option value="{{ $crypto->coin_symbol }}" {{ request('crypto') == $crypto->coin_symbol ? 'selected' : '' }}>
                                        {{ $crypto->coin_symbol }} - {{ $crypto->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @endif

                                <a href="{{ route('admin.crypto-staking.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Plans
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h2 class="text-primary mb-0">{{ $topStakers->count() ?? 0 }}</h2>
                            <p class="text-muted mb-0">Total Stakers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h2 class="text-success mb-0">{{ $settings->currency }}{{ number_format($totalStaked ?? 0, 2) }}</h2>
                            <p class="text-muted mb-0">Total Staked</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h2 class="text-warning mb-0">{{ $settings->currency }}{{ number_format($totalRewards ?? 0, 2) }}</h2>
                            <p class="text-muted mb-0">Total Rewards Paid</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h2 class="text-info mb-0">{{ $activeStakes ?? 0 }}</h2>
                            <p class="text-muted mb-0">Active Stakes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Podium (Top 3) -->
            @if($topStakers && $topStakers->count() >= 3)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-primary text-white">
                            <h4 class="card-title mb-0"><i class="fas fa-trophy"></i> Top 3 Stakers</h4>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <!-- 2nd Place -->
                                <div class="col-md-4 order-md-1">
                                    <div class="podium-item silver">
                                        <div class="podium-rank">
                                            <i class="fas fa-medal fa-3x" style="color: #C0C0C0;"></i>
                                            <h3 class="mt-2">2nd</h3>
                                        </div>
                                        <img src="{{ $topStakers[1]->user->profile_photo_url ?? asset('dash/images/default-avatar.png') }}" alt="{{ $topStakers[1]->user->name }}" class="rounded-circle" width="80" height="80">
                                        <h5 class="mt-3">{{ $topStakers[1]->user->name }}</h5>
                                        <p class="text-muted">{{ $topStakers[1]->user->email }}</p>
                                        <h4 class="text-success">{{ $settings->currency }}{{ number_format($topStakers[1]->total_rewards, 2) }}</h4>
                                        <p class="text-muted">Total Rewards</p>
                                        <div class="mt-2">
                                            <span class="badge badge-info">{{ $topStakers[1]->stake_count }} Stakes</span>
                                            <span class="badge badge-primary">{{ $settings->currency }}{{ number_format($topStakers[1]->total_staked, 2) }} Staked</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 1st Place -->
                                <div class="col-md-4 order-md-2">
                                    <div class="podium-item gold">
                                        <div class="podium-rank">
                                            <i class="fas fa-crown fa-3x" style="color: #FFD700;"></i>
                                            <h3 class="mt-2">1st</h3>
                                        </div>
                                        <img src="{{ $topStakers[0]->user->profile_photo_url ?? asset('dash/images/default-avatar.png') }}" alt="{{ $topStakers[0]->user->name }}" class="rounded-circle" width="100" height="100">
                                        <h4 class="mt-3">{{ $topStakers[0]->user->name }}</h4>
                                        <p class="text-muted">{{ $topStakers[0]->user->email }}</p>
                                        <h3 class="text-success">{{ $settings->currency }}{{ number_format($topStakers[0]->total_rewards, 2) }}</h3>
                                        <p class="text-muted">Total Rewards</p>
                                        <div class="mt-2">
                                            <span class="badge badge-info">{{ $topStakers[0]->stake_count }} Stakes</span>
                                            <span class="badge badge-primary">{{ $settings->currency }}{{ number_format($topStakers[0]->total_staked, 2) }} Staked</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3rd Place -->
                                <div class="col-md-4 order-md-3">
                                    <div class="podium-item bronze">
                                        <div class="podium-rank">
                                            <i class="fas fa-medal fa-3x" style="color: #CD7F32;"></i>
                                            <h3 class="mt-2">3rd</h3>
                                        </div>
                                        <img src="{{ $topStakers[2]->user->profile_photo_url ?? asset('dash/images/default-avatar.png') }}" alt="{{ $topStakers[2]->user->name }}" class="rounded-circle" width="80" height="80">
                                        <h5 class="mt-3">{{ $topStakers[2]->user->name }}</h5>
                                        <p class="text-muted">{{ $topStakers[2]->user->email }}</p>
                                        <h4 class="text-success">{{ $settings->currency }}{{ number_format($topStakers[2]->total_rewards, 2) }}</h4>
                                        <p class="text-muted">Total Rewards</p>
                                        <div class="mt-2">
                                            <span class="badge badge-info">{{ $topStakers[2]->stake_count }} Stakes</span>
                                            <span class="badge badge-primary">{{ $settings->currency }}{{ number_format($topStakers[2]->total_staked, 2) }} Staked</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Full Leaderboard Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="card-title">Full Rankings (Top 50)</h4>
                        </div>
                        <div class="card-body">
                            @if($topStakers && $topStakers->count() > 0)
                            <div class="table-responsive">
                                <table id="leaderboardTable" class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Total Staked</th>
                                            <th>Total Rewards</th>
                                            <th>Active Stakes</th>
                                            <th>Avg APR</th>
                                            @if(!isset($plan))
                                            <th>Crypto Breakdown</th>
                                            @endif
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topStakers as $index => $staker)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">#{{ $index + 1 }}</strong>
                                                @if($index == 0)
                                                <i class="fas fa-crown text-warning ml-1"></i>
                                                @elseif($index == 1)
                                                <i class="fas fa-medal text-secondary ml-1"></i>
                                                @elseif($index == 2)
                                                <i class="fas fa-medal ml-1" style="color: #CD7F32;"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $staker->user->name }}</strong>
                                                @if($staker->stake_count > 5)
                                                <span class="badge badge-success badge-sm ml-1">Active Trader</span>
                                                @endif
                                            </td>
                                            <td>{{ $staker->user->email }}</td>
                                            <td>{{ $settings->currency }}{{ number_format($staker->total_staked, 2) }}</td>
                                            <td class="text-success">
                                                <strong>{{ $settings->currency }}{{ number_format($staker->total_rewards, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $staker->stake_count }}</span>
                                            </td>
                                            <td>{{ number_format($staker->avg_apr, 2) }}%</td>
                                            @if(!isset($plan))
                                            <td>
                                                @php
                                                $userSubscriptions = \App\Models\StakingSubscription::where('user_id', $staker->user_id)
                                                    ->with('stakingPlan.cryptoPrice')
                                                    ->get()
                                                    ->groupBy('stakingPlan.coin_symbol');
                                                @endphp
                                                @foreach($userSubscriptions as $symbol => $subs)
                                                <span class="badge badge-secondary badge-sm mr-1">{{ $symbol }}</span>
                                                @endforeach
                                            </td>
                                            @endif
                                            <td>
                                                @php
                                                $roi = $staker->total_staked > 0 ? ($staker->total_rewards / $staker->total_staked * 100) : 0;
                                                @endphp
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar {{ $roi > 20 ? 'bg-success' : ($roi > 10 ? 'bg-info' : 'bg-warning') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min($roi, 100) }}%"
                                                         title="{{ number_format($roi, 2) }}% ROI">
                                                        {{ number_format($roi, 1) }}% ROI
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                                <p class="text-muted">No stakers yet.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.podium-item {
    padding: 30px 20px;
    margin: 10px;
    background: #fff;
    border-radius: 10px;
    transition: transform 0.3s;
}
.podium-item:hover {
    transform: translateY(-10px);
}
.podium-item.gold {
    border: 3px solid #FFD700;
    box-shadow: 0 5px 20px rgba(255, 215, 0, 0.3);
}
.podium-item.silver {
    border: 3px solid #C0C0C0;
    box-shadow: 0 5px 20px rgba(192, 192, 192, 0.3);
}
.podium-item.bronze {
    border: 3px solid #CD7F32;
    box-shadow: 0 5px 20px rgba(205, 127, 50, 0.3);
}
.podium-rank {
    margin-bottom: 20px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#leaderboardTable').DataTable({
        order: [[0, 'asc']], // Sort by rank
        pageLength: 50,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> Export CSV',
                className: 'btn btn-success btn-sm',
                title: 'Staking_Leaderboard_' + new Date().toISOString().split('T')[0]
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
