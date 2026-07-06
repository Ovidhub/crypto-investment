@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content bg-light">
            <div class="page-inner">
                <div class="mt-2 mb-4 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="text-dark font-weight-bold">Wallet Approvals</h3>
                        <p class="text-muted mb-0">Review public wallet addresses submitted by users and set their daily reward</p>
                    </div>
                    <span class="badge badge-warning p-2" style="font-size:0.9rem;">{{ $pending }} pending</span>
                </div>

                <x-danger-alert />
                <x-success-alert />

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Provider / Network</th>
                                        <th>Public Address</th>
                                        <th>Status</th>
                                        <th>Daily Reward ($)</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($wallets as $wallet)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark">
                                                {{ optional($wallet->user)->username ?? optional($wallet->user)->name ?? 'Unknown' }}
                                            </div>
                                            <small class="text-muted">{{ optional($wallet->user)->email }}</small>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $wallet->provider }}</div>
                                            <small class="text-muted">{{ $wallet->network ?: 'Not specified' }}</small>
                                            @if($wallet->label)<br><small class="text-muted">“{{ $wallet->label }}”</small>@endif
                                        </td>
                                        <td style="max-width:220px;">
                                            <code class="text-break">{{ $wallet->address }}</code>
                                        </td>
                                        <td>
                                            @if($wallet->status === 'approved')
                                                <span class="badge badge-success p-2">Connected</span>
                                            @elseif($wallet->status === 'rejected')
                                                <span class="badge badge-danger p-2">Rejected</span>
                                            @else
                                                <span class="badge badge-warning p-2">Waiting for approval</span>
                                            @endif
                                        </td>
                                        <td style="min-width:170px;">
                                            @if($wallet->user)
                                            <form method="POST" action="{{ route('wallet.reward', $wallet->user->id) }}" class="form-inline">
                                                @csrf
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" min="0" name="wallet_daily_reward"
                                                           value="{{ number_format((float) $wallet->user->wallet_daily_reward, 2, '.', '') }}"
                                                           class="form-control form-control-sm" style="max-width:90px;">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-sm btn-outline-primary" type="submit">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                            @endif
                                        </td>
                                        <td class="text-right" style="min-width:190px;">
                                            @if($wallet->status !== 'approved')
                                            <form method="POST" action="{{ route('wallet.approve', $wallet->id) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success" type="submit">Approve</button>
                                            </form>
                                            @endif
                                            @if($wallet->status !== 'rejected')
                                            <form method="POST" action="{{ route('wallet.reject', $wallet->id) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Reject</button>
                                            </form>
                                            @endif
                                            <form method="POST" action="{{ route('wallet.approval.destroy', $wallet->id) }}" class="d-inline"
                                                  onsubmit="return confirm('Delete this wallet record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-light text-danger" type="submit"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted p-4">No wallets have been submitted yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
