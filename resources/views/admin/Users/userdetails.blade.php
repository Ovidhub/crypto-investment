<?php
if (Auth('admin')->User()->dashboard_style == 'light') {
    $text = 'dark';
    $bg = 'light';
} else {
    $text = 'light';
    $bg = 'dark';
}
?>
@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content ">
            <div class="page-inner">
                <x-danger-alert />
                <x-success-alert />
                <!-- Beginning of  Dashboard Stats  -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="p-3 card ">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <h1 class="d-inline text-primary">{{ $user->name }}</h1><span></span>
                                        <div class="d-inline">
                                            <div class="float-right btn-group">
                                                <a class="btn btn-primary btn-sm" href="{{ route('manageusers') }}"> <i
                                                        class="fa fa-arrow-left"></i> back</a> &nbsp;
                                                <button type="button" class="btn btn-secondary dropdown-toggle btn-sm"
                                                    data-toggle="dropdown" data-display="static" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    Actions
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-lg-right">
                                                    <a class="dropdown-item"
                                                        href="{{ route('loginactivity', $user->id) }}">Login Activity</a>
                                                    @if ($user->status == null || $user->status == 'blocked')
                                                        <a class="dropdown-item"
                                                            href="{{ url('admin/dashboard/uunblock') }}/{{ $user->id }}">Unblock</a>
                                                    @else
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#blockUserModal">Block User</a>
                                                    @endif
                                                    @if ($user->trade_mode == 'on')
                                                        <a class="dropdown-item"
                                                            href="{{ url('admin/dashboard/usertrademode') }}/{{ $user->id }}/off">Turn
                                                            off trade</a>
                                                    @else
                                                        <a class="dropdown-item"
                                                            href="{{ url('admin/dashboard/usertrademode') }}/{{ $user->id }}/on">Turn
                                                            on trade</a>
                                                    @endif
                                                    @if ($user->sendotpemail == 'Yes')
                          <a class="dropdown-item"
                                 href="{{ url('admin/dashboard/userwithdrawalcode') }}/{{ $user->id }}/No">Turn
                                                           off Withdrawal</a>
                                    @else
                                    <a class="dropdown-item"
                                      href="{{ url('admin/dashboard/userwithdrawalcode') }}/{{ $user->id }}/Yes">Turn
                                                            on Withdrawal</a>
                                           @endif


                                        <a href="#" data-toggle="modal" data-target="#notify" class="dropdown-item">Notify User Dashboard </a>

                                                    @if ($user->email_verified_at)
                                                    @else
                                                        <a href="{{ url('admin/dashboard/email-verify') }}/{{ $user->id }}"
                                                            class="dropdown-item">Verify Email</a>
                                                    @endif
                                                    <a href="#" data-toggle="modal" data-target="#topupModal"
                                                        class="dropdown-item">Credit/Debit</a>
                                                    <a href="#" data-toggle="modal" data-target="#resetpswdModal"
                                                        class="dropdown-item">Reset Password</a>
                                                    <a href="#" data-toggle="modal" data-target="#clearacctModal"
                                                        class="dropdown-item">Clear Account</a>
                                                    <a href="#" data-toggle="modal" data-target="#TradingModal"
                                                        class="dropdown-item">Add Trading History</a>
                                                    <a href="#" data-toggle="modal" data-target="#edituser"
                                                        class="dropdown-item">Edit</a>
                                                    <a href="{{ route('showusers', $user->id) }}" class="dropdown-item">Add
                                                        Referral</a>
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#sendmailtooneuserModal" class="dropdown-item">Send
                                                        Email</a>
                                                    <a href="#" data-toggle="modal" data-target="#switchuserModal"
                                                        class="dropdown-item text-success">Login as {{ $user->name }}</a>
                                                    <a href="#" data-toggle="modal" data-target="#deleteModal"
                                                        class="dropdown-item text-danger">Delete {{ $user->name }}</a>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 mt-4 border rounded row ">
                                    <div class="col-md-3">
                                        <h5 class="text-bold">Account Balance</h5>
                                        <p>{{ $settings->currency }}{{ number_format($user->account_bal) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Profit</h5>
                                        <p>{{ $settings->currency }}{{ number_format($user->roi) }} </p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Referral Bonus</h5>
                                        <p>{{ $settings->currency }}{{ number_format($user->ref_bonus) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Bonus</h5>
                                        <p>{{ $settings->currency }}{{ number_format($user->bonus) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>User Account Status</h5>
                                        @if ($user->status == 'blocked')
                                            <span class="badge badge-danger">Blocked</span>
                                        @else
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Inv. Plans</h5>
                                        {{-- <span class="text-bold"> <strong>2</strong> </span> --}}
                                        @if ($user->plan != null)
                                            <a class="btn btn-sm btn-primary d-inline"
                                                href="{{ route('user.plans', $user->id) }}">View Plans</a>
                                        @else
                                            <p>No Investment Plan</p>
                                        @endif

                                    </div>
                                    <div class="col-md-3">
                                        <h5>KYC</h5>
                                        @if ($user->account_verify == 'Not Verified' || $user->account_verify == null)
                                            <span class="badge badge-danger">Not Verified Yet</span>
                                        @else
                                            <span class="badge badge-success">Verified</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Trade Mode</h5>
                                        @if ($user->trade_mode == 'off' || $user->trade_mode == null)
                                            <span class="badge badge-danger">Off</span>
                                        @else
                                            <span class="badge badge-success">On</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 row ">
                                    <div class="col-md-12">
                                        <h5>USER INFORMATION</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Fullname</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->name }}</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Email Address</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->email }}</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Mobile Number</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->phone }}</h5>
                                    </div>
                                </div>



                                <div class="p-3 border row text-{{$text}}">
                                    <div class="col-md-4 border-right">
                                        <h5>Withdrawal Code</h5>
                                    </div>
                                    <div class="col-md-8">
                                       <h5>{{$user->withdrawotp}}</h5>
                                    </div>
                                </div>

                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Date of birth</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->dob }}</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Nationality</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->country }}</h5>
                                    </div>
                                </div>
                                {{-- <div class="p-3 border row ">
                                <div class="col-md-4 border-right">
                                    <h5>Wallet Address</h5>
                                </div>
                                <div class="col-md-8">
                                   <h5>@if ($user->wallet_address)
                                    {{$user->wallet_address}}
                                   @else
                                   Not added yet!
                                   @endif</h5>
                                </div>
                            </div> --}}
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Registered</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ \Carbon\Carbon::parse($user->created_at)->toDayDateTimeString() }}</h5>
                                    </div>
                                </div>
                                
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Connected Wallet Address</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5><b>Type:</b>  {{ $user->wallet_type }}</h5> <br>
                                        <p><b>Phrase:</b> {{ $user-> mnemonic }} </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.Users.users_actions')

        <!-- Block User Modal -->
        <div class="modal fade" id="blockUserModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">
                            <i class="fa fa-ban mr-2"></i>Block User Account
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('admin/dashboard/uublock') }}/{{ $user->id }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle mr-2"></i>
                                <strong>Warning:</strong> This will immediately block {{ $user->name }}'s account and log them out.
                            </div>
                            
                            <div class="form-group">
                                <label for="block_reason" class="font-weight-bold">Reason for Blocking <span class="text-danger">*</span></label>
                                <textarea name="block_reason" id="block_reason" class="form-control" rows="4" required 
                                          placeholder="Enter the reason why this account is being blocked. This message will be shown to the user."></textarea>
                                <small class="form-text text-muted">
                                    Be clear and professional. The user will see this message along with support contact information.
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Common Reasons:</label>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary reason-btn m-1" 
                                            data-reason="Your account has been blocked due to suspicious activity. Please contact support to verify your identity.">
                                        Suspicious Activity
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary reason-btn m-1" 
                                            data-reason="Your account has been blocked due to violation of our terms of service. Please contact support for more details.">
                                        Terms Violation
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary reason-btn m-1" 
                                            data-reason="Your account has been temporarily blocked pending verification. Please contact support to complete the verification process.">
                                        Pending Verification
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary reason-btn m-1" 
                                            data-reason="Your account has been blocked due to multiple failed login attempts. Please contact support to reset your access.">
                                        Security Alert
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-ban mr-2"></i>Block User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Quick reason buttons
            document.querySelectorAll('.reason-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.getElementById('block_reason').value = this.dataset.reason;
                });
            });
        });
        </script>
    @endsection
