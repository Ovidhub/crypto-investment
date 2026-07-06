@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Create Manual Trade</h1>
                <p class="text-muted">Create a trade on behalf of a user (for corrections, disputes, etc.)</p>
            </div>

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
                            <h4 class="card-title">Trade Details</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.stock-trading.store-trade') }}" method="POST" id="createTradeForm">
                                @csrf
                                
                                <div class="form-group">
                                    <label for="user_id">Select User <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control" required onchange="updateUserInfo()">
                                        <option value="">-- Select User --</option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                            data-balance="{{ $user->account_bal }}"
                                            data-email="{{ $user->email }}"
                                            {{ $selectedUser && $selectedUser->id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">The user this trade will be assigned to</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="stock_symbol">Stock Symbol <span class="text-danger">*</span></label>
                                            <input type="text" name="stock_symbol" id="stock_symbol" class="form-control" 
                                                list="stocksList" required placeholder="e.g., AAPL" style="text-transform: uppercase;">
                                            <datalist id="stocksList">
                                                @foreach($stocks as $stock)
                                                <option value="{{ $stock->symbol }}">{{ $stock->name }}</option>
                                                @endforeach
                                            </datalist>
                                            <small class="text-muted">Type or select from available stocks</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Trade Type <span class="text-danger">*</span></label>
                                            <div class="d-flex">
                                                <div class="custom-control custom-radio mr-3">
                                                    <input type="radio" id="buyRadio" name="trade_type" value="buy" class="custom-control-input" checked onchange="calculateTotals()">
                                                    <label class="custom-control-label" for="buyRadio">Buy</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="sellRadio" name="trade_type" value="sell" class="custom-control-input" onchange="calculateTotals()">
                                                    <label class="custom-control-label" for="sellRadio">Sell</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                            <input type="number" name="quantity" id="quantity" class="form-control" 
                                                step="0.00000001" min="0.00000001" required
                                                oninput="calculateTotals()">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">Price per Share <span class="text-danger">*</span></label>
                                            <input type="number" name="price" id="price" class="form-control" 
                                                step="0.01" min="0.01" required
                                                oninput="calculateTotals()">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Admin Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" 
                                        placeholder="Reason for manual trade creation (e.g., Customer dispute resolution, Trade correction, etc.)"></textarea>
                                    <small class="text-muted">This will be appended to trade notes along with admin name and timestamp</small>
                                </div>

                                <hr>

                                <h5 class="mb-3">Calculated Amounts</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td><strong>Subtotal</strong></td>
                                                <td class="text-right" id="subtotal">$0.00</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fee ({{ $feePercent }}%)</strong></td>
                                                <td class="text-right" id="fee">$0.00</td>
                                            </tr>
                                            <tr class="table-info">
                                                <td><strong>Net Amount</strong></td>
                                                <td class="text-right"><strong id="netAmount">$0.00</strong></td>
                                            </tr>
                                            <tr>
                                                <td><strong>User Balance Impact</strong></td>
                                                <td class="text-right" id="balanceImpact">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-warning">
                                    <strong><i class="fa fa-exclamation-triangle"></i> Admin Override Notice</strong>
                                    <p class="mb-0">This trade will be created with "completed" status and user balance will be immediately adjusted. Negative balances are allowed for admin corrections.</p>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-plus-circle"></i> Create Trade
                                    </button>
                                    <a href="{{ route('admin.stock-trading.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fa fa-arrow-left"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Selected User Info -->
                    <div class="card" id="userInfoCard" style="display: none;">
                        <div class="card-header">
                            <h4 class="card-title">Selected User Info</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Name:</strong> <span id="userName">-</span></p>
                            <p class="mb-2"><strong>Email:</strong> <span id="userEmail">-</span></p>
                            <p class="mb-2">
                                <strong>Current Balance:</strong> 
                                <span id="currentBalance" class="text-success">$0.00</span>
                            </p>
                            <p class="mb-0">
                                <strong>Balance After Trade:</strong> 
                                <span id="balanceAfter" class="font-weight-bold">$0.00</span>
                            </p>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card bg-info">
                        <div class="card-body">
                            <h5 class="text-white"><i class="fa fa-info-circle"></i> Usage Guide</h5>
                            <ul class="text-white mb-0">
                                <li>Use for dispute resolutions</li>
                                <li>Correct erroneous trades</li>
                                <li>Adjust user positions manually</li>
                                <li>Negative balances are allowed</li>
                                <li>All actions are logged</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const feePercent = parseFloat('{{ $feePercent }}');

function updateUserInfo() {
    const userSelect = document.getElementById('user_id');
    const selectedOption = userSelect.options[userSelect.selectedIndex];
    
    if (userSelect.value) {
        const balance = parseFloat(selectedOption.dataset.balance);
        const email = selectedOption.dataset.email;
        const name = selectedOption.text.split('(')[0].trim();
        
        document.getElementById('userName').textContent = name;
        document.getElementById('userEmail').textContent = email;
        document.getElementById('currentBalance').textContent = '$' + balance.toFixed(2);
        document.getElementById('userInfoCard').style.display = 'block';
        
        calculateTotals();
    } else {
        document.getElementById('userInfoCard').style.display = 'none';
    }
}

function calculateTotals() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const price = parseFloat(document.getElementById('price').value) || 0;
    const tradeType = document.querySelector('input[name="trade_type"]:checked').value;
    const userSelect = document.getElementById('user_id');
    
    const subtotal = quantity * price;
    const fee = subtotal * (feePercent / 100);
    const netAmount = tradeType === 'buy' ? (subtotal + fee) : (subtotal - fee);
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('fee').textContent = '$' + fee.toFixed(2);
    document.getElementById('netAmount').textContent = '$' + netAmount.toFixed(2);
    
    if (userSelect.value) {
        const currentBalance = parseFloat(userSelect.options[userSelect.selectedIndex].dataset.balance);
        const balanceChange = tradeType === 'buy' ? -netAmount : netAmount;
        const balanceAfter = currentBalance + balanceChange;
        
        document.getElementById('balanceImpact').innerHTML = 
            (balanceChange >= 0 ? '+' : '') + '$' + balanceChange.toFixed(2) +
            ' <span class="text-muted">(' + (tradeType === 'buy' ? 'deduct' : 'credit') + ')</span>';
        
        const balanceAfterEl = document.getElementById('balanceAfter');
        balanceAfterEl.textContent = '$' + balanceAfter.toFixed(2);
        
        if (balanceAfter < 0) {
            balanceAfterEl.className = 'font-weight-bold text-danger';
        } else {
            balanceAfterEl.className = 'font-weight-bold text-success';
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('user_id').value) {
        updateUserInfo();
    }
});

// Confirm before submit
document.getElementById('createTradeForm').addEventListener('submit', function(e) {
    const userName = document.getElementById('userName').textContent;
    const symbol = document.getElementById('stock_symbol').value.toUpperCase();
    const tradeType = document.querySelector('input[name="trade_type"]:checked').value;
    const quantity = document.getElementById('quantity').value;
    const price = document.getElementById('price').value;
    const netAmount = document.getElementById('netAmount').textContent;
    
    if (!confirm(`Confirm manual trade creation:\n\nUser: ${userName}\nStock: ${symbol}\nType: ${tradeType.toUpperCase()}\nQuantity: ${quantity}\nPrice: $${price}\nNet Amount: ${netAmount}\n\nThis will immediately affect user balance.`)) {
        e.preventDefault();
    }
});
</script>

@endsection
