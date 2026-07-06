@extends('layouts.app')
@section('content')
@include('admin.topmenu')
@include('admin.sidebar')
<div class="main-panel bg-light">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Edit Staking Plan</h1>
            </div>

            <!-- APR Change Warning -->
            @if($plan->activeSubscriptions->count() > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong>Important:</strong> This plan has <strong>{{ $plan->activeSubscriptions->count() }}</strong> active subscription(s) at <strong>{{ $plan->apr_percentage }}%</strong> APR.
                        <br>Changing the APR will only affect <strong>new subscriptions</strong>. Existing subscriptions retain their frozen APR rate.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="card-title">Plan Details</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.crypto-staking.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Crypto Selection (Read-only) -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cryptocurrency</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <img src="{{ $plan->cryptoPrice->logo }}" alt="{{ $plan->cryptoPrice->coin_symbol }}" width="20" height="20">
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control" value="{{ $plan->cryptoPrice->coin_symbol }} - {{ $plan->cryptoPrice->name }}" readonly>
                                                <input type="hidden" name="coin_symbol" value="{{ $plan->coin_symbol }}">
                                            </div>
                                            <small class="text-muted">Cryptocurrency cannot be changed after creation</small>
                                        </div>
                                    </div>

                                    <!-- Plan Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Plan Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control" required value="{{ $plan->name }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="3">{{ $plan->description }}</textarea>
                                </div>

                                <div class="row">
                                    <!-- APR Percentage -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="apr_percentage">APR (Annual Percentage Rate) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" name="apr_percentage" id="apr_percentage" class="form-control" required min="0.01" max="100" step="0.01" value="{{ $plan->apr_percentage }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <small id="apr_suggestion" class="text-muted"></small>
                                        </div>
                                    </div>

                                    <!-- Min Stake -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="min_stake">Minimum Stake <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{ $settings->currency }}</span>
                                                </div>
                                                <input type="number" name="min_stake" id="min_stake" class="form-control" required min="1" step="0.01" value="{{ $plan->min_stake }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Max Stake -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="max_stake">Maximum Stake <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{ $settings->currency }}</span>
                                                </div>
                                                <input type="number" name="max_stake" id="max_stake" class="form-control" required min="1" step="0.01" value="{{ $plan->max_stake }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Max Total Staked -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_total_staked">Maximum Total Capacity</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{ $settings->currency }}</span>
                                                </div>
                                                <input type="number" name="max_total_staked" id="max_total_staked" class="form-control" min="0" step="0.01" value="{{ $plan->max_total_staked }}">
                                            </div>
                                            <small class="text-muted">Currently staked: {{ $settings->currency }}{{ number_format($plan->current_total_staked, 2) }}</small>
                                        </div>
                                    </div>

                                    <!-- Lock Duration -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lock_duration">Lock Duration <span class="text-danger">*</span></label>
                                            <select name="lock_duration" id="lock_duration" class="form-control" required>
                                                <option value="Flexible" {{ $plan->lock_duration == 'Flexible' ? 'selected' : '' }}>Flexible (Withdraw Anytime)</option>
                                                <option value="7 Days" {{ $plan->lock_duration == '7 Days' ? 'selected' : '' }}>7 Days</option>
                                                <option value="30 Days" {{ $plan->lock_duration == '30 Days' ? 'selected' : '' }}>30 Days</option>
                                                <option value="90 Days" {{ $plan->lock_duration == '90 Days' ? 'selected' : '' }}>90 Days</option>
                                                <option value="180 Days" {{ $plan->lock_duration == '180 Days' ? 'selected' : '' }}>180 Days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Reward Interval -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reward_interval">Reward Distribution Interval <span class="text-danger">*</span></label>
                                            <select name="reward_interval" id="reward_interval" class="form-control" required>
                                                <option value="5 Minutes" {{ $plan->reward_interval == '5 Minutes' ? 'selected' : '' }}>5 Minutes (Testing)</option>
                                                <option value="30 Minutes" {{ $plan->reward_interval == '30 Minutes' ? 'selected' : '' }}>30 Minutes</option>
                                                <option value="Hourly" {{ $plan->reward_interval == 'Hourly' ? 'selected' : '' }}>Hourly</option>
                                                <option value="Daily" {{ $plan->reward_interval == 'Daily' ? 'selected' : '' }}>Daily</option>
                                                <option value="Weekly" {{ $plan->reward_interval == 'Weekly' ? 'selected' : '' }}>Weekly</option>
                                                <option value="Monthly" {{ $plan->reward_interval == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Early Withdrawal Penalty -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="early_withdrawal_penalty">Early Withdrawal Penalty <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" name="early_withdrawal_penalty" id="early_withdrawal_penalty" class="form-control" required min="0" max="50" step="0.1" value="{{ $plan->early_withdrawal_penalty }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <div id="penalty_preview" class="mt-2 text-info"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Compound Enabled -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Enable Compound Staking?</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="compound_enabled" value="off" class="selectgroup-input" {{ $plan->compound_enabled == 'off' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">Off</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="compound_enabled" value="on" class="selectgroup-input" {{ $plan->compound_enabled == 'on' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">On</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Auto Distribute -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Auto-Distribute Rewards?</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="auto_distribute" value="off" class="selectgroup-input" {{ $plan->auto_distribute == 'off' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">Off</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="auto_distribute" value="on" class="selectgroup-input" {{ $plan->auto_distribute == 'on' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">On</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Plan Status</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="status" value="inactive" class="selectgroup-input" {{ $plan->status == 'inactive' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">Inactive</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="status" value="active" class="selectgroup-input" {{ $plan->status == 'active' ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">Active</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Photo -->
                                @if($plan->photo)
                                <div class="form-group">
                                    <label>Current Plan Image</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $plan->photo) }}" alt="{{ $plan->name }}" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>
                                @endif

                                <!-- Photo Upload -->
                                <div class="form-group">
                                    <label for="photo">Change Plan Image</label>
                                    <input type="file" name="photo" id="photo" class="form-control-file" accept="image/*">
                                    <small class="text-muted">Upload a new image to replace the current one (Max 2MB)</small>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save"></i> Update Staking Plan
                                    </button>
                                    <a href="{{ route('admin.crypto-staking.index') }}" class="btn btn-secondary btn-lg px-5">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Controls -->
            @if($plan->activeSubscriptions->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-danger">
                        <div class="card-header bg-danger text-white">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-exclamation-triangle"></i> Emergency Controls
                            </h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Use these controls carefully as they affect all active subscriptions for this plan.</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Pause/Resume Distributions</h5>
                                    <p class="text-muted">Temporarily stop reward distributions for this plan only.</p>
                                    <form action="{{ route('admin.crypto-staking.toggle-pause', $plan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if($plan->distribution_paused == 'off')
                                        <button type="submit" class="btn btn-warning btn-lg">
                                            <i class="fas fa-pause-circle"></i> Pause Distributions
                                        </button>
                                        @else
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-play-circle"></i> Resume Distributions
                                        </button>
                                        @endif
                                    </form>
                                </div>

                                <div class="col-md-6">
                                    <h5>Force Complete All Subscriptions</h5>
                                    <p class="text-muted">Immediately complete all {{ $plan->activeSubscriptions->count() }} active subscriptions.</p>
                                    <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#forceCompleteModal">
                                        <i class="fas fa-stop-circle"></i> Force Complete All
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Subscription Stats -->
            @if($plan->subscriptions->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="card-title">Subscription Statistics</h4>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h2 class="text-primary">{{ $plan->subscriptions->count() }}</h2>
                                    <p class="text-muted">Total Subscriptions</p>
                                </div>
                                <div class="col-md-3">
                                    <h2 class="text-success">{{ $plan->activeSubscriptions->count() }}</h2>
                                    <p class="text-muted">Active</p>
                                </div>
                                <div class="col-md-3">
                                    <h2 class="text-info">{{ $settings->currency }}{{ number_format($plan->current_total_staked, 2) }}</h2>
                                    <p class="text-muted">Total Staked</p>
                                </div>
                                <div class="col-md-3">
                                    <h2 class="text-warning">{{ $settings->currency }}{{ number_format($plan->subscriptions->sum('current_rewards'), 2) }}</h2>
                                    <p class="text-muted">Total Rewards Earned</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Force Complete Modal -->
<div class="modal fade" id="forceCompleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Force Complete All Subscriptions</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.crypto-staking.force-complete-all', $plan->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-danger"><strong>Warning:</strong> This will immediately complete all {{ $plan->activeSubscriptions->count() }} active subscriptions and return funds to users.</p>

                    <div class="form-group">
                        <label>Apply Early Withdrawal Penalty?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="apply_penalty" id="penalty_yes" value="yes">
                            <label class="form-check-label" for="penalty_yes">
                                Yes - Apply {{ $plan->early_withdrawal_penalty }}% penalty on rewards
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="apply_penalty" id="penalty_no" value="no" checked>
                            <label class="form-check-label" for="penalty_no">
                                No - Return full rewards
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason">Reason for Force Completion <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" required placeholder="Explain why you are force completing all subscriptions..."></textarea>
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

@push('scripts')
<script>
$(document).ready(function() {
    const symbol = '{{ $plan->coin_symbol }}';
    const aprSuggestions = {
        'USDT': '10-12% (Stablecoin)', 'USDC': '10-12% (Stablecoin)', 'DAI': '10-12% (Stablecoin)',
        'BTC': '5-8% (Major)', 'ETH': '5-8% (Major)', 'WBTC': '5-8% (Major)',
        'BNB': '10-15% (Layer 1)', 'SOL': '10-15% (Layer 1)', 'ADA': '10-15% (Layer 1)'
    };
    const suggestion = aprSuggestions[symbol] || '15-25% (High-Risk)';
    $('#apr_suggestion').html(`<i class="fas fa-info-circle"></i> Suggested APR: <strong>${suggestion}</strong>`);

    function updatePenaltyPreview() {
        const penalty = parseFloat($('#early_withdrawal_penalty').val()) || 0;
        const exampleReward = 100;
        const penaltyAmount = (exampleReward * penalty / 100).toFixed(2);
        const netReward = (exampleReward - penaltyAmount).toFixed(2);
        $('#penalty_preview').html(`Example: {{ $settings->currency }}${exampleReward} rewards → {{ $settings->currency }}${penaltyAmount} penalty = {{ $settings->currency }}${netReward} net`);
    }

    $('#early_withdrawal_penalty').on('input', updatePenaltyPreview);
    updatePenaltyPreview();
});
</script>
@endpush
@endsection
