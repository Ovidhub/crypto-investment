@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel bg-light">
    <div class="content">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Edit Crypto Trade #{{ $trade->id }}</h1>
                <p class="text-muted">User: {{ $trade->user->name }} ({{ $trade->user->email }})</p>
            </div>

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Trade</h4>
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

                            <form action="{{ route('admin.crypto-trading.update', $trade->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label>User</label>
                                    <input type="text" class="form-control" value="{{ $trade->user->name }} ({{ $trade->user->email }})" disabled>
                                    <small class="form-text text-muted">Current Balance: ${{ number_format($trade->user->account_bal, 2) }}</small>
                                </div>

                                <div class="form-group">
                                    <label for="coin_symbol">Cryptocurrency *</label>
                                    <select name="coin_symbol" id="coin_symbol" class="form-control select2" required onchange="updatePrice()">
                                        @foreach($cryptos as $crypto)
                                            <option value="{{ $crypto->coin_symbol }}" data-price="{{ $crypto->price_usd }}" {{ $trade->coin_symbol == $crypto->coin_symbol ? 'selected' : '' }}>
                                                {{ $crypto->name }} ({{ $crypto->coin_symbol }}) - ${{ number_format($crypto->price_usd, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="trade_type">Trade Type *</label>
                                    <select name="trade_type" id="trade_type" class="form-control" required>
                                        <option value="buy" {{ $trade->trade_type == 'buy' ? 'selected' : '' }}>Buy</option>
                                        <option value="sell" {{ $trade->trade_type == 'sell' ? 'selected' : '' }}>Sell</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="quantity">Quantity *</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" step="0.00000001" min="0.00000001" value="{{ $trade->quantity }}" required oninput="calculateTotal()">
                                </div>

                                <div class="form-group">
                                    <label for="price_usd">Price per Unit (USD) *</label>
                                    <input type="number" name="price_usd" id="price_usd" class="form-control" step="0.01" min="0.01" value="{{ $trade->price_usd }}" required oninput="calculateTotal()">
                                </div>

                                <div class="form-group">
                                    <label for="fee_percent">Fee Percentage</label>
                                    <input type="number" name="fee_percent" id="fee_percent" class="form-control" step="0.01" min="0" max="100" value="{{ $trade->fee_percent }}" oninput="calculateTotal()">
                                </div>

                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="pending" {{ $trade->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ $trade->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $trade->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <!-- Balance Impact Preview -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Balance Reconciliation Preview</h5>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td>Old Trade Net Amount:</td>
                                                <td class="text-right"><strong>${{ number_format($trade->net_amount, 2) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>New Trade Net Amount:</td>
                                                <td class="text-right"><strong class="text-primary" id="displayNewTotal">$0.00</strong></td>
                                            </tr>
                                            <tr class="border-top">
                                                <td><strong>Balance Adjustment:</strong></td>
                                                <td class="text-right"><strong id="balanceAdjustment">$0.00</strong></td>
                                            </tr>
                                        </table>
                                        <small class="text-muted">The system will automatically reverse the old transaction and apply the new one</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Admin Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ $trade->notes }}</textarea>
                                </div>

                                <div class="form-group">
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Important:</strong> Editing this trade will:
                                        <ul class="mb-0 mt-2">
                                            <li>Reverse the original balance impact</li>
                                            <li>Apply the new balance impact based on updated values</li>
                                            <li>Update all trade details</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-save"></i> Update Trade
                                    </button>
                                    <a href="{{ route('admin.crypto-trading.user-trades', $trade->user_id) }}" class="btn btn-secondary btn-lg">
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
const oldNetAmount = {{ $trade->net_amount }};
const oldTradeType = '{{ $trade->trade_type }}';

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
    var newTotal = subtotal + fee;
    
    document.getElementById('displayNewTotal').textContent = '$' + newTotal.toFixed(2);
    
    // Calculate balance adjustment
    var tradeType = document.getElementById('trade_type').value;
    var adjustment = 0;
    
    // Reverse old impact
    if (oldTradeType === 'buy') {
        adjustment += oldNetAmount; // Refund old buy
    } else {
        adjustment -= oldNetAmount; // Reverse old sell
    }
    
    // Apply new impact
    if (tradeType === 'buy') {
        adjustment -= newTotal; // Deduct new buy
    } else {
        adjustment += newTotal; // Add new sell
    }
    
    var adjustmentText = (adjustment >= 0 ? '+' : '') + '$' + Math.abs(adjustment).toFixed(2);
    document.getElementById('balanceAdjustment').textContent = adjustmentText;
}

$(document).ready(function() {
    $('.select2').select2();
    calculateTotal();
});
</script>
@endpush
