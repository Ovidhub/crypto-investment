@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content bg-light">
            <div class="page-inner">
                <div class="mt-2 mb-4">
                    <h3 class="text-dark font-weight-bold mb-0">Contact Messages</h3>
                    <p class="text-muted mb-0">Enquiries submitted through the website contact form</p>
                </div>

                <x-danger-alert />
                <x-success-alert />

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th>From</th>
                                        <th>Message</th>
                                        <th>IP</th>
                                        <th>Received</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $m)
                                    <tr>
                                        <td style="min-width:160px;">
                                            <div class="font-weight-bold text-dark">{{ $m->name }}</div>
                                            <a href="mailto:{{ $m->email }}" class="small text-primary">{{ $m->email }}</a>
                                        </td>
                                        <td style="max-width:520px; white-space:pre-wrap;">{{ $m->message }}</td>
                                        <td><small class="text-muted">{{ $m->ip_address }}</small></td>
                                        <td><small class="text-muted">{{ \Carbon\Carbon::parse($m->created_at)->format('M d, Y H:i') }}</small></td>
                                        <td class="text-right" style="min-width:140px;">
                                            <a href="mailto:{{ $m->email }}?subject=Re: your enquiry" class="btn btn-sm btn-outline-primary">Reply</a>
                                            <form method="POST" action="{{ route('contact.messages.destroy', $m->id) }}" class="d-inline"
                                                  onsubmit="return confirm('Delete this message?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-light text-danger"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center text-muted p-4">No messages yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">{{ $messages->links() }}</div>
            </div>
        </div>
    </div>
@endsection
