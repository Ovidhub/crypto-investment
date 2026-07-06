@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Edit Expert Trader</h1>
            </div>

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

            <div class="mb-5 row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card shadow">
                        <div class="card-body">
                            <form action="{{ route('admin.copytrading.update', $trader->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="name">Trader Name *</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $trader->name) }}" 
                                           required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="photo">Profile Photo</label>
                                    @if($trader->photo)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/app/public/expert_traders/' . $trader->photo) }}" 
                                             alt="{{ $trader->name }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px;">
                                    </div>
                                    @endif
                                    <input type="file" 
                                           class="form-control-file @error('photo') is-invalid @enderror" 
                                           id="photo" 
                                           name="photo" 
                                           accept="image/*">
                                    <small class="form-text text-muted">Leave empty to keep current photo. Recommended size: 400x400px (JPG, PNG)</small>
                                    @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="bio">Biography</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" 
                                              id="bio" 
                                              name="bio" 
                                              rows="4">{{ old('bio', $trader->bio) }}</textarea>
                                    <small class="form-text text-muted">Brief description of trader's experience and strategy</small>
                                    @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="min_capital">Minimum Capital *</label>
                                            <input type="number" 
                                                   class="form-control @error('min_capital') is-invalid @enderror" 
                                                   id="min_capital" 
                                                   name="min_capital" 
                                                   value="{{ old('min_capital', $trader->min_capital) }}" 
                                                   step="0.01" 
                                                   required>
                                            @error('min_capital')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_capital">Maximum Capital *</label>
                                            <input type="number" 
                                                   class="form-control @error('max_capital') is-invalid @enderror" 
                                                   id="max_capital" 
                                                   name="max_capital" 
                                                   value="{{ old('max_capital', $trader->max_capital) }}" 
                                                   step="0.01" 
                                                   required>
                                            @error('max_capital')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_followers">Maximum Followers *</label>
                                            <input type="number" 
                                                   class="form-control @error('max_followers') is-invalid @enderror" 
                                                   id="max_followers" 
                                                   name="max_followers" 
                                                   value="{{ old('max_followers', $trader->max_followers) }}" 
                                                   required>
                                            <small class="form-text text-muted">Real subscriber limit (Current: {{ $trader->total_followers }})</small>
                                            @error('max_followers')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="preset_followers">Preset Followers</label>
                                            <input type="number" 
                                                   class="form-control @error('preset_followers') is-invalid @enderror" 
                                                   id="preset_followers" 
                                                   name="preset_followers" 
                                                   value="{{ old('preset_followers', $trader->preset_followers) }}" 
                                                   min="0">
                                            <small class="form-text text-muted">Displayed count will be: {{ $trader->preset_followers + $trader->total_followers }}</small>
                                            @error('preset_followers')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="duration">Copy Duration *</label>
                                    <select class="form-control @error('duration') is-invalid @enderror" 
                                            id="duration" 
                                            name="duration" 
                                            required>
                                        <option value="">Select Duration</option>
                                        <option value="1 Day" {{ old('duration', $trader->duration) == '1 Day' ? 'selected' : '' }}>1 Day</option>
                                        <option value="2 Days" {{ old('duration', $trader->duration) == '2 Days' ? 'selected' : '' }}>2 Days</option>
                                        <option value="3 Days" {{ old('duration', $trader->duration) == '3 Days' ? 'selected' : '' }}>3 Days</option>
                                        <option value="4 Days" {{ old('duration', $trader->duration) == '4 Days' ? 'selected' : '' }}>4 Days</option>
                                        <option value="5 Days" {{ old('duration', $trader->duration) == '5 Days' ? 'selected' : '' }}>5 Days</option>
                                        <option value="6 Days" {{ old('duration', $trader->duration) == '6 Days' ? 'selected' : '' }}>6 Days</option>
                                        <option value="7 Days" {{ old('duration', $trader->duration) == '7 Days' ? 'selected' : '' }}>7 Days</option>
                                        <option value="14 Days" {{ old('duration', $trader->duration) == '14 Days' ? 'selected' : '' }}>14 Days</option>
                                        <option value="30 Days" {{ old('duration', $trader->duration) == '30 Days' ? 'selected' : '' }}>30 Days</option>
                                        <option value="60 Days" {{ old('duration', $trader->duration) == '60 Days' ? 'selected' : '' }}>60 Days</option>
                                        <option value="90 Days" {{ old('duration', $trader->duration) == '90 Days' ? 'selected' : '' }}>90 Days</option>
                                        <option value="1 Month" {{ old('duration', $trader->duration) == '1 Month' ? 'selected' : '' }}>1 Month</option>
                                        <option value="2 Months" {{ old('duration', $trader->duration) == '2 Months' ? 'selected' : '' }}>2 Months</option>
                                        <option value="3 Months" {{ old('duration', $trader->duration) == '3 Months' ? 'selected' : '' }}>3 Months</option>
                                        <option value="4 Months" {{ old('duration', $trader->duration) == '4 Months' ? 'selected' : '' }}>4 Months</option>
                                        <option value="5 Months" {{ old('duration', $trader->duration) == '5 Months' ? 'selected' : '' }}>5 Months</option>
                                        <option value="6 Months" {{ old('duration', $trader->duration) == '6 Months' ? 'selected' : '' }}>6 Months</option>
                                        <option value="1 Year" {{ old('duration', $trader->duration) == '1 Year' ? 'selected' : '' }}>1 Year</option>
                                    </select>
                                    @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="roi_percentage">Expected ROI (%) *</label>
                                            <input type="number" 
                                                   class="form-control @error('roi_percentage') is-invalid @enderror" 
                                                   id="roi_percentage" 
                                                   name="roi_percentage" 
                                                   value="{{ old('roi_percentage', $trader->roi_percentage) }}" 
                                                   step="0.01" 
                                                   required>
                                            @error('roi_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="win_rate">Win Rate (%) *</label>
                                            <input type="number" 
                                                   class="form-control @error('win_rate') is-invalid @enderror" 
                                                   id="win_rate" 
                                                   name="win_rate" 
                                                   value="{{ old('win_rate', $trader->win_rate) }}" 
                                                   step="0.01" 
                                                   max="100" 
                                                   required>
                                            @error('win_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="active_trades">Active Trades *</label>
                                            <input type="number" 
                                                   class="form-control @error('active_trades') is-invalid @enderror" 
                                                   id="active_trades" 
                                                   name="active_trades" 
                                                   value="{{ old('active_trades', $trader->active_trades) }}" 
                                                   required>
                                            @error('active_trades')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="risk_level">Risk Level *</label>
                                    <select class="form-control @error('risk_level') is-invalid @enderror" 
                                            id="risk_level" 
                                            name="risk_level" 
                                            required>
                                        <option value="">Select Risk Level</option>
                                        <option value="Low" {{ old('risk_level', $trader->risk_level) == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Medium" {{ old('risk_level', $trader->risk_level) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="High" {{ old('risk_level', $trader->risk_level) == 'High' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('risk_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="active" {{ old('status', $trader->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $trader->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">
                                <h5 class="mb-3">Automatic Profit Distribution</h5>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="auto_distribute" 
                                               name="auto_distribute" 
                                               value="on"
                                               {{ old('auto_distribute', $trader->auto_distribute) == 'on' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="auto_distribute">
                                            Enable Automatic Profit Distribution
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">When enabled, profits will be distributed automatically based on the interval below</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="distribution_interval">Distribution Interval</label>
                                            <select class="form-control @error('distribution_interval') is-invalid @enderror" 
                                                    id="distribution_interval" 
                                                    name="distribution_interval">
                                                <option value="5 Minutes" {{ old('distribution_interval', $trader->distribution_interval) == '5 Minutes' ? 'selected' : '' }}>Every 5 Minutes</option>
                                                <option value="30 Minutes" {{ old('distribution_interval', $trader->distribution_interval) == '30 Minutes' ? 'selected' : '' }}>Every 30 Minutes</option>
                                                <option value="Hourly" {{ old('distribution_interval', $trader->distribution_interval) == 'Hourly' ? 'selected' : '' }}>Hourly</option>
                                                <option value="Daily" {{ old('distribution_interval', $trader->distribution_interval) == 'Daily' ? 'selected' : '' }}>Daily</option>
                                                <option value="Weekly" {{ old('distribution_interval', $trader->distribution_interval) == 'Weekly' ? 'selected' : '' }}>Weekly</option>
                                                <option value="Monthly" {{ old('distribution_interval', $trader->distribution_interval) == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                            </select>
                                            <small class="form-text text-muted">How often to distribute profits to subscribers</small>
                                            @error('distribution_interval')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="distribution_percentage">Distribution Percentage (%)</label>
                                            <input type="number" 
                                                   class="form-control @error('distribution_percentage') is-invalid @enderror" 
                                                   id="distribution_percentage" 
                                                   name="distribution_percentage" 
                                                   value="{{ old('distribution_percentage', $trader->distribution_percentage) }}" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100">
                                            <small class="form-text text-muted">Percentage of invested capital to distribute per interval</small>
                                            @error('distribution_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info" role="alert">
                                    <i class="fa fa-info-circle"></i> <strong>Note:</strong> Automatic distribution runs every 5 minutes and checks if the interval has elapsed. Ensure the distribution percentage and interval combination does not exceed the expected ROI to avoid over-distribution.
                                </div>

                                <div class="form-group text-right mt-4">
                                    <a href="{{ route('admin.copytrading.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check"></i> Update Trader
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>
@endsection
