@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Edit Stock - {{ $stock->symbol }}</h1>
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
                            <h4 class="card-title">Stock Details</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.stock-trading.update-stock', $stock->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="symbol">Stock Symbol <span class="text-danger">*</span></label>
                                            <input type="text" name="symbol" id="symbol" class="form-control" 
                                                value="{{ old('symbol', $stock->symbol) }}" required 
                                                style="text-transform: uppercase;">
                                            <small class="text-muted">e.g., AAPL, MSFT, TSLA</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exchange">Exchange</label>
                                            <input type="text" name="exchange" id="exchange" class="form-control" 
                                                value="{{ old('exchange', $stock->exchange) }}" 
                                                placeholder="e.g., NASDAQ, NYSE">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" 
                                        value="{{ old('name', $stock->name) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="logo_url">Logo URL</label>
                                    <input type="url" name="logo_url" id="logo_url" class="form-control" 
                                        value="{{ old('logo_url', $stock->logo_url) }}" 
                                        placeholder="https://example.com/logo.png">
                                    <small class="text-muted">Full URL to company logo image</small>
                                </div>

                                <hr>
                                <h5>Price Information</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">Current Price <span class="text-danger">*</span></label>
                                            <input type="number" name="price" id="price" class="form-control" 
                                                value="{{ old('price', $stock->price) }}" 
                                                step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="previous_close">Previous Close</label>
                                            <input type="number" name="previous_close" id="previous_close" class="form-control" 
                                                value="{{ old('previous_close', $stock->previous_close) }}" 
                                                step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="open">Open</label>
                                            <input type="number" name="open" id="open" class="form-control" 
                                                value="{{ old('open', $stock->open) }}" 
                                                step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="high">High</label>
                                            <input type="number" name="high" id="high" class="form-control" 
                                                value="{{ old('high', $stock->high) }}" 
                                                step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="low">Low</label>
                                            <input type="number" name="low" id="low" class="form-control" 
                                                value="{{ old('low', $stock->low) }}" 
                                                step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5>Additional Data</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="volume">Volume</label>
                                            <input type="number" name="volume" id="volume" class="form-control" 
                                                value="{{ old('volume', $stock->volume) }}" 
                                                min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="market_cap">Market Cap</label>
                                            <input type="number" name="market_cap" id="market_cap" class="form-control" 
                                                value="{{ old('market_cap', $stock->market_cap) }}" 
                                                step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pe_ratio">P/E Ratio</label>
                                            <input type="number" name="pe_ratio" id="pe_ratio" class="form-control" 
                                                value="{{ old('pe_ratio', $stock->pe_ratio) }}" 
                                                step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="dividend_yield">Dividend Yield (%)</label>
                                            <input type="number" name="dividend_yield" id="dividend_yield" class="form-control" 
                                                value="{{ old('dividend_yield', $stock->dividend_yield) }}" 
                                                step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-save"></i> Update Stock
                                    </button>
                                    <a href="{{ route('admin.stock-trading.manage-stocks') }}" class="btn btn-secondary btn-lg">
                                        <i class="fa fa-arrow-left"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Preview Card -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Preview</h4>
                        </div>
                        <div class="card-body text-center">
                            @if($stock->logo_url)
                            <img src="{{ $stock->logo_url }}" alt="{{ $stock->symbol }}" 
                                 class="img-fluid rounded mb-3" style="max-width: 100px;" 
                                 onerror="this.style.display='none'">
                            @else
                            <div class="mb-3 text-muted">No logo</div>
                            @endif
                            <h3>{{ $stock->symbol }}</h3>
                            <p class="mb-2">{{ $stock->name }}</p>
                            <h2 class="text-primary">${{ number_format($stock->price, 2) }}</h2>
                            <p class="text-{{ $stock->percent_change >= 0 ? 'success' : 'danger' }}">
                                {{ $stock->percent_change >= 0 ? '+' : '' }}${{ number_format($stock->change, 2) }} 
                                ({{ $stock->percent_change >= 0 ? '+' : '' }}{{ number_format($stock->percent_change, 2) }}%)
                            </p>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="card bg-info">
                        <div class="card-body">
                            <h5 class="text-white"><i class="fa fa-info-circle"></i> Last Updated</h5>
                            <p class="text-white mb-0">
                                {{ $stock->last_updated ? $stock->last_updated->format('M d, Y H:i') : 'Never' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
