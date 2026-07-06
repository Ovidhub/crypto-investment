@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Edit Trade</h1>
                <p class="text-muted">Trade #{{ $trade->id }} - {{ $trade->asset_name }}</p>
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
                            <form action="{{ route('admin.bot-trading.update-trade', $trade) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="asset">Select Asset *</label>
                                    <div class="d-flex align-items-center">
                                        <select class="form-control @error('asset_symbol') is-invalid @enderror" 
                                                id="asset" 
                                                name="asset_symbol" 
                                                required 
                                                onchange="updateAssetDetails(this)"
                                                style="flex: 1;">
                                            @foreach($cryptos as $crypto)
                                            <option value="{{ $crypto->coin_symbol }}" 
                                                    data-name="{{ $crypto->name }}" 
                                                    data-logo="{{ $crypto->logo }}"
                                                    {{ old('asset_symbol', $trade->asset_symbol) == $crypto->coin_symbol ? 'selected' : '' }}>
                                                {{ $crypto->coin_symbol }} - {{ $crypto->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <img id="asset_preview" 
                                             src="{{ $trade->asset_logo }}" 
                                             alt="Asset Icon" 
                                             style="width: 40px; height: 40px; margin-left: 15px; border-radius: 50%; {{ $trade->asset_logo ? '' : 'display: none;' }} object-fit: cover; border: 2px solid #e0e0e0;">
                                    </div>
                                    @error('asset_symbol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" id="asset_name" name="asset_name" value="{{ old('asset_name', $trade->asset_name) }}">
                                <input type="hidden" id="asset_logo" name="asset_logo" value="{{ old('asset_logo', $trade->asset_logo) }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="entry_price">Entry Price ($) *</label>
                                            <input type="number" 
                                                   class="form-control @error('entry_price') is-invalid @enderror" 
                                                   id="entry_price" 
                                                   name="entry_price" 
                                                   value="{{ old('entry_price', $trade->entry_price) }}" 
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
                                                   value="{{ old('exit_price', $trade->exit_price) }}" 
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
                                                   value="{{ old('amount', $trade->amount) }}" 
                                                   step="0.000001" 
                                                   required>
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
                                                <option value="win" {{ old('outcome', $trade->outcome) == 'win' ? 'selected' : '' }}>Win</option>
                                                <option value="loss" {{ old('outcome', $trade->outcome) == 'loss' ? 'selected' : '' }}>Loss</option>
                                            </select>
                                            @error('outcome')
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
                                              rows="3">{{ old('notes', $trade->notes) }}</textarea>
                                    @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-info">
                                    <strong>Note:</strong> Changing trade values will automatically update the user's profit and balance.
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-save"></i> Update Trade
                                    </button>
                                    <a href="{{ route('admin.bot-trading.trades', $trade->subscription) }}" class="btn btn-secondary btn-lg ml-2">
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
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endsection
