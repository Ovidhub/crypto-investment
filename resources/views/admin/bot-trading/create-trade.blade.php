@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Create Manual Trade</h1>
                <p class="text-muted">
                    Bot: <strong>{{ $subscription->tradingBot->name }}</strong> | 
                    User: <strong>{{ $subscription->user->name }}</strong>
                </p>
            </div>

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> Please fix the following issues:
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endif

            <div class="mb-5 row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card shadow">
                        <div class="card-body">
                            <form action="{{ route('admin.bot-trading.store-trade', $subscription) }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="asset">Select Asset *</label>
                                    <div class="d-flex align-items-center">
                                        <select class="form-control @error('asset_symbol') is-invalid @enderror" 
                                                id="asset" 
                                                name="asset_symbol" 
                                                required 
                                                onchange="updateAssetDetails(this)"
                                                style="flex: 1;">
                                            <option value="">Choose cryptocurrency...</option>
                                            @foreach($cryptos as $crypto)
                                            <option value="{{ $crypto->coin_symbol }}" 
                                                    data-name="{{ $crypto->name }}" 
                                                    data-logo="{{ $crypto->logo }}"
                                                    data-price="{{ $crypto->price_usd }}"
                                                    {{ old('asset_symbol') == $crypto->coin_symbol ? 'selected' : '' }}>
                                                {{ $crypto->coin_symbol }} - {{ $crypto->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <img id="asset_preview" 
                                             src="" 
                                             alt="Asset Icon" 
                                             style="width: 40px; height: 40px; margin-left: 15px; border-radius: 50%; display: none; object-fit: cover; border: 2px solid #e0e0e0;">
                                    </div>
                                    @error('asset_symbol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" id="asset_name" name="asset_name" value="{{ old('asset_name') }}">
                                <input type="hidden" id="asset_logo" name="asset_logo" value="{{ old('asset_logo') }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="entry_price">Entry Price ($) *</label>
                                            <input type="number" 
                                                   class="form-control @error('entry_price') is-invalid @enderror" 
                                                   id="entry_price" 
                                                   name="entry_price" 
                                                   value="{{ old('entry_price') }}" 
                                                   step="0.00000001" 
                                                   required>
                                            @error('entry_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exit_price">Exit Price ($) *</label>
                                            <input type="number" 
                                                   class="form-control @error('exit_price') is-invalid @enderror" 
                                                   id="exit_price" 
                                                   name="exit_price" 
                                                   value="{{ old('exit_price') }}" 
                                                   step="0.00000001" 
                                                   required>
                                            @error('exit_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amount">Trade Amount (Units) *</label>
                                            <input type="number" 
                                                   class="form-control @error('amount') is-invalid @enderror" 
                                                   id="amount" 
                                                   name="amount" 
                                                   value="{{ old('amount', 1) }}" 
                                                   step="0.000001" 
                                                   required>
                                            <small class="form-text text-muted">Number of units traded</small>
                                            @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="outcome">Trade Outcome *</label>
                                            <select class="form-control @error('outcome') is-invalid @enderror" 
                                                    id="outcome" 
                                                    name="outcome" 
                                                    required>
                                                <option value="win" {{ old('outcome') == 'win' ? 'selected' : '' }}>Win</option>
                                                <option value="loss" {{ old('outcome') == 'loss' ? 'selected' : '' }}>Loss</option>
                                            </select>
                                            @error('outcome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="opened_at">Opened At (optional)</label>
                                            <input type="datetime-local" 
                                                   class="form-control @error('opened_at') is-invalid @enderror" 
                                                   id="opened_at" 
                                                   name="opened_at" 
                                                   value="{{ old('opened_at') }}">
                                            <small class="form-text text-muted">Leave empty for 1 hour ago</small>
                                            @error('opened_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="closed_at">Closed At (optional)</label>
                                            <input type="datetime-local" 
                                                   class="form-control @error('closed_at') is-invalid @enderror" 
                                                   id="closed_at" 
                                                   name="closed_at" 
                                                   value="{{ old('closed_at') }}">
                                            <small class="form-text text-muted">Leave empty for now</small>
                                            @error('closed_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Admin Notes (optional)</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" 
                                              name="notes" 
                                              rows="3" 
                                              placeholder="Internal notes about this trade...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fa fa-save"></i> Create Trade
                                    </button>
                                    <a href="{{ route('admin.bot-trading.trades', $subscription) }}" class="btn btn-secondary btn-lg ml-2">
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


<script>
    function updateAssetDetails(select) {
        var option = select.options[select.selectedIndex];
        var preview = document.getElementById('asset_preview');
        
        if (option.value) {
            document.getElementById('asset_name').value = option.dataset.name;
            document.getElementById('asset_logo').value = option.dataset.logo;
            
            // Show logo preview
            if (option.dataset.logo) {
                preview.src = option.dataset.logo;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
            
            // Auto-fill entry price with current price if available
            var price = parseFloat(option.dataset.price);
            if (price > 0) {
                document.getElementById('entry_price').value = price.toFixed(8);
                // Set exit price slightly higher for a win
                document.getElementById('exit_price').value = (price * 1.02).toFixed(8);
            }
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endsection
