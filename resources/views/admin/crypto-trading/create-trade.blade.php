@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel bg-light">
    <div class="content">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Create Manual Crypto Trade</h1>
            </div>

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Manual Trade Form</h4>
                        </div>
                        <div class="card-body">
                            @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="{{ route('admin.crypto-trading.store-trade') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="user_id">Select User *</label>
                                    <select name="user_id" id="user_id" class="form-control select2" required>
                                        <option value="">-- Select User --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', request('user_id')) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }}) - Balance: ${{ number_format($user->account_bal, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="coin_symbol">Cryptocurrency *</label>
                                    <select name="coin_symbol" id="coin_symbol" class="form-control select2" required onchange="updatePrice()">
                                        <option value="">-- Select Cryptocurrency --</option>
                                        @foreach($cryptos as $crypto)
                                            <option value="{{ $crypto->coin_symbol }}" data-price="{{ $crypto->price_usd }}" {{ old('coin_symbol') == $crypto->coin_symbol ? 'selected' : '' }}>
                                                {{ $crypto->name }} ({{ $crypto->coin_symbol }}) - ${{ number_format($crypto->price_usd, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="trade_type">Trade Type *</label>
                                    <select name="trade_type" id="trade_type" class="form-control" required>
                                        <option value="buy" {{ old('trade_type') == 'buy' ? 'selected' : '' }}>Buy</option>
                                        <option value="sell" {{ old('trade_type') == 'sell' ? 'selected' : '' }}>Sell</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="quantity">Quantity *</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" step="0.00000001" min="0.00000001" value="{{ old('quantity') }}" required oninput="calculateTotal()">
                                    <small class="form-text text-muted">Enter the quantity of cryptocurrency</small>
                                </div>

                                <div class="form-group">
                                    <label for="price_usd">Price per Unit (USD) *</label>
                                    <input type="number" name="price_usd" id="price_usd" class="form-control" step="0.01" min="0.01" value="{{ old('price_usd') }}" required oninput="calculateTotal()">
                                    <small class="form-text text-muted">Current market price will be auto-filled</small>
                                </div>

                                <div class="form-group">
                                    <label for="fee_percent">Fee Percentage</label>
                                    <input type="number" name="fee_percent" id="fee_percent" class="form-control" step="0.01" min="0" max="100" value="{{ old('fee_percent', $settings->crypto_trade_fee_percent ?? 0.25) }}" oninput="calculateTotal()">
                                    <small class="form-text text-muted">Default: {{ $settings->crypto_trade_fee_percent ?? 0.25 }}%</small>
                                </div>

                                <!-- Calculation Summary -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Trade Summary</h5>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td>Subtotal:</td>
                                                <td class="text-right"><strong id="displaySubtotal">$0.00</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Fee:</td>
                                                <td class="text-right"><strong id="displayFee">$0.00</strong></td>
                                            </tr>
                                            <tr class="border-top">
                                                <td><strong>Net Total:</strong></td>
                                                <td class="text-right"><strong class="text-primary" id="displayTotal">$0.00</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Admin Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                                    <small class="form-text text-muted">Optional notes about this trade (visible to admins only)</small>
                                </div>

                                <div class="form-group">
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Important:</strong> This will immediately update the user's account balance.
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Buy trades</strong> will deduct the net amount from the user's balance</li>
                                            <li><strong>Sell trades</strong> will add the net amount to the user's balance</li>
                                            <li>A transaction record will be created in the user's transaction history</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-save"></i> Create Trade
                                    </button>
                                    <a href="{{ route('admin.crypto-trading.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fa fa-times"></i> Cancel
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

@endsection

@push('scripts')
<script>
function updatePrice() {
    var select = document.getElementById('coin_symbol');
    var selectedOption = select.options[select.selectedIndex];
    var price = selectedOption.getAttribute('data-price');
    
    if (price) {
        document.getElementById('price_usd').value = parseFloat(price).toFixed(2);
        calculateTotal();
    }
}

function calculateTotal() {
    var quantity = parseFloat(document.getElementById('quantity').value) || 0;
    var price = parseFloat(document.getElementById('price_usd').value) || 0;
    var feePercent = parseFloat(document.getElementById('fee_percent').value) || 0;
    
    var subtotal = quantity * price;
    var fee = (subtotal * feePercent) / 100;
    var total = subtotal + fee;
    
    document.getElementById('displaySubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('displayFee').textContent = '$' + fee.toFixed(2);
    document.getElementById('displayTotal').textContent = '$' + total.toFixed(2);
}

$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true
    });
});
</script>
@endpush
