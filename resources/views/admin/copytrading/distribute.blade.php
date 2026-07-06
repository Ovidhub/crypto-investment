@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Distribute Profit - {{ $trader->name }}</h1>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

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

            <div class="mb-3">
                <a href="{{ route('admin.copytrading.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Traders
                </a>
                <a href="{{ route('admin.copytrading.subscribers', $trader->id) }}" class="btn btn-info">
                    <i class="fa fa-users"></i> View Subscribers
                </a>
            </div>

            <div class="mb-4 row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Profit Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> 
                                <strong>Active Subscribers:</strong> {{ $activeSubscribers->count() }}
                                <br>
                                <strong>Total Active Investment:</strong> {{ $settings->currency }}{{ number_format($activeSubscribers->sum('amount_invested'), 2) }}
                            </div>

                            @if($activeSubscribers->count() == 0)
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> 
                                No active subscribers to distribute profit to.
                            </div>
                            @else
                            <form action="{{ route('admin.copytrading.distribute.profit', $trader->id) }}" method="POST" id="profitForm">
                                @csrf

                                <div class="form-group">
                                    <label for="profit_percentage">Profit Percentage (%) *</label>
                                    <input type="number" 
                                           class="form-control @error('profit_percentage') is-invalid @enderror" 
                                           id="profit_percentage" 
                                           name="profit_percentage" 
                                           value="{{ old('profit_percentage') }}" 
                                           step="0.01" 
                                           min="0" 
                                           required>
                                    <small class="form-text text-muted">
                                        Enter the profit percentage to distribute to all active subscribers. 
                                        This percentage will be calculated on each subscriber's invested amount.
                                    </small>
                                    @error('profit_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="preview" class="card bg-light" style="display: none;">
                                    <div class="card-body">
                                        <h6 class="card-title">Distribution Preview</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Subscriber</th>
                                                        <th>Invested</th>
                                                        <th>Profit Amount</th>
                                                        <th>New Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="previewBody">
                                                </tbody>
                                                <tfoot>
                                                    <tr class="font-weight-bold">
                                                        <td>Total</td>
                                                        <td id="totalInvested">{{ $settings->currency }}{{ number_format($activeSubscribers->sum('amount_invested'), 2) }}</td>
                                                        <td id="totalProfit">-</td>
                                                        <td>-</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success btn-lg btn-block" onclick="return confirm('Are you sure you want to distribute this profit? This action cannot be undone.');">
                                        <i class="fa fa-dollar-sign"></i> Distribute Profit
                                    </button>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>

@push('scripts')
<script>
    const subscribers = @json($activeSubscribers);
    const currencySymbol = "{{ $settings->currency }}";

    document.getElementById('profit_percentage').addEventListener('input', function() {
        const percentage = parseFloat(this.value);
        
        if (isNaN(percentage) || percentage <= 0) {
            document.getElementById('preview').style.display = 'none';
            return;
        }

        let totalProfit = 0;
        let previewHTML = '';

        subscribers.forEach(sub => {
            const invested = parseFloat(sub.amount_invested);
            const profitAmount = (invested * percentage) / 100;
            const newBalance = invested + parseFloat(sub.current_profit) + profitAmount;
            totalProfit += profitAmount;

            previewHTML += `
                <tr>
                    <td>${sub.user.name}</td>
                    <td>${currencySymbol}${invested.toFixed(2)}</td>
                    <td class="text-success">${currencySymbol}${profitAmount.toFixed(2)}</td>
                    <td>${currencySymbol}${newBalance.toFixed(2)}</td>
                </tr>
            `;
        });

        document.getElementById('previewBody').innerHTML = previewHTML;
        document.getElementById('totalProfit').innerHTML = `<span class="text-success">${currencySymbol}${totalProfit.toFixed(2)}</span>`;
        document.getElementById('preview').style.display = 'block';
    });
</script>
@endpush
@endsection
