@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Expert Traders</h1>
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

            <div class="mb-5 row">
                <div class="col-12">
                    <a href="{{ route('admin.copytrading.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create New Trader
                    </a>
                </div>
            </div>

            <div class="mb-5 row">
                <div class="col-12 card p-4 shadow">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tradersTable">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Followers</th>
                                    <th>ROI</th>
                                    <th>Win Rate</th>
                                    <th>Active Trades</th>
                                    <th>Risk Level</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expertTraders as $trader)
                                <tr>
                                    <td>
                                        @if($trader->photo)
                                        <img src="{{ asset('storage/app/public/expert_traders/' . $trader->photo) }}" 
                                             alt="{{ $trader->name }}" 
                                             class="rounded-circle"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fa fa-user text-white"></i>
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{ $trader->name }}</td>
                                    <td>
                                        {{ $trader->total_followers }} / {{ $trader->max_followers }}
                                        @if($trader->isAtCapacity())
                                        <span class="badge badge-warning">Full</span>
                                        @endif
                                    </td>
                                    <td>{{ $trader->roi_percentage }}%</td>
                                    <td>{{ $trader->win_rate }}%</td>
                                    <td>{{ $trader->active_trades }}</td>
                                    <td>
                                        @if($trader->risk_level == 'Low')
                                        <span class="badge badge-success">{{ $trader->risk_level }}</span>
                                        @elseif($trader->risk_level == 'Medium')
                                        <span class="badge badge-warning">{{ $trader->risk_level }}</span>
                                        @else
                                        <span class="badge badge-danger">{{ $trader->risk_level }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($trader->status == 'active')
                                        <span class="badge badge-primary">Active</span>
                                        @else
                                        <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.copytrading.subscribers', $trader->id) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="View Subscribers">
                                                <i class="fa fa-users"></i>
                                            </a>
                                            <a href="{{ route('admin.copytrading.distribute', $trader->id) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Distribute Profit">
                                                <i class="fa fa-dollar-sign"></i>
                                            </a>
                                            <a href="{{ route('admin.copytrading.edit', $trader->id) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.copytrading.toggle', $trader->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $trader->status == 'active' ? 'btn-secondary' : 'btn-primary' }}" 
                                                        title="{{ $trader->status == 'active' ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.copytrading.destroy', $trader->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this trader? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tradersTable').DataTable({
            "order": [[ 1, "asc" ]]
        });
    });
</script>
@endpush
@endsection
