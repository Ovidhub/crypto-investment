@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Edit Trade #{{ $trade->id }}</h1>
            </div>

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Errors:</strong>
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

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Trade Details</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.stock-trading.update', $trade->id) }}" method="POST" id="editTradeForm">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Stock Symbol</label>
                                            <input type="text" class="form-control" value="{{ $trade->stock_symbol }}" readonly>
                                            <small class="text-muted">
                                                @if($trade->stockPrice)
                                                    {{ $trade->stockPrice->name }} • Current: ${{ number_format($trade->stockPrice->price, 2) }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Trade Type</label>
                                            <div class="d-flex">
                                                <div class="custom-control custom-radio mr-3">
                                                    <input type="radio" id="buyRadio" name="trade_type" value="buy" class="custom-control-input" {{ $trade->trade_type === 'buy' ? 'checked' : '' }} onchange="calculateTotals()">
                                                    <label class="custom-control-label" for="buyRadio">Buy</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="sellRadio" name="trade_type" value="sell" class="custom-control-input" {{ $trade->trade_type === 'sell' ? 'checked' : '' }} onchange="calculateTotals()">
                                                    <label class="custom-control-label" for="sellRadio">Sell</label>
                                                </div>
                                            </div>
                                            <small class="text-danger">⚠️ Changing trade type will recalculate user balance</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control" 
                                                value="{{ old('quantity', $trade->quantity) }}" 
                                                step="0.00000001" min="0.00000001" required
                                                oninput="calculateTotals()">
                                            <small class="text-muted">Original: {{ $trade->quantity }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Price per Share</label>
                                            <input type="number" name="price" id="price" class="form-control" 
                                                value="{{ old('price', $trade->price) }}" 
                                                step="0.01" min="0.01" required
                                                oninput="calculateTotals()">
                                            <small class="text-muted">Original: ${{ number_format($trade->price, 2) }}</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <input type="text" class="form-control" value="{{ strtoupper($trade->status) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date Created</label>
                                            <input type="text" class="form-control" value="{{ $trade->created_at->format('M d, Y H:i') }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="mb-3">Calculated Amounts</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="text-center">Original</th>
                                                <th class="text-center">New</th>
                                                <th class="text-center">Change</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Subtotal</strong></td>
                                                <td class="text-right">${{ number_format($trade->total_amount, 2) }}</td>
                                                <td class="text-right" id="newSubtotal">$0.00</td>
                                                <td class="text-right" id="subtotalChange">$0.00</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fee ({{ $feePercent }}%)</strong></td>
                                                <td class="text-right">${{ number_format($trade->fee_amount, 2) }}</td>
                                                <td class="text-right" id="newFee">$0.00</td>
                                                <td class="text-right" id="feeChange">$0.00</td>
                                            </tr>
                                            <tr class="table-info">
                                                <td><strong>Net Amount</strong></td>
                                                <td class="text-right"><strong>${{ number_format($trade->net_amount, 2) }}</strong></td>
                                                <td class="text-right"><strong id="newNet">$0.00</strong></td>
                                                <td class="text-right"><strong id="netChange">$0.00</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-save"></i> Update Trade
                                    </button>
                                    <a href="{{ route('admin.stock-trading.user-trades', $trade->user_id) }}" class="btn btn-secondary btn-lg">
                                        <i class="fa fa-arrow-left"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- User Info -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">User Information</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Name:</strong> {{ $trade->user->name }}</p>
                            <p class="mb-2"><strong>Email:</strong> {{ $trade->user->email }}</p>
                            <p class="mb-2">
                                <strong>Current Balance:</strong> 
                                <span class="text-success">${{ number_format($trade->user->account_bal, 2) }}</span>
                            </p>
                            <p class="mb-0">
                                <strong>Balance After Edit:</strong> 
                                <span id="balanceAfter" class="font-weight-bold">$0.00</span>
                            </p>
                        </div>
                    </div>

                    <!-- Warnings -->
                    @if($trade->status === 'completed')
                    <div class="card bg-warning">
                        <div class="card-body">
                            <h5 class="text-white"><i class="fa fa-exclamation-triangle"></i> Completed Trade</h5>
                            <p class="text-white mb-0">
                                This trade is completed. Editing will:
                            </p>
                            <ul class="text-white">
                                <li>Reverse the original transaction</li>
                                <li>Apply the new transaction amounts</li>
                                <li>Update user's account balance</li>
                                <li>Maintain trade history in notes</li>
                            </ul>
                        </div>
                    </div>
                    @endif

                    <div class="card bg-info">
                        <div class="card-body">
                            <h5 class="text-white"><i class="fa fa-info-circle"></i> Admin Override</h5>
                            <p class="text-white mb-0">
                                You can edit any trade value. Negative balances are allowed for admin corrections.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const feePercent = parseFloat('{{ $feePercent }}');
const originalNetAmount = parseFloat('{{ $trade->net_amount }}');
const originalTradeType = '{{ $trade->trade_type }}';
const userBalance = parseFloat('{{ $trade->user->account_bal }}');
const tradeStatus = '{{ $trade->status }}';

function calculateTotals() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const price = parseFloat(document.getElementById('price').value) || 0;
    const tradeType = document.querySelector('input[name="trade_type"]:checked').value;
    
    // Calculate new amounts
    const newSubtotal = quantity * price;
    const newFee = newSubtotal * (feePercent / 100);
    const newNet = tradeType === 'buy' ? (newSubtotal + newFee) : (newSubtotal - newFee);
    
    // Calculate changes
    const originalSubtotal = parseFloat('{{ $trade->total_amount }}');
    const originalFee = parseFloat('{{ $trade->fee_amount }}');
    
    const subtotalChange = newSubtotal - originalSubtotal;
    const feeChange = newFee - originalFee;
    const netChange = newNet - originalNetAmount;
    
    // Update display
    document.getElementById('newSubtotal').textContent = '$' + newSubtotal.toFixed(2);
    document.getElementById('newFee').textContent = '$' + newFee.toFixed(2);
    document.getElementById('newNet').textContent = '$' + newNet.toFixed(2);
    
    document.getElementById('subtotalChange').textContent = (subtotalChange >= 0 ? '+' : '') + '$' + subtotalChange.toFixed(2);
    document.getElementById('feeChange').textContent = (feeChange >= 0 ? '+' : '') + '$' + feeChange.toFixed(2);
    document.getElementById('netChange').textContent = (netChange >= 0 ? '+' : '') + '$' + netChange.toFixed(2);
    
    // Calculate balance after edit (only for completed trades)
    if (tradeStatus === 'completed') {
        let balanceAdjustment = 0;
        
        // Reverse old transaction
        if (originalTradeType === 'buy') {
            balanceAdjustment += originalNetAmount; // Refund
        } else {
            balanceAdjustment -= originalNetAmount; // Deduct
        }
        
        // Apply new transaction
        if (tradeType === 'buy') {
            balanceAdjustment -= newNet; // Deduct
        } else {
            balanceAdjustment += newNet; // Credit
        }
        
        const balanceAfter = userBalance + balanceAdjustment;
        const balanceAfterEl = document.getElementById('balanceAfter');
        balanceAfterEl.textContent = '$' + balanceAfter.toFixed(2);
        
        // Color code balance
        if (balanceAfter < 0) {
            balanceAfterEl.className = 'font-weight-bold text-danger';
        } else if (balanceAfter < userBalance) {
            balanceAfterEl.className = 'font-weight-bold text-warning';
        } else {
            balanceAfterEl.className = 'font-weight-bold text-success';
        }
    } else {
        document.getElementById('balanceAfter').textContent = 'N/A (not completed)';
    }
}

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
});

// Confirm before submit
document.getElementById('editTradeForm').addEventListener('submit', function(e) {
    const tradeType = document.querySelector('input[name="trade_type"]:checked').value;
    const quantity = document.getElementById('quantity').value;
    const price = document.getElementById('price').value;
    
    if (!confirm(`Confirm trade update:\n\nType: ${tradeType.toUpperCase()}\nQuantity: ${quantity}\nPrice: $${price}\n\nThis will update the trade and adjust user balance if completed.`)) {
        e.preventDefault();
    }
});
</script>

@endsection
