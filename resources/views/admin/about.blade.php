@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content bg-light">
            <div class="page-inner">
                <div class="mt-2 mb-4">
                    <h3 class="text-dark font-weight-bold">About BrembyProMax</h3>
                    <p class="text-muted">Professional Multi-Asset Investment & Trading Platform</p>
                </div>

                <x-danger-alert />
                <x-success-alert />


                <!-- Version Info -->
                <div class="row mb-4">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-3" style="font-size: 3rem; color: #0F9D58;">
                                    <i class="fas fa-code-branch"></i>
                                </div>
                                <h5 class="font-weight-bold text-dark">Version Info</h5>
                                <p class="text-muted mb-2">BrembyProMax</p>
                                <h3 class="text-primary mb-0">v5.0 Pro Max</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-3" style="font-size: 3rem; color: #0F9D58;">
                                    <i class="fab fa-laravel"></i>
                                </div>
                                <h5 class="font-weight-bold text-dark">Framework</h5>
                                <p class="text-muted mb-2">Built with</p>
                                <h3 class="text-danger mb-0">Laravel 8</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-3" style="font-size: 3rem; color: #0F9D58;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h5 class="font-weight-bold text-dark">Security</h5>
                                <p class="text-muted mb-2">Enterprise-grade</p>
                                <h3 class="text-success mb-0">2FA + KYC</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Core Features Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0 py-3">
                                <h4 class="mb-0 font-weight-bold text-dark">
                                    <i class="fas fa-rocket text-primary mr-2"></i>Core Features
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Investment Management -->
                                    <div class="col-lg-6 mb-4">
                                        <div class="feature-item p-3 rounded" style="background: #f8f9fa;">
                                            <h5 class="font-weight-bold text-primary mb-3">
                                                <i class="fas fa-chart-line mr-2"></i>Investment Management
                                            </h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Unlimited investment plans</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Flexible ROI calculation</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Automatic profit distribution</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Capital return options</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Investment history tracking</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Plan upgrade system</li>
                                                <li><i class="fas fa-check text-success mr-2"></i>Compound interest support</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Stock Investment -->
                                    <div class="col-lg-6 mb-4">
                                        <div class="feature-item p-3 rounded" style="background: #f8f9fa;">
                                            <h5 class="font-weight-bold text-primary mb-3">
                                                <i class="fas fa-chart-candlestick mr-2"></i>Stock Investment
                                            </h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Live TradingView charts</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Real-time stock data</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Stock-based plans</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Popular stocks (AAPL, TSLA)</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Automatic ROI processing</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Dark mode support</li>
                                                <li><i class="fas fa-check text-success mr-2"></i>Investment tracking</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Cryptocurrency Trading -->
                                    <div class="col-lg-6 mb-4">
                                        <div class="feature-item p-3 rounded" style="background: #f8f9fa;">
                                            <h5 class="font-weight-bold text-primary mb-3">
                                                <i class="fab fa-bitcoin mr-2"></i>Cryptocurrency Trading
                                            </h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Live crypto prices</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Real-time market data</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Buy/Sell functionality</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Trading history</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Watchlist management</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Multi-exchange API</li>
                                                <li><i class="fas fa-check text-success mr-2"></i>Portfolio tracking</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Stock Trading -->
                                    <div class="col-lg-6 mb-4">
                                        <div class="feature-item p-3 rounded" style="background: #f8f9fa;">
                                            <h5 class="font-weight-bold text-primary mb-3">
                                                <i class="fas fa-chart-bar mr-2"></i>Stock Trading
                                            </h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Live stock market data</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Real-time quotes</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Order management</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Stock portfolio</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Market analysis tools</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Trading preferences</li>
                                                <li><i class="fas fa-check text-success mr-2"></i>Multi-market support</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Copy Trading -->
                                    <div class="col-lg-6 mb-4">
                                        <div class="feature-item p-3 rounded" style="background: #f8f9fa;">
                                            <h5 class="font-weight-bold text-primary mb-3">
                                                <i class="fas fa-copy mr-2"></i>Copy Trading
                                            </h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Expert trader profiles</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Performance tracking</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Risk management tools</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Profit distribution</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Trade execution logs</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Subscription management</li>
                                                <li><i class="fas fa-check text-success mr-2"></i>Automated profit sharing</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Bot Trading -->
                                    <div class="col-lg-6 mb-4">
                                        <div class="feature-item p-3 rounded" style="background: #f8f9fa;">
                                            <h5 class="font-weight-bold text-primary mb-3">
                                                <i class="fas fa-robot mr-2"></i>Bot Trading
                                            </h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>AI-powered bots</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Multiple strategies</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>24/7 automated trading</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Configurable profit/loss</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Real-time execution</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Performance metrics</li>
                                                <li><i class="fas fa-check text-success mr-2"></i>Flexible durations</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Crypto Staking -->
                                    <div class="col-lg-6 mb-4">
                                        <div class="feature-item p-3 rounded" style="background: #f8f9fa;">
                                            <h5 class="font-weight-bold text-primary mb-3">
                                                <i class="fas fa-coins mr-2"></i>Crypto Staking
                                            </h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Multiple staking plans</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Flexible lock periods</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Auto reward distribution</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Compound rewards</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Leaderboard system</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Subscription management</li>
                                                <li><i class="fas fa-check text-success mr-2"></i>Balance tracking</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Payment Systems -->
                                    <div class="col-lg-6 mb-4">
                                        <div class="feature-item p-3 rounded" style="background: #f8f9fa;">
                                            <h5 class="font-weight-bold text-primary mb-3">
                                                <i class="fas fa-credit-card mr-2"></i>Payment Gateways
                                            </h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Stripe integration</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>PayStack support</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Flutterwave/Rave</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>BitPay (Bitcoin)</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>CoinPayments</li>
                                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Manual payments</li>
                                                <li><i class="fas fa-check text-success mr-2"></i>Auto verification</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security & Admin Features -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h4 class="mb-0 font-weight-bold text-dark">
                                    <i class="fas fa-shield-alt text-success mr-2"></i>Security Features
                                </h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Two-Factor Authentication</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Multi-Guard System</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>KYC Verification</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Email Verification</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>IP Blocking</li>
                                    <li><i class="fas fa-check-circle text-success mr-2"></i>Activity Logging</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h4 class="mb-0 font-weight-bold text-dark">
                                    <i class="fas fa-user-cog text-info mr-2"></i>Admin Panel
                                </h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>User Management</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Deposit/Withdrawal Approval</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>KYC Dashboard</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Plan Management</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Payment Settings</li>
                                    <li><i class="fas fa-check-circle text-success mr-2"></i>Reports & Analytics</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technology Stack -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0 py-3">
                                <h4 class="mb-0 font-weight-bold text-dark">
                                    <i class="fas fa-code text-danger mr-2"></i>Technology Stack
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class="fab fa-laravel" style="font-size: 3rem; color: #FF2D20;"></i>
                                        <h6 class="mt-2 font-weight-bold">Laravel 8</h6>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class="fas fa-bolt" style="font-size: 3rem; color: #FB70A9;"></i>
                                        <h6 class="mt-2 font-weight-bold">Livewire</h6>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class="fas fa-wind" style="font-size: 3rem; color: #38BDF8;"></i>
                                        <h6 class="mt-2 font-weight-bold">Tailwind</h6>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class="fas fa-mountain" style="font-size: 3rem; color: #8BC34A;"></i>
                                        <h6 class="mt-2 font-weight-bold">Alpine.js</h6>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class="fas fa-database" style="font-size: 3rem; color: #00758F;"></i>
                                        <h6 class="mt-2 font-weight-bold">MySQL</h6>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class="fab fa-bootstrap" style="font-size: 3rem; color: #7952B3;"></i>
                                        <h6 class="mt-2 font-weight-bold">Bootstrap</h6>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class="fab fa-js-square" style="font-size: 3rem; color: #F7DF1E;"></i>
                                        <h6 class="mt-2 font-weight-bold">jQuery</h6>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class="fas fa-chart-line" style="font-size: 3rem; color: #2196F3;"></i>
                                        <h6 class="mt-2 font-weight-bold">TradingView</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- How It Works -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0 py-3">
                                <h4 class="mb-0 font-weight-bold text-dark">
                                    <i class="fas fa-cogs text-warning mr-2"></i>How It Works
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                                 style="width: 60px; height: 60px; background: #0F9D58; color: white; font-weight: bold; font-size: 1.5rem;">
                                                1
                                            </div>
                                            <h6 class="font-weight-bold">Registration & KYC</h6>
                                            <p class="text-muted small">Users register and complete KYC verification</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                                 style="width: 60px; height: 60px; background: #0F9D58; color: white; font-weight: bold; font-size: 1.5rem;">
                                                2
                                            </div>
                                            <h6 class="font-weight-bold">Deposits & Payments</h6>
                                            <p class="text-muted small">Multiple payment gateways for easy deposits</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                                 style="width: 60px; height: 60px; background: #0F9D58; color: white; font-weight: bold; font-size: 1.5rem;">
                                                3
                                            </div>
                                            <h6 class="font-weight-bold">Investment Selection</h6>
                                            <p class="text-muted small">Choose from plans, trading, bots, or staking</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                                 style="width: 60px; height: 60px; background: #0F9D58; color: white; font-weight: bold; font-size: 1.5rem;">
                                                4
                                            </div>
                                            <h6 class="font-weight-bold">Automated ROI</h6>
                                            <p class="text-muted small">Profits automatically credited every 5 minutes</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                                 style="width: 60px; height: 60px; background: #0F9D58; color: white; font-weight: bold; font-size: 1.5rem;">
                                                5
                                            </div>
                                            <h6 class="font-weight-bold">Trading & Monitoring</h6>
                                            <p class="text-muted small">Real-time trading with live charts</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                                 style="width: 60px; height: 60px; background: #0F9D58; color: white; font-weight: bold; font-size: 1.5rem;">
                                                6
                                            </div>
                                            <h6 class="font-weight-bold">Withdrawals</h6>
                                            <p class="text-muted small">Request and receive your profits easily</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <!-- Module Usage Guides -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0 py-3">
                                <h4 class="mb-0 font-weight-bold text-dark">
                                    <i class="fas fa-book-open text-primary mr-2"></i>Module Usage Guide (For Non-Technical Users)
                                </h4>
                                <p class="text-muted mb-0 small">Step-by-step instructions for Admin and Users</p>
                            </div>
                            <div class="card-body">
                                
                                <!-- Bot Trading Guide -->
                                <div class="mb-5">
                                    <h5 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-robot mr-2"></i>1. Bot Trading Module
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-success mb-3">
                                                        <i class="fas fa-user-shield mr-2"></i>For Admin:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Go to <strong>Bot Trading → Create Bot</strong> in admin sidebar</li>
                                                        <li class="mb-2">Fill in bot details:
                                                            <ul class="mt-1">
                                                                <li>Name (e.g., "Quantum Trader AI")</li>
                                                                <li>Description of strategy</li>
                                                                <li>Win Rate % (60-90% recommended)</li>
                                                                <li>ROI % (expected returns)</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Set investment limits (Min: $100, Max: $10,000)</li>
                                                        <li class="mb-2">Choose strategy type (Scalping/Day Trading/Swing)</li>
                                                        <li class="mb-2">Configure profit/loss ranges (Min/Max %)</li>
                                                        <li class="mb-2">Set trade interval (e.g., "Every 5 minutes")</li>
                                                        <li class="mb-2">Enable/Disable auto trading</li>
                                                        <li class="mb-2">Click <strong>Create Bot</strong> to save</li>
                                                        <li>Bot will appear on user's bot trading page</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-info mb-3">
                                                        <i class="fas fa-user mr-2"></i>For User:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Deposit funds to your account balance</li>
                                                        <li class="mb-2">Navigate to <strong>Bot Trading</strong> menu</li>
                                                        <li class="mb-2">Browse available trading bots</li>
                                                        <li class="mb-2">View bot details:
                                                            <ul class="mt-1">
                                                                <li>Strategy type and win rate</li>
                                                                <li>Expected ROI and performance</li>
                                                                <li>Recent trade history</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Click <strong>Subscribe</strong> on preferred bot</li>
                                                        <li class="mb-2">Enter investment amount (within bot's limits)</li>
                                                        <li class="mb-2">Select subscription duration (1-90 days)</li>
                                                        <li class="mb-2">Confirm subscription</li>
                                                        <li class="mb-2">Bot trades automatically 24/7</li>
                                                        <li>View profits in <strong>Bot Trading Subscriptions</strong></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Copy Trading Guide -->
                                <div class="mb-5">
                                    <h5 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-copy mr-2"></i>2. Copy Trading Module
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-success mb-3">
                                                        <i class="fas fa-user-shield mr-2"></i>For Admin:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Navigate to <strong>Copy Trading → Create Trader</strong></li>
                                                        <li class="mb-2">Add expert trader profile:
                                                            <ul class="mt-1">
                                                                <li>Trader name (e.g., "John Smith")</li>
                                                                <li>Trading experience (years)</li>
                                                                <li>Specialization (Crypto/Stocks/Forex)</li>
                                                                <li>Bio and strategy description</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Set performance metrics:
                                                            <ul class="mt-1">
                                                                <li>Win rate % (70-95%)</li>
                                                                <li>Total profit %</li>
                                                                <li>Risk level (Low/Medium/High)</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Configure subscription fees & profit sharing</li>
                                                        <li class="mb-2">Set minimum investment amount</li>
                                                        <li class="mb-2">Upload trader photo (optional)</li>
                                                        <li class="mb-2">Enable/Disable trader status</li>
                                                        <li class="mb-2">Click <strong>Create Trader</strong></li>
                                                        <li>Go to trader details to distribute profits to subscribers</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-info mb-3">
                                                        <i class="fas fa-user mr-2"></i>For User:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Ensure account has sufficient balance</li>
                                                        <li class="mb-2">Go to <strong>Copy Trading</strong> section</li>
                                                        <li class="mb-2">Browse expert trader profiles</li>
                                                        <li class="mb-2">Review trader performance:
                                                            <ul class="mt-1">
                                                                <li>Win rate and profit history</li>
                                                                <li>Trading strategy details</li>
                                                                <li>Risk level assessment</li>
                                                                <li>Number of followers</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Select trader to follow</li>
                                                        <li class="mb-2">Click <strong>Subscribe</strong> button</li>
                                                        <li class="mb-2">Enter investment amount</li>
                                                        <li class="mb-2">Confirm subscription</li>
                                                        <li class="mb-2">Your trades will copy the expert automatically</li>
                                                        <li>Monitor performance in <strong>My Subscriptions</strong></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stock Investment Guide -->
                                <div class="mb-5">
                                    <h5 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-chart-candlestick mr-2"></i>3. Stock Investment Module
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-success mb-3">
                                                        <i class="fas fa-user-shield mr-2"></i>For Admin:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Go to <strong>Investment → Create New Plan</strong></li>
                                                        <li class="mb-2">Select <strong>Plan Type: Stock</strong></li>
                                                        <li class="mb-2">Enter stock symbol in TradingView format:
                                                            <ul class="mt-1">
                                                                <li>NASDAQ:AAPL (Apple)</li>
                                                                <li>NYSE:TSLA (Tesla)</li>
                                                                <li>NASDAQ:MSFT (Microsoft)</li>
                                                                <li>NASDAQ:GOOGL (Google)</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Add company name and description</li>
                                                        <li class="mb-2">Set plan details:
                                                            <ul class="mt-1">
                                                                <li>Minimum/Maximum amounts</li>
                                                                <li>ROI percentage and interval</li>
                                                                <li>Plan duration</li>
                                                                <li>Capital return (Yes/No)</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Click <strong>Create Plan</strong></li>
                                                        <li>Users will see live TradingView chart on plan page</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-info mb-3">
                                                        <i class="fas fa-user mr-2"></i>For User:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Visit <strong>Stock Plans</strong> page</li>
                                                        <li class="mb-2">Browse available stock investments</li>
                                                        <li class="mb-2">View plan details:
                                                            <ul class="mt-1">
                                                                <li>Live stock chart (TradingView)</li>
                                                                <li>Expected ROI and duration</li>
                                                                <li>Investment range</li>
                                                                <li>Company information</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Click <strong>Invest Now</strong></li>
                                                        <li class="mb-2">Enter investment amount</li>
                                                        <li class="mb-2">Choose payment method from balance</li>
                                                        <li class="mb-2">Confirm investment</li>
                                                        <li class="mb-2">ROI credited automatically based on schedule</li>
                                                        <li class="mb-2">Track progress in <strong>My Plans</strong></li>
                                                        <li>Chart updates in real-time with market data</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stock Trading Guide -->
                                <div class="mb-5">
                                    <h5 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-chart-bar mr-2"></i>4. Stock Trading Module
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-success mb-3">
                                                        <i class="fas fa-user-shield mr-2"></i>For Admin:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Navigate to <strong>Stock Trading → Settings</strong></li>
                                                        <li class="mb-2">Configure trading fees:
                                                            <ul class="mt-1">
                                                                <li>Buy transaction fee %</li>
                                                                <li>Sell transaction fee %</li>
                                                                <li>Minimum trade amount</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Go to <strong>Manage Stocks</strong></li>
                                                        <li class="mb-2">Add stocks manually or enable API</li>
                                                        <li class="mb-2">Set initial prices for demo mode</li>
                                                        <li class="mb-2">View <strong>All Trades</strong> to monitor activity</li>
                                                        <li class="mb-2">Check user holdings and portfolios</li>
                                                        <li class="mb-2">Create manual trades for specific users if needed</li>
                                                        <li>Export reports for analysis</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-info mb-3">
                                                        <i class="fas fa-user mr-2"></i>For User:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Go to <strong>Stock Trading</strong> dashboard</li>
                                                        <li class="mb-2">Browse available stocks with live prices</li>
                                                        <li class="mb-2">Use search to find specific stocks</li>
                                                        <li class="mb-2">Click on stock to view detailed chart</li>
                                                        <li class="mb-2">To Buy:
                                                            <ul class="mt-1">
                                                                <li>Click <strong>Buy</strong> button</li>
                                                                <li>Enter quantity or amount</li>
                                                                <li>Review order details</li>
                                                                <li>Confirm purchase</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">To Sell:
                                                            <ul class="mt-1">
                                                                <li>Go to <strong>Portfolio</strong></li>
                                                                <li>Select stock to sell</li>
                                                                <li>Enter quantity</li>
                                                                <li>Confirm sale</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">View order history in <strong>Orders</strong> tab</li>
                                                        <li>Monitor portfolio performance and P&L</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Crypto Trading Guide -->
                                <div class="mb-5">
                                    <h5 class="font-weight-bold text-primary mb-3">
                                        <i class="fab fa-bitcoin mr-2"></i>5. Crypto Trading Module
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-success mb-3">
                                                        <i class="fas fa-user-shield mr-2"></i>For Admin:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Go to <strong>Crypto Trading → All Trades</strong></li>
                                                        <li class="mb-2">Monitor all user crypto trades</li>
                                                        <li class="mb-2">Navigate to <strong>Manage Cryptos</strong></li>
                                                        <li class="mb-2">Enable/disable cryptocurrencies:
                                                            <ul class="mt-1">
                                                                <li>Bitcoin (BTC)</li>
                                                                <li>Ethereum (ETH)</li>
                                                                <li>Litecoin (LTC)</li>
                                                                <li>Ripple (XRP), etc.</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Set trading fees per crypto</li>
                                                        <li class="mb-2">Configure minimum trade amounts</li>
                                                        <li class="mb-2">Create manual trades for users if needed</li>
                                                        <li class="mb-2">View user-specific trade history</li>
                                                        <li>API integration handles live price updates</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-info mb-3">
                                                        <i class="fas fa-user mr-2"></i>For User:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Navigate to <strong>Crypto Trading</strong></li>
                                                        <li class="mb-2">View live cryptocurrency prices</li>
                                                        <li class="mb-2">Add coins to watchlist for quick access</li>
                                                        <li class="mb-2">Click cryptocurrency to open trading view</li>
                                                        <li class="mb-2">To Buy Crypto:
                                                            <ul class="mt-1">
                                                                <li>Select <strong>Buy</strong> tab</li>
                                                                <li>Choose order type (Market/Limit)</li>
                                                                <li>Enter amount in USD or crypto</li>
                                                                <li>Confirm order</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">To Sell Crypto:
                                                            <ul class="mt-1">
                                                                <li>Select <strong>Sell</strong> tab</li>
                                                                <li>Enter quantity to sell</li>
                                                                <li>Set price (if limit order)</li>
                                                                <li>Confirm sale</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">View holdings in <strong>Portfolio</strong></li>
                                                        <li class="mb-2">Check order history in <strong>Orders</strong></li>
                                                        <li>Cancel pending orders anytime</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Crypto Staking Guide -->
                                <div class="mb-0">
                                    <h5 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-coins mr-2"></i>6. Crypto Staking Module
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-success mb-3">
                                                        <i class="fas fa-user-shield mr-2"></i>For Admin:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Navigate to <strong>Crypto Staking → Create Plan</strong></li>
                                                        <li class="mb-2">Enter staking plan details:
                                                            <ul class="mt-1">
                                                                <li>Plan name (e.g., "Bitcoin 90-Day Staking")</li>
                                                                <li>Cryptocurrency (BTC, ETH, etc.)</li>
                                                                <li>APY % (Annual Percentage Yield)</li>
                                                                <li>Lock period (7, 30, 60, 90 days)</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Set minimum and maximum stake amounts</li>
                                                        <li class="mb-2">Configure reward distribution frequency</li>
                                                        <li class="mb-2">Enable compound staking option</li>
                                                        <li class="mb-2">Set early withdrawal penalty (if any)</li>
                                                        <li class="mb-2">Upload plan image/icon</li>
                                                        <li class="mb-2">Click <strong>Create Plan</strong></li>
                                                        <li class="mb-2">View all stakes in <strong>Staking Plans</strong></li>
                                                        <li class="mb-2">Check <strong>Leaderboard</strong> for top stakers</li>
                                                        <li>Force complete subscriptions if needed</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold text-info mb-3">
                                                        <i class="fas fa-user mr-2"></i>For User:
                                                    </h6>
                                                    <ol class="small mb-0">
                                                        <li class="mb-2">Go to <strong>Crypto Staking</strong> menu</li>
                                                        <li class="mb-2">Browse available staking plans</li>
                                                        <li class="mb-2">Compare plans by:
                                                            <ul class="mt-1">
                                                                <li>APY (higher = better returns)</li>
                                                                <li>Lock period (longer = higher rewards)</li>
                                                                <li>Cryptocurrency type</li>
                                                                <li>Minimum stake amount</li>
                                                            </ul>
                                                        </li>
                                                        <li class="mb-2">Click on plan to view full details</li>
                                                        <li class="mb-2">Click <strong>Subscribe</strong> button</li>
                                                        <li class="mb-2">Enter staking amount</li>
                                                        <li class="mb-2">Choose compound option (reinvest rewards)</li>
                                                        <li class="mb-2">Read and accept terms</li>
                                                        <li class="mb-2">Confirm subscription</li>
                                                        <li class="mb-2">Funds are locked for selected period</li>
                                                        <li class="mb-2">Rewards credited automatically</li>
                                                        <li class="mb-2">View earnings in <strong>My Subscriptions</strong></li>
                                                        <li class="mb-2">Check ranking in <strong>Leaderboard</strong></li>
                                                        <li>Unstake after lock period ends</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Important Notes -->
                                <div class="alert alert-info mt-4" role="alert">
                                    <h6 class="alert-heading font-weight-bold">
                                        <i class="fas fa-info-circle mr-2"></i>Important Notes:
                                    </h6>
                                    <ul class="mb-0 small">
                                        <li>All ROI processing runs automatically via Laravel scheduler every 5 minutes</li>
                                        <li>Users must have sufficient account balance before investing/trading</li>
                                        <li>Admin approval required for deposits and withdrawals</li>
                                        <li>KYC verification may be required depending on settings</li>
                                        <li>All modules can be enabled/disabled from Settings → App Settings</li>
                                        <li>Join our support channels if you need help setting up any module</li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        .feature-item {
            transition: all 0.3s ease;
        }
        .feature-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(15, 157, 88, 0.15);
        }
    </style>
@endsection
