@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel bg-light">
    <div class="content">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Manage Cryptocurrencies</h1>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">All Cryptocurrencies</h4>
                                <div class="ml-auto">
                                    <a href="{{ route('admin.crypto-trading.index') }}" class="btn btn-secondary btn-round">
                                        <i class="fa fa-arrow-left"></i>
                                        Back to Trades
                                    </a>
                                    <a href="{{ route('admin.crypto-trading.create-crypto') }}" class="btn btn-primary btn-round">
                                        <i class="fa fa-plus"></i>
                                        Add Cryptocurrency
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="cryptoTable" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Logo</th>
                                            <th>Name</th>
                                            <th>Symbol</th>
                                            <th>Price (USD)</th>
                                            <th>24h Change</th>
                                            <th>Market Cap</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cryptos as $crypto)
                                        <tr>
                                            <td>{{ $crypto->id }}</td>
                                            <td>
                                                @if($crypto->logo)
                                                    <img src="{{ $crypto->logo }}" alt="{{ $crypto->name }}" style="width: 32px; height: 32px; border-radius: 50%;">
                                                @else
                                                    <i class="fa fa-bitcoin fa-2x text-muted"></i>
                                                @endif
                                            </td>
                                            <td><strong>{{ $crypto->name }}</strong></td>
                                            <td><span class="badge badge-primary">{{ $crypto->coin_symbol }}</span></td>
                                            <td><strong>${{ number_format($crypto->price_usd, 2) }}</strong></td>
                                            <td>
                                                @if($crypto->percent_change_24h >= 0)
                                                    <span class="text-success">
                                                        <i class="fa fa-arrow-up"></i> +{{ number_format($crypto->percent_change_24h, 2) }}%
                                                    </span>
                                                @else
                                                    <span class="text-danger">
                                                        <i class="fa fa-arrow-down"></i> {{ number_format($crypto->percent_change_24h, 2) }}%
                                                    </span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($crypto->market_cap, 0) }}</td>
                                            <!--<td>-->
                                            <!--    <div class="form-button-action">-->
                                            <!--        <a href="{{ route('admin.crypto-trading.edit-crypto', $crypto->id) }}" class="btn btn-link btn-primary btn-lg" data-toggle="tooltip" title="Edit Crypto">-->
                                            <!--            <i class="fa fa-edit"></i>-->
                                            <!--        </a>-->
                                            <!--        <form action="{{ route('admin.crypto-trading.delete-crypto', $crypto->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this cryptocurrency? This action cannot be undone if there are existing trades.')">-->
                                            <!--            @csrf-->
                                            <!--            @method('DELETE')-->
                                            <!--            <button type="submit" class="btn btn-link btn-danger" data-toggle="tooltip" title="Delete">-->
                                            <!--                <i class="fa fa-times"></i>-->
                                            <!--            </button>-->
                                            <!--        </form>-->
                                            <!--    </div>-->
                                            <!--</td>-->
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Edit Modal -->
            <div class="modal fade" id="editCryptoModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Quick Edit Cryptocurrency</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <form id="quickEditForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Symbol</label>
                                    <input type="text" name="coin_symbol" id="edit_symbol" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" id="edit_name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Logo URL</label>
                                    <input type="url" name="logo" id="edit_logo" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Price (USD)</label>
                                    <input type="number" name="price_usd" id="edit_price" class="form-control" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>24h Change (%)</label>
                                    <input type="number" name="percent_change_24h" id="edit_change" class="form-control" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label>Market Cap</label>
                                    <input type="number" name="market_cap" id="edit_market_cap" class="form-control" step="1">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#cryptoTable').DataTable({
        "pageLength": 25,
        "order": [[0, 'desc']]
    });

    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
