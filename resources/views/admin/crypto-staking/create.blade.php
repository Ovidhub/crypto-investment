@extends('layouts.app')
@section('content')
@include('admin.topmenu')
@include('admin.sidebar')
<div class="main-panel bg-light">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Create Crypto Staking Plan</h1>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="card-title">Plan Details</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.crypto-staking.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <!-- Crypto Selection -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="coin_symbol">Cryptocurrency <span class="text-danger">*</span></label>
                                            <select name="coin_symbol" id="coin_symbol" class="form-control select2" required>
                                                <option value="">Select Cryptocurrency</option>
                                                @foreach($cryptos as $crypto)
                                                <option value="{{ $crypto->coin_symbol }}" data-logo="{{ $crypto->logo }}" data-apr="{{ $crypto->coin_symbol }}">
                                                    {{ $crypto->coin_symbol }} - {{ $crypto->name }} ({{ $settings->currency }}{{ number_format($crypto->price_usd, 2) }})
                                                </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Select the cryptocurrency for this staking plan</small>
                                        </div>
                                    </div>

                                    <!-- Plan Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Plan Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control" required placeholder="e.g., Bitcoin 30-Day Stake">
                                            <small class="text-muted">Descriptive name for this staking plan</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Describe the benefits and features of this staking plan..."></textarea>
                                </div>

                                <div class="row">
                                    <!-- APR Percentage -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="apr_percentage">APR (Annual Percentage Rate) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" name="apr_percentage" id="apr_percentage" class="form-control" required min="0.01" max="100" step="0.01" placeholder="10.00">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <small id="apr_suggestion" class="text-muted">Suggested APR will appear here</small>
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
                                                <input type="number" name="min_stake" id="min_stake" class="form-control" required min="1" step="0.01" placeholder="100.00">
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
                                                <input type="number" name="max_stake" id="max_stake" class="form-control" required min="1" step="0.01" placeholder="10000.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Max Total Staked -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_total_staked">Maximum Total Capacity (Optional)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{ $settings->currency }}</span>
                                                </div>
                                                <input type="number" name="max_total_staked" id="max_total_staked" class="form-control" min="0" step="0.01" placeholder="Leave empty for unlimited">
                                            </div>
                                            <small class="text-muted">Total amount that can be staked across all users</small>
                                        </div>
                                    </div>

                                    <!-- Lock Duration -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lock_duration">Lock Duration <span class="text-danger">*</span></label>
                                            <select name="lock_duration" id="lock_duration" class="form-control" required>
                                                <option value="">Select Duration</option>
                                                <option value="Flexible">Flexible (Withdraw Anytime)</option>
                                                <option value="7 Days">7 Days</option>
                                                <option value="30 Days" selected>30 Days</option>
                                                <option value="90 Days">90 Days</option>
                                                <option value="180 Days">180 Days</option>
                                            </select>
                                            <small class="text-muted">How long funds will be locked</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Reward Interval -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reward_interval">Reward Distribution Interval <span class="text-danger">*</span></label>
                                            <select name="reward_interval" id="reward_interval" class="form-control" required>
                                                <option value="">Select Interval</option>
                                                <option value="5 Minutes">5 Minutes (Testing)</option>
                                                <option value="30 Minutes">30 Minutes</option>
                                                <option value="Hourly">Hourly</option>
                                                <option value="Daily" selected>Daily</option>
                                                <option value="Weekly">Weekly</option>
                                                <option value="Monthly">Monthly</option>
                                            </select>
                                            <small class="text-muted">How often rewards are distributed</small>
                                        </div>
                                    </div>

                                    <!-- Early Withdrawal Penalty -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="early_withdrawal_penalty">Early Withdrawal Penalty <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" name="early_withdrawal_penalty" id="early_withdrawal_penalty" class="form-control" required min="0" max="50" step="0.1" value="10" placeholder="10.0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <small class="text-muted">Penalty applied on rewards for early withdrawal (locked plans only)</small>
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
                                                    <input type="radio" name="compound_enabled" value="off" class="selectgroup-input" checked>
                                                    <span class="selectgroup-button">Off</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="compound_enabled" value="on" class="selectgroup-input">
                                                    <span class="selectgroup-button">On</span>
                                                </label>
                                            </div>
                                            <small class="text-muted">Allow users to auto-restake rewards</small>
                                        </div>
                                    </div>

                                    <!-- Auto Distribute -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Auto-Distribute Rewards?</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="auto_distribute" value="off" class="selectgroup-input">
                                                    <span class="selectgroup-button">Off</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="auto_distribute" value="on" class="selectgroup-input" checked>
                                                    <span class="selectgroup-button">On</span>
                                                </label>
                                            </div>
                                            <small class="text-muted">Enable automatic reward distribution</small>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Plan Status</label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="status" value="inactive" class="selectgroup-input">
                                                    <span class="selectgroup-button">Inactive</span>
                                                </label>
                                                <label class="selectgroup-item">
                                                    <input type="radio" name="status" value="active" class="selectgroup-input" checked>
                                                    <span class="selectgroup-button">Active</span>
                                                </label>
                                            </div>
                                            <small class="text-muted">Plan visibility to users</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo Upload -->
                                <div class="form-group">
                                    <label for="photo">Plan Image (Optional)</label>
                                    <input type="file" name="photo" id="photo" class="form-control-file" accept="image/*">
                                    <small class="text-muted">Upload an image for this staking plan (Max 2MB)</small>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save"></i> Create Staking Plan
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
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // APR Suggestions based on crypto type
    const aprSuggestions = {
        'USDT': '10-12% (Stablecoin)',
        'USDC': '10-12% (Stablecoin)',
        'DAI': '10-12% (Stablecoin)',
        'USDS': '10-12% (Stablecoin)',
        'USDE': '10-12% (Stablecoin)',
        'PYUSD': '10-12% (Stablecoin)',
        'USD1': '10-12% (Stablecoin)',
        'BTC': '5-8% (Major Coin)',
        'ETH': '5-8% (Major Coin)',
        'WBTC': '5-8% (Major Coin)',
        'WETH': '5-8% (Major Coin)',
        'STETH': '5-8% (Major Coin)',
        'BNB': '10-15% (Layer 1)',
        'SOL': '10-15% (Layer 1)',
        'ADA': '10-15% (Layer 1)',
        'AVAX': '10-15% (Layer 1)',
        'DOT': '10-15% (Layer 1)',
        'LINK': '10-15% (Layer 1)'
    };

    $('#coin_symbol').on('change', function() {
        const symbol = $(this).val();
        const suggestion = aprSuggestions[symbol] || '15-25% (High-Risk)';
        $('#apr_suggestion').html(`<i class="fas fa-info-circle"></i> Suggested APR: <strong>${suggestion}</strong>`);
    });

    // APR validation with color coding
    $('#apr_percentage').on('input', function() {
        const apr = parseFloat($(this).val());
        const symbol = $('#coin_symbol').val();
        let message = '';
        let color = 'text-success';

        if (apr < 5) {
            message = '<i class="fas fa-arrow-down"></i> Low APR - May not be attractive to users';
            color = 'text-warning';
        } else if (apr >= 5 && apr <= 15) {
            message = '<i class="fas fa-check-circle"></i> Good APR range';
            color = 'text-success';
        } else if (apr > 15 && apr <= 30) {
            message = '<i class="fas fa-exclamation-triangle"></i> High APR - Ensure sustainability';
            color = 'text-warning';
        } else {
            message = '<i class="fas fa-exclamation-circle"></i> Very High APR - May be unsustainable';
            color = 'text-danger';
        }

        $('#apr_suggestion').removeClass('text-muted text-success text-warning text-danger').addClass(color).append(`<br>${message}`);
    });

    // Penalty preview calculator
    function updatePenaltyPreview() {
        const penalty = parseFloat($('#early_withdrawal_penalty').val()) || 0;
        const exampleReward = 100;
        const penaltyAmount = (exampleReward * penalty / 100).toFixed(2);
        const netReward = (exampleReward - penaltyAmount).toFixed(2);

        $('#penalty_preview').html(`Example: If user has {{ $settings->currency }}${exampleReward} in rewards, penalty = {{ $settings->currency }}${penaltyAmount}, net return = {{ $settings->currency }}${netReward}`);
    }

    $('#early_withdrawal_penalty').on('input', updatePenaltyPreview);
    updatePenaltyPreview();

    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            placeholder: 'Select an option',
            allowClear: true
        });
    }
});
</script>
@endpush
@endsection
