@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Stock Trading Settings</h1>
            </div>

            <div class="mb-5 row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card shadow">
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error!</strong> Please fix the following issues:
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form action="{{ route('admin.stock-trading.update-settings') }}" method="POST">
                                @csrf

                                <!-- Enable Stock Trading -->
                            <div class="form-group">
                                <label>Enable Stock Trading Module</label>
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="enable_stock_trading" value="on" class="selectgroup-input" 
                                            {{ $settings->enable_stock_trading == 'on' ? 'checked' : '' }}>
                                        <span class="selectgroup-button">Enabled</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="enable_stock_trading" value="off" class="selectgroup-input"
                                            {{ $settings->enable_stock_trading == 'off' ? 'checked' : '' }}>
                                        <span class="selectgroup-button">Disabled</span>
                                    </label>
                                </div>
                                <small class="form-text text-muted">Enable or disable the stock trading module for all users</small>
                            </div>

                            <!-- Twelve Data API Key -->
                            <div class="form-group">
                                <label for="twelvedata_api_key">Twelve Data API Key *</label>
                                <input type="text" class="form-control @error('twelvedata_api_key') is-invalid @enderror" 
                                    id="twelvedata_api_key" name="twelvedata_api_key" 
                                    value="{{ old('twelvedata_api_key', $settings->twelvedata_api_key) }}" required>
                                <small class="form-text text-muted">
                                    Get your free API key from <a href="https://twelvedata.com" target="_blank">twelvedata.com</a>
                                </small>
                                @error('twelvedata_api_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Test API Connection -->
                            <div class="form-group">
                                <button type="button" id="testApiBtn" class="btn btn-info">
                                    <i class="fa fa-plug"></i> Test API Connection
                                </button>
                                <div id="apiTestResult" class="mt-2"></div>
                            </div>

                            <!-- Market Hours Detection -->
                            <div class="form-group">
                                <label>Stock Market Hours Detection</label>
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="stock_market_hours_enabled" value="on" class="selectgroup-input"
                                            {{ $settings->stock_market_hours_enabled == 'on' ? 'checked' : '' }}>
                                        <span class="selectgroup-button">Enabled</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="stock_market_hours_enabled" value="off" class="selectgroup-input"
                                            {{ $settings->stock_market_hours_enabled == 'off' ? 'checked' : '' }}>
                                        <span class="selectgroup-button">Disabled</span>
                                    </label>
                                </div>
                                <small class="form-text text-muted">When enabled, price updates skip NYSE holidays and weekends</small>
                            </div>

                            <!-- Max Watchlist Items -->
                            <div class="form-group">
                                <label for="max_stock_watchlist_items">Maximum Watchlist Items Per User *</label>
                                <input type="number" class="form-control @error('max_stock_watchlist_items') is-invalid @enderror" 
                                    id="max_stock_watchlist_items" name="max_stock_watchlist_items" 
                                    min="1" max="100" 
                                    value="{{ old('max_stock_watchlist_items', $settings->max_stock_watchlist_items) }}" required>
                                <small class="form-text text-muted">Limit the number of stocks each user can add to their watchlist</small>
                                @error('max_stock_watchlist_items')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Trade Fee Percent -->
                            <div class="form-group">
                                <label for="stock_trade_fee_percent">Trade Fee Percentage *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('stock_trade_fee_percent') is-invalid @enderror" 
                                        id="stock_trade_fee_percent" name="stock_trade_fee_percent" 
                                        min="0" max="10" step="0.01" 
                                        value="{{ old('stock_trade_fee_percent', $settings->stock_trade_fee_percent) }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Fee charged on each trade (0-10%). For buy: user pays total+fee, for sell: user receives total-fee</small>
                                @error('stock_trade_fee_percent')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group text-right mt-4">
                                <a href="{{ route('admin.stock-trading.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to Trades
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Settings
                                </button>
                            </div>
                            </form>

                            <hr class="my-4">
                            <h5 class="mb-3">Force Update & Bulk Import</h5>

                            <!-- Force Update Section -->
                            <div class="form-group">
                                <label>Force Update Stock Prices</label>
                                @if($lastForceUpdate)
                                <div class="alert alert-info">
                                    <small>
                                        <strong>Last forced update:</strong><br>
                                        {{ $lastForceUpdate->diffForHumans() }}
                                    </small>
                                </div>
                                @endif

                                @if($canForceUpdate)
                                <p class="text-muted">Manually trigger stock price updates, bypassing market hours check.</p>
                                <button type="button" id="forceUpdateBtn" class="btn btn-warning">
                                    <i class="fa fa-refresh"></i> Force Update Now
                                </button>
                                @else
                                <div class="alert alert-warning">
                                    <small>
                                        <i class="fa fa-clock-o"></i> Please wait {{ $cooldownMinutes }} minutes between force updates.<br>
                                        Next available: {{ $nextForceUpdate->diffForHumans() }}
                                    </small>
                                </div>
                                <button type="button" class="btn btn-warning" disabled>
                                    <i class="fa fa-refresh"></i> Force Update (Cooldown)
                                </button>
                                @endif
                                
                                <div id="forceUpdateResult" class="mt-3"></div>
                            </div>

                            <hr class="my-4">

                            <!-- Bulk Import Section -->
                            <div class="form-group">
                                <label>Bulk Import Stocks</label>
                                <p class="text-muted">Add popular stock symbols to the system.</p>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <form action="{{ route('admin.stock-trading.add-preset') }}" method="POST" class="mb-2">
                                            @csrf
                                            <input type="hidden" name="preset" value="sp500">
                                            <button type="submit" class="btn btn-info btn-block">
                                                <i class="fa fa-plus"></i> Add S&P 500 (30)
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <form action="{{ route('admin.stock-trading.add-preset') }}" method="POST" class="mb-2">
                                            @csrf
                                            <input type="hidden" name="preset" value="nasdaq100">
                                            <button type="submit" class="btn btn-info btn-block">
                                                <i class="fa fa-plus"></i> Add NASDAQ 100 (30)
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <form action="{{ route('admin.stock-trading.add-preset') }}" method="POST" class="mb-2">
                                            @csrf
                                            <input type="hidden" name="preset" value="dow30">
                                            <button type="submit" class="btn btn-info btn-block">
                                                <i class="fa fa-plus"></i> Add DOW 30 (30)
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <form action="{{ route('admin.stock-trading.bulk-import') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                    @csrf
                                    <div class="form-group">
                                        <label>Or Upload CSV File</label>
                                        <input type="file" class="form-control-file" name="csv_file" accept=".csv" required>
                                        <small class="form-text text-muted">CSV format: symbol, name, exchange</small>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-upload"></i> Upload CSV
                                    </button>
                                </form>
                            </div>

                            <div class="alert alert-info mt-4" role="alert">
                                <i class="fa fa-info-circle"></i> <strong>Information:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>Scheduled Updates:</strong> Every 15 minutes</li>
                                    <li><strong>NYSE Trading Hours:</strong> 9:30 AM - 4:00 PM ET</li>
                                    <li><strong>Cooldown:</strong> 5 minutes between manual updates</li>
                                    <li><strong>API Limits:</strong> 8 requests per minute</li>
                                </ul>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<script>
$(document).ready(function() {
    // Test API Connection
    $('#testApiBtn').click(function() {
        const btn = $(this);
        const resultDiv = $('#apiTestResult');
        const apiKey = $('#twelvedata_api_key').val();

        if (!apiKey) {
            resultDiv.html('<div class="alert alert-warning">Please enter an API key first</div>');
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');
        resultDiv.html('');

        $.ajax({
            url: '{{ route('admin.stock-trading.test-api') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                api_key: apiKey
            },
            success: function(response) {
                if (response.success) {
                    resultDiv.html('<div class="alert alert-success"><i class="fa fa-check"></i> API connection successful!</div>');
                } else {
                    resultDiv.html('<div class="alert alert-danger"><i class="fa fa-times"></i> ' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'API test failed';
                resultDiv.html('<div class="alert alert-danger"><i class="fa fa-times"></i> ' + error + '</div>');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-plug"></i> Test API Connection');
            }
        });
    });

    // Force Update
    $('#forceUpdateBtn').click(function() {
        if (!confirm('Force update all stock prices? This will trigger price updates regardless of market hours.')) {
            return;
        }

        const btn = $(this);
        const resultDiv = $('#forceUpdateResult');

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        resultDiv.html('');

        $.ajax({
            url: '{{ route('admin.stock-trading.force-update') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    resultDiv.html('<div class="alert alert-success"><i class="fa fa-check"></i> ' + response.message + '</div>');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    resultDiv.html('<div class="alert alert-danger"><i class="fa fa-times"></i> ' + response.message + '</div>');
                    btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Force Update Now');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Force update failed';
                resultDiv.html('<div class="alert alert-danger"><i class="fa fa-times"></i> ' + error + '</div>');
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Force Update Now');
            }
        });
    });
});
</script>
@endsection
