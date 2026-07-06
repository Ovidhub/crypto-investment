@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Manage Stock Prices</h1>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Stock Prices Database</h4>
                                <a href="{{ route('admin.stock-trading.create-stock') }}" class="btn btn-primary btn-round ml-auto">
                                    <i class="fa fa-plus"></i>
                                    Add New Stock
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="stocksTable" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Symbol</th>
                                            <th>Name</th>
                                            <th>Exchange</th>
                                            <th>Logo</th>
                                            <th class="text-right">Price</th>
                                            <th class="text-right">Change</th>
                                            <th class="text-right">Volume</th>
                                            <th>Last Updated</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stocks as $stock)
                                        <tr>
                                            <td>{{ $stock->id }}</td>
                                            <td><strong>{{ $stock->symbol }}</strong></td>
                                            <td>{{ $stock->name }}</td>
                                            <td>{{ $stock->exchange }}</td>
                                            <td>
                                                @if($stock->logo_url)
                                                <img src="{{ $stock->logo_url }}" alt="{{ $stock->symbol }}" 
                                                     class="rounded" style="width: 32px; height: 32px;" 
                                                     onerror="this.style.display='none'">
                                                @else
                                                <span class="text-muted">No logo</span>
                                                @endif
                                            </td>
                                            <td class="text-right">${{ number_format($stock->price, 2) }}</td>
                                            <td class="text-right">
                                                <span class="text-{{ $stock->percent_change >= 0 ? 'success' : 'danger' }}">
                                                    {{ $stock->percent_change >= 0 ? '+' : '' }}{{ number_format($stock->percent_change, 2) }}%
                                                </span>
                                            </td>
                                            <td class="text-right">{{ number_format($stock->volume) }}</td>
                                            <td>
                                                @if($stock->last_updated)
                                                    {{ $stock->last_updated->diffForHumans() }}
                                                @else
                                                    Never
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="form-button-action">
                                                    <a href="{{ route('admin.stock-trading.edit-stock', $stock->id) }}" 
                                                       class="btn btn-link btn-primary btn-lg" 
                                                       data-toggle="tooltip" title="Edit Stock">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('admin.stock-trading.delete-stock', $stock->id) }}" 
                                                          method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger btn-lg" 
                                                            data-toggle="tooltip" title="Delete Stock"
                                                            onclick="return confirm('Delete {{ $stock->symbol }}? This will fail if stock has existing trades.')">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                {{ $stocks->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/af-2.3.5/b-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/r-2.2.5/datatables.min.js"></script>
<script>
$(document).ready(function() {
    $('#stocksTable').DataTable({
        "pageLength": 50,
        "order": [[1, "asc"]],
        "responsive": true,
        "dom": 'Bfrtip',
        "buttons": [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
            'print'
        ]
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
