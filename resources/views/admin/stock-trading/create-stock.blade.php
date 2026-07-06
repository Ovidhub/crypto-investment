@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Add New Stock</h1>
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
                            <form action="{{ route('admin.stock-trading.store-stock') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="symbol">Stock Symbol <span class="text-danger">*</span></label>
                                            <input type="text" name="symbol" id="symbol" class="form-control" 
                                                value="{{ old('symbol') }}" required 
                                                style="text-transform: uppercase;" placeholder="e.g., AAPL">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exchange">Exchange</label>
                                            <input type="text" name="exchange" id="exchange" class="form-control" 
                                                value="{{ old('exchange', 'NASDAQ') }}" placeholder="e.g., NASDAQ, NYSE">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" 
                                        value="{{ old('name') }}" required placeholder="e.g., Apple Inc.">
                                </div>

                                <div class="form-group">
                                    <label for="logo_url">Logo URL</label>
                                    <input type="url" name="logo_url" id="logo_url" class="form-control" 
                                        value="{{ old('logo_url') }}" placeholder="https://example.com/logo.png">
                                    <small class="text-muted">Full URL to company logo image</small>
                                </div>

                                <hr>
                                <h5>Price Information</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">Current Price <span class="text-danger">*</span></label>
                                            <input type="number" name="price" id="price" class="form-control" 
                                                value="{{ old('price') }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="previous_close">Previous Close</label>
                                            <input type="number" name="previous_close" id="previous_close" class="form-control" 
                                                value="{{ old('previous_close') }}" step="0.01" min="0">
                                            <small class="text-muted">Leave empty to use current price</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="open">Open</label>
                                            <input type="number" name="open" id="open" class="form-control" 
                                                value="{{ old('open') }}" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="high">High</label>
                                            <input type="number" name="high" id="high" class="form-control" 
                                                value="{{ old('high') }}" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="low">Low</label>
                                            <input type="number" name="low" id="low" class="form-control" 
                                                value="{{ old('low') }}" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5>Additional Data (Optional)</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="volume">Volume</label>
                                            <input type="number" name="volume" id="volume" class="form-control" 
                                                value="{{ old('volume', 0) }}" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="market_cap">Market Cap</label>
                                            <input type="number" name="market_cap" id="market_cap" class="form-control" 
                                                value="{{ old('market_cap') }}" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pe_ratio">P/E Ratio</label>
                                            <input type="number" name="pe_ratio" id="pe_ratio" class="form-control" 
                                                value="{{ old('pe_ratio') }}" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="dividend_yield">Dividend Yield (%)</label>
                                            <input type="number" name="dividend_yield" id="dividend_yield" class="form-control" 
                                                value="{{ old('dividend_yield') }}" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-plus-circle"></i> Add Stock
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
                    <!-- Help Card -->
                    <div class="card bg-info">
                        <div class="card-body">
                            <h5 class="text-white"><i class="fa fa-info-circle"></i> Adding Stocks</h5>
                            <ul class="text-white mb-0">
                                <li>Symbol must be unique</li>
                                <li>Only symbol, name, and price are required</li>
                                <li>Other fields will use defaults if empty</li>
                                <li>Change/percent will be auto-calculated</li>
                                <li>You can update values later</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Example Card -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Example</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Symbol:</strong> AAPL</p>
                            <p><strong>Name:</strong> Apple Inc.</p>
                            <p><strong>Exchange:</strong> NASDAQ</p>
                            <p><strong>Price:</strong> 175.50</p>
                            <p><strong>Logo URL:</strong><br>
                            <small>https://logo.clearbit.com/apple.com</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
