@extends('layouts.app')

@section('content')
@include('admin.topmenu')
@include('admin.sidebar')

<div class="main-panel">
    <div class="content bg-light">
        <div class="page-inner">
            <div class="mt-2 mb-4">
                <h1 class="title1 text-dark">Edit Trading Bot</h1>
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
                <div class="col-lg-10 offset-lg-1">
                    <div class="card shadow">
                        <div class="card-body">
                            <form action="{{ route('admin.bot-trading.update', $bot) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <h4 class="text-dark mb-3"><i class="fa fa-info-circle"></i> Basic Information</h4>
                                <hr>

                                @if($bot->photo)
                                <div class="form-group">
                                    <label>Current Photo</label>
                                    <div>
                                        <img src="{{ asset('storage/app/public/' . $bot->photo) }}" alt="{{ $bot->name }}" 
                                             class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>
                                @endif

                                <div class="form-group">
                                    <label for="name">Bot Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $bot->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Description *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" required>{{ old('description', $bot->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="strategy_type">Strategy Type *</label>
                                            <select class="form-control @error('strategy_type') is-invalid @enderror" 
                                                    id="strategy_type" name="strategy_type" required>
                                                <option value="Scalping" {{ old('strategy_type', $bot->strategy_type) == 'Scalping' ? 'selected' : '' }}>Scalping</option>
                                                <option value="Day Trading" {{ old('strategy_type', $bot->strategy_type) == 'Day Trading' ? 'selected' : '' }}>Day Trading</option>
                                                <option value="Swing Trading" {{ old('strategy_type', $bot->strategy_type) == 'Swing Trading' ? 'selected' : '' }}>Swing Trading</option>
                                            </select>
                                            @error('strategy_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="photo">Change Photo</label>
                                            <input type="file" class="form-control-file @error('photo') is-invalid @enderror" 
                                                   id="photo" name="photo" accept="image/*">
                                            <small class="form-text text-muted">Leave empty to keep current photo</small>
                                            @error('photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <h4 class="text-dark mb-3 mt-4"><i class="fa fa-chart-line"></i> Performance Metrics</h4>
                                <hr>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="win_rate">Win Rate (%) *</label>
                                            <input type="number" class="form-control @error('win_rate') is-invalid @enderror" 
                                                   id="win_rate" name="win_rate" value="{{ old('win_rate', $bot->win_rate) }}" 
                                                   min="0" max="100" step="0.01" required>
                                            @error('win_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="roi_percentage">ROI (%) *</label>
                                            <input type="number" class="form-control @error('roi_percentage') is-invalid @enderror" 
                                                   id="roi_percentage" name="roi_percentage" value="{{ old('roi_percentage', $bot->roi_percentage) }}" 
                                                   min="0" step="0.01" required>
                                            @error('roi_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="total_trades">Total Trades</label>
                                            <input type="number" class="form-control @error('total_trades') is-invalid @enderror" 
                                                   id="total_trades" name="total_trades" value="{{ old('total_trades', $bot->total_trades) }}" 
                                                   min="0">
                                            @error('total_trades')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="total_profit">Total Profit ($)</label>
                                            <input type="number" class="form-control @error('total_profit') is-invalid @enderror" 
                                                   id="total_profit" name="total_profit" value="{{ old('total_profit', $bot->total_profit) }}" 
                                                   step="0.01">
                                            @error('total_profit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status *</label>
                                            <select class="form-control @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="active" {{ old('status', $bot->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $bot->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fake_active_users">Boost Active Users Count</label>
                                            <input type="number" 
                                                   class="form-control @error('fake_active_users') is-invalid @enderror" 
                                                   id="fake_active_users" 
                                                   name="fake_active_users" 
                                                   value="{{ old('fake_active_users', $bot->fake_active_users ?? 0) }}" 
                                                   min="0">
                                            <small class="form-text text-muted">Add to real subscribers count (Real: {{ $bot->activeSubscriptions()->count() }})</small>
                                            @error('fake_active_users')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <h4 class="text-dark mb-3 mt-4"><i class="fa fa-dollar-sign"></i> Investment Limits</h4>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="min_capital">Minimum Capital ($) *</label>
                                            <input type="number" class="form-control @error('min_capital') is-invalid @enderror" 
                                                   id="min_capital" name="min_capital" value="{{ old('min_capital', $bot->min_capital) }}" 
                                                   min="0" step="0.01" required>
                                            @error('min_capital')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_capital">Maximum Capital ($) *</label>
                                            <input type="number" class="form-control @error('max_capital') is-invalid @enderror" 
                                                   id="max_capital" name="max_capital" value="{{ old('max_capital', $bot->max_capital) }}" 
                                                   min="0" step="0.01" required>
                                            @error('max_capital')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <h4 class="text-dark mb-3 mt-4"><i class="fa fa-robot"></i> Automation Settings</h4>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="auto_trade">Auto Trading *</label>
                                            <select class="form-control @error('auto_trade') is-invalid @enderror" 
                                                    id="auto_trade" name="auto_trade" required>
                                                <option value="on" {{ old('auto_trade', $bot->auto_trade) == 'on' ? 'selected' : '' }}>Enabled</option>
                                                <option value="off" {{ old('auto_trade', $bot->auto_trade) == 'off' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                            @error('auto_trade')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="trade_interval">Trade Interval *</label>
                                            <select class="form-control @error('trade_interval') is-invalid @enderror" 
                                                    id="trade_interval" name="trade_interval" required>
                                                <option value="5 Minutes" {{ old('trade_interval', $bot->trade_interval) == '5 Minutes' ? 'selected' : '' }}>5 Minutes</option>
                                                <option value="30 Minutes" {{ old('trade_interval', $bot->trade_interval) == '30 Minutes' ? 'selected' : '' }}>30 Minutes</option>
                                                <option value="Hourly" {{ old('trade_interval', $bot->trade_interval) == 'Hourly' ? 'selected' : '' }}>Hourly</option>
                                                <option value="Daily" {{ old('trade_interval', $bot->trade_interval) == 'Daily' ? 'selected' : '' }}>Daily</option>
                                            </select>
                                            @error('trade_interval')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <h4 class="text-dark mb-3 mt-4"><i class="fa fa-percentage"></i> Profit/Loss Ranges</h4>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="min_profit">Min Profit (%) *</label>
                                            <input type="number" class="form-control @error('min_profit') is-invalid @enderror" 
                                                   id="min_profit" name="min_profit" value="{{ old('min_profit', $bot->min_profit) }}" 
                                                   min="0" step="0.01" required>
                                            @error('min_profit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_profit">Max Profit (%) *</label>
                                            <input type="number" class="form-control @error('max_profit') is-invalid @enderror" 
                                                   id="max_profit" name="max_profit" value="{{ old('max_profit', $bot->max_profit) }}" 
                                                   min="0" step="0.01" required>
                                            @error('max_profit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="min_loss">Min Loss (%) *</label>
                                            <input type="number" class="form-control @error('min_loss') is-invalid @enderror" 
                                                   id="min_loss" name="min_loss" value="{{ old('min_loss', $bot->min_loss) }}" 
                                                   min="0" step="0.01" required>
                                            @error('min_loss')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_loss">Max Loss (%) *</label>
                                            <input type="number" class="form-control @error('max_loss') is-invalid @enderror" 
                                                   id="max_loss" name="max_loss" value="{{ old('max_loss', $bot->max_loss) }}" 
                                                   min="0" step="0.01" required>
                                            @error('max_loss')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-save"></i> Update Bot
                                    </button>
                                    <a href="{{ route('admin.bot-trading.index') }}" class="btn btn-secondary btn-lg ml-2">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
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
