@extends('layouts.dash')
@section('title', $title)
@section('content')
@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('dash/css/stripeglobal.css') }}">
    <link rel="stylesheet" href="{{ asset('dash/css/stripenormalize.css') }}">
@endsection

<!-- Modern Page Header with Animation -->
<div class="relative mb-8 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-primary/20 via-secondary/20 to-tertiary/20 rounded-3xl -z-10 blur-xl opacity-50"></div>
    <div class="px-6 py-8 rounded-3xl bg-gradient-to-r from-white/80 to-white/60 dark:from-dark-50/80 dark:to-dark-100/60 backdrop-blur-md border border-white/20 dark:border-dark-200/30 shadow-lg">
        <!-- Animated Elements -->
        <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
            <div class="floating-element elem-1 bg-primary/20 dark:bg-primary/30"></div>
            <div class="floating-element elem-2 bg-secondary/20 dark:bg-secondary/30"></div>
            <div class="floating-element elem-3 bg-tertiary/20 dark:bg-tertiary/30"></div>
            <div class="floating-element elem-4 bg-accent/20 dark:bg-accent/30"></div>
        </div>

        <!-- Header Content -->
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-dark dark:text-white">Make Payment</h1>
                    <p class="mt-2 text-base text-dark-300 dark:text-light-300">Complete your payment process</p>
                </div>
                <div>
                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-white/80 dark:bg-dark-50/80 backdrop-blur-sm border border-white/20 dark:border-dark-200/30 shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-secondary text-white">
                            <i class="fas fa-money-bill-wave text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs text-dark-300 dark:text-light-300">Payment Amount</p>
                            <p class="text-xl font-bold text-dark dark:text-white">{{ $settings->currency }}{{ number_format($amount) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<x-danger-alert />
<x-success-alert />

<!-- Modern Payment Container -->
<div class="max-w-4xl mx-auto">
    <div class="rounded-2xl overflow-hidden bg-white dark:bg-dark-50 shadow-xl shadow-primary/5 dark:shadow-primary/10 border border-light-200 dark:border-dark-200/50 transition-all duration-300">
        <!-- Payment Method Header -->
        <div class="p-6 border-b border-light-200 dark:border-dark-200/50">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center">
                    <i class="fas fa-credit-card text-accent-600 dark:text-accent-400"></i>
                </div>
                <div>
                    <p class="text-sm text-dark-300 dark:text-light-300">Your payment method</p>
                    <p class="text-lg font-semibold text-dark dark:text-white">{{ $payment_mode->name }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Content -->
        <div class="p-6">
            @if ($title != 'Complete Payment')
                @php
                    if ($payment_mode->name == 'Bitcoin') {
                        $coin = 'BTC';
                    } elseif ($payment_mode->name == 'Litecoin') {
                        $coin = 'LTC';
                    } elseif ($payment_mode->name == 'Ethereum') {
                        $coin = 'ETH';
                    } elseif ($payment_mode->name == 'BUSD') {
                        $coin = 'BUSD';
                    } else {
                        $coin = 'USDT.TRC20';
                    }
                @endphp

                <div class="mb-8">
                    <div class="mb-4 text-center p-4 rounded-xl bg-gradient-to-r from-primary-50 to-secondary-50 dark:from-dark-100 dark:to-dark-200 border border-light-200 dark:border-dark-200">
                        <p class="text-dark dark:text-white">You are to make payment of <span class="font-bold">{{ $settings->currency }}{{ number_format($amount) }}</span> using your selected payment method.</p>
                    </div>

                    
                    <div class="my-6 flex justify-center">
                        <div class="p-4 rounded-xl bg-white dark:bg-dark-100 shadow-md inline-block">
                           <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $payment_mode->wallet_address }}"
                                 alt="Payment QR Code"
                                 class="w-full h-auto rounded-lg">
                            <button type="button"
                                    @click="downloadQR()"
                                    class="absolute top-2 right-2 p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                <i data-lucide="download" class="w-4 h-4 text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                   
                </div>

                <div class="space-y-6">
                    @if ($settings->deposit_option != 'manual')
                        @if ($payment_mode->name == 'Bitcoin' or
                            $payment_mode->name == 'Litecoin' or
                            $payment_mode->name == 'Ethereum' or
                            $payment_mode->name == 'USDT' or
                            $payment_mode->name == 'BUSD')
                            @if ($payment_mode->name == 'USDT' and
                                $settings->auto_merchant_option == 'Binance' and
                                $settings->deposit_option == 'auto')
                                <div class="mb-6">
                                    <livewire:user.crypto-payment />
                                </div>
                            @else
                                <div class="flex justify-center mb-6">
                                    <a href="{{ url('dashboard/cpay') }}/{{ $amount }}/{{ $coin }}/{{ Auth::user()->id }}/new"
                                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium flex items-center gap-2 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-primary/20">
                                        <i class="fas fa-coins"></i>
                                        <span>Pay Via Coinpayment</span>
                                    </a>
                                </div>
                            @endif
                        @else
                            @if ((!empty($payment_mode->barcode) or $payment_mode->barcode != null) and
                                $payment_mode->methodtype != 'currency')
                                {{-- <div class="flex justify-center mb-6">
                                    <div class="p-4 rounded-xl bg-white dark:bg-dark-100 shadow-md">
                                        <img src="{{ asset('storage/' . $payment_mode->barcode) }}" alt="Payment QR Code" class="max-w-xs">
                                    </div>
                                </div> --}}
                            @endif
                        @endif
                    @endif

                    @if ($payment_mode->methodtype != 'currency')
                        @if (($payment_mode->name == 'Bitcoin' or
                            $payment_mode->name == 'Litecoin' or
                            $payment_mode->name == 'Ethereum' or
                            $payment_mode->name == 'USDT' or
                            $payment_mode->name == 'BUSD') and
                            $settings->deposit_option != 'manual')
                        @else
                            <div class="space-y-2 mb-6">
                                <h3 class="text-lg font-semibold text-dark dark:text-white">
                                    {{ $payment_mode->name }} Address:
                                </h3>
                                <div class="relative">
                                    <input type="text" class="w-full py-3 pl-4 pr-12 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 text-dark dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                        value="{{ $payment_mode->wallet_address }}" id="reflink" readonly>
                                    <button onclick="copyToClipboard('reflink')" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary/20 transition-all">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <p class="text-sm text-dark-300 dark:text-light-300">
                                    <span class="font-semibold">Network Type:</span> {{ $payment_mode->network }}
                                </p>
                            </div>
                        @endif
                    @else
                        <h3 class="text-xl font-bold text-dark dark:text-white mb-4">
                            {{ $payment_mode->name }}:
                        </h3>
                        @if ($payment_mode->defaultpay == 'yes')
                            @if ($payment_mode->name == 'Credit Card' and $settings->credit_card_provider == 'Paystack')
                                <?php $payamount = $amount * 100; ?>
                                {{-- Paystack Option --}}
                                <div id="paystack" class="mb-6">
                                    <form method="POST" action="{{ route('pay.paystack') }}"
                                        accept-charset="UTF-8" class="form-horizontal"
                                        role="form">
                                        <input type="hidden" name="email"
                                            value="{{ Auth::user()->email }}">
                                        <input type="hidden" name="amount"
                                            value="{{ $payamount }}">
                                        <input type="hidden" name="currency"
                                            value="{{ $settings->s_currency }}">
                                        <input type="hidden" name="metadata"
                                            value="{{ json_encode($array = ['key_name' => 'value']) }}">
                                        <input type="hidden" name="reference"
                                            value="{{ Paystack::genTranxRef() }}">
                                        <input type="hidden" name="_token"
                                            value="{{ csrf_token() }}">

                                        <button class="px-6 py-3 rounded-xl bg-gradient-to-r from-tertiary to-purple hover:from-tertiary-600 hover:to-purple-600 text-white font-medium flex items-center gap-2 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-tertiary/20" type="submit">
                                            <i class="fas fa-credit-card"></i>
                                            <span>Pay with Card</span>
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if ($payment_mode->name == 'Credit Card' and $settings->credit_card_provider == 'Flutterwave')
                                <div class="mb-6">
                                    <form method="POST" action="{{ route('paybyflutterwave') }}">
                                        {{ csrf_field() }}

                                        <input type="hidden" name="name"
                                            value="{{ Auth::user()->name }}" />
                                        <input name="email" type="hidden"
                                            value="{{ Auth::user()->email }}" />
                                        <input name="phone" type="hidden"
                                            value="{{ Auth::user()->phone }}" />
                                        <input name="amount" type="hidden"
                                            value="{{ $amount }}" />

                                        <button class="px-6 py-3 rounded-xl bg-gradient-to-r from-tertiary to-purple hover:from-tertiary-600 hover:to-purple-600 text-white font-medium flex items-center gap-2 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-tertiary/20" type="submit">
                                            <i class="fas fa-credit-card"></i>
                                            <span>Pay with Card</span>
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if ($payment_mode->name == 'Credit Card' and $settings->credit_card_provider == 'Stripe')
                                <div class="mb-8 max-w-lg mx-auto">
                                    <div class="p-6 rounded-xl bg-white dark:bg-dark-100 shadow-lg border border-light-200 dark:border-dark-200">
                                        <form id="payment-form" class="space-y-6">
                                            @csrf
                                            <div class="sr-combo-inputs-row">
                                                <div class="sr-input sr-card-element" id="card-element">
                                                </div>
                                            </div>
                                            <button id="stripesubmit" class="w-full px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium flex items-center justify-center gap-2 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-primary/20">
                                                <div class="spinner hidden" id="spinner"></div>
                                                <span id="buttontext">Pay Now</span>
                                            </button>
                                        </form>
                                    </div>

                                    <div class="hidden mt-4 p-4 rounded-xl bg-secondary-50 dark:bg-secondary-900/20 text-secondary-700 dark:text-secondary-300 text-center" id="stripesuccess">
                                        <span>Payment Completed, redirecting.....</span>
                                    </div>

                                    <form id="selectform" method="POST" action="javascript:void(0)">
                                        @csrf
                                        <input type="hidden" name="amount" value="{{ $amount }}">
                                    </form>
                                </div>
                            @endif

                            @if ($payment_mode->name == 'Paypal')
                                <div class="mb-6">
                                    @include('includes.paypal')
                                </div>
                            @endif

                            @if ($payment_mode->name == 'Bank Transfer')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    @if (!empty($payment_mode->bankname))
                                        <div class="space-y-2">
                                            <h4 class="text-sm font-semibold text-dark-300 dark:text-light-300">Bank Name</h4>
                                            <div class="relative">
                                                <input type="text" class="w-full py-3 pl-4 pr-12 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 text-dark dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                                    value="{{ $payment_mode->bankname }}" readonly>
                                                <button onclick="copyToClipboard(this.previousElementSibling)" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary/20 transition-all">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($payment_mode->account_name))
                                        <div class="space-y-2">
                                            <h4 class="text-sm font-semibold text-dark-300 dark:text-light-300">Account Name</h4>
                                            <div class="relative">
                                                <input type="text" class="w-full py-3 pl-4 pr-12 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 text-dark dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                                    value="{{ $payment_mode->account_name }}" readonly>
                                                <button onclick="copyToClipboard(this.previousElementSibling)" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary/20 transition-all">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($payment_mode->account_number))
                                        <div class="space-y-2">
                                            <h4 class="text-sm font-semibold text-dark-300 dark:text-light-300">Account Number</h4>
                                            <div class="relative">
                                                <input type="text" class="w-full py-3 pl-4 pr-12 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 text-dark dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                                    value="{{ $payment_mode->account_number }}" readonly>
                                                <button onclick="copyToClipboard(this.previousElementSibling)" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary/20 transition-all">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($payment_mode->swift_code))
                                        <div class="space-y-2">
                                            <h4 class="text-sm font-semibold text-dark-300 dark:text-light-300">Swift Code</h4>
                                            <div class="relative">
                                                <input type="text" class="w-full py-3 pl-4 pr-12 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 text-dark dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                                    value="{{ $payment_mode->swift_code }}" readonly>
                                                <button onclick="copyToClipboard(this.previousElementSibling)" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary/20 transition-all">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                @if (!empty($payment_mode->bankname))
                                    <div class="space-y-2">
                                        <h4 class="text-sm font-semibold text-dark-300 dark:text-light-300">Bank Name</h4>
                                        <div class="relative">
                                            <input type="text" class="w-full py-3 pl-4 pr-12 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 text-dark dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                                value="{{ $payment_mode->bankname }}" readonly>
                                            <button onclick="copyToClipboard(this.previousElementSibling)" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary/20 transition-all">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($payment_mode->account_name))
                                    <div class="space-y-2">
                                        <h4 class="text-sm font-semibold text-dark-300 dark:text-light-300">Account Name</h4>
                                        <div class="relative">
                                            <input type="text" class="w-full py-3 pl-4 pr-12 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 text-dark dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                                value="{{ $payment_mode->account_name }}" readonly>
                                            <button onclick="copyToClipboard(this.previousElementSibling)" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary/20 transition-all">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($payment_mode->account_number))
                                    <div class="space-y-2">
                                        <h4 class="text-sm font-semibold text-dark-300 dark:text-light-300">Account Number</h4>
                                        <div class="relative">
                                            <input type="text" class="w-full py-3 pl-4 pr-12 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 text-dark dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                                value="{{ $payment_mode->account_number }}" readonly>
                                            <button onclick="copyToClipboard(this.previousElementSibling)" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary/20 transition-all">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($payment_mode->swift_code))
                                    <div class="space-y-2">
                                        <h4 class="text-sm font-semibold text-dark-300 dark:text-light-300">Swift Code</h4>
                                        <div class="relative">
                                            <input type="text" class="w-full py-3 pl-4 pr-12 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 text-dark dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                                value="{{ $payment_mode->swift_code }}" readonly>
                                            <button onclick="copyToClipboard(this.previousElementSibling)" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary/20 transition-all">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>

                <!-- File Upload Section -->
                @if ($settings->deposit_option == 'auto' and $payment_mode->name == 'Bank Transfer' or
                    $settings->deposit_option == 'auto' and $payment_mode->defaultpay != 'yes')
                    <div class="mt-8 border-t border-light-200 dark:border-dark-200 pt-6">
                        <form method="post" action="{{ route('savedeposit') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-dark dark:text-white">Upload Payment proof after payment</label>
                                <div class="relative">
                                    <input type="file" name="proof" class="block w-full text-sm text-dark dark:text-white
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-primary-50 file:text-primary-700
                                        dark:file:bg-primary-900/30 dark:file:text-primary-300
                                        hover:file:bg-primary-100 dark:hover:file:bg-primary-800/40
                                        cursor-pointer
                                        focus:outline-none" required>
                                </div>
                                <p class="text-xs text-dark-300 dark:text-light-300">Accepted formats: JPG, PNG, PDF (Max. 5MB)</p>
                            </div>
                            <input type="hidden" name="amount" value="{{ $amount }}">
                            <input type="hidden" name="paymethd_method" value="{{ $payment_mode->name }}">

                            <div>
                                <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium flex items-center gap-2 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-primary/20">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Submit Payment</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                @if ($settings->deposit_option == 'manual' and
                    $payment_mode->name != 'Credit Card' and
                    $payment_mode->name != 'Paypal')
                    <div class="mt-8 border-t border-light-200 dark:border-dark-200 pt-6">
                        <form method="post" action="{{ route('savedeposit') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-dark dark:text-white">Upload Payment proof after payment</label>
                                <div class="relative">
                                    <input type="file" name="proof" class="block w-full text-sm text-dark dark:text-white
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-primary-50 file:text-primary-700
                                        dark:file:bg-primary-900/30 dark:file:text-primary-300
                                        hover:file:bg-primary-100 dark:hover:file:bg-primary-800/40
                                        cursor-pointer
                                        focus:outline-none" required>
                                </div>
                                <p class="text-xs text-dark-300 dark:text-light-300">Accepted formats: JPG, PNG, PDF (Max. 5MB)</p>
                            </div>
                            <input type="hidden" name="amount" value="{{ $amount }}">
                            <input type="hidden" name="paymethd_method" value="{{ $payment_mode->name }}">

                            <div>
                                <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium flex items-center gap-2 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-primary/20">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Submit Payment</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif

           {{-- Automatic Cryptopayment qrcode --}}
@if ($title == 'Complete Payment')
    <div class="text-center p-6 max-w-md mx-auto">
        <div class="p-6 rounded-xl bg-light-50 dark:bg-dark-100 shadow-lg border border-light-200 dark:border-dark-200 mb-6">
            <h3 class="text-xl font-bold text-dark dark:text-white mb-6">
                Send <span class="text-primary">{{ $amount }}</span> to the below address or scan the
                <span class="text-tertiary">{{ $coin }}</span> QR code to complete payment.
            </h3>

            <div class="rounded-xl bg-white dark:bg-dark-50 p-4 shadow-md border border-light-200 dark:border-dark-200 mb-6">
                <p class="font-mono text-sm text-dark dark:text-white break-all">{{ $p_address }}</p>
                <button onclick="copyToClipboard('{{ $p_address }}')" class="mt-2 px-4 py-2 rounded-lg bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-300 text-sm font-medium flex items-center gap-2 mx-auto hover:bg-primary-100 dark:hover:bg-primary-800/40 transition-all">
                    <i class="fas fa-copy"></i>
                    <span>Copy Address</span>
                </button>
            </div>

            <div class="flex justify-center mb-6">
                <div class="p-4 rounded-xl bg-white dark:bg-dark-50 border-2 border-accent dark:border-accent-600 shadow-lg relative">
                    <div class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-accent text-white flex items-center justify-center shadow-md">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <img width="220" height="220" alt="Payment QR code" src="{{ $p_qrcode }}" class="rounded-lg">
                </div>
            </div>

            <div class="p-4 rounded-xl bg-tertiary-50 dark:bg-tertiary-900/20 border border-tertiary-100 dark:border-tertiary-800/40">
                <p class="text-sm text-tertiary-700 dark:text-tertiary-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    You can exit this page after scanning and completing payment. The system will track your payment and update your account automatically.
                </p>
            </div>
        </div>

        <!-- Payment Steps -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-lg border border-light-200 dark:border-dark-200 overflow-hidden">
            <div class="p-4 bg-gradient-to-r from-primary-50 to-secondary-50 dark:from-dark-200 dark:to-dark-100 border-b border-light-200 dark:border-dark-300">
                <h4 class="font-semibold text-dark dark:text-white">How to Complete Your Payment</h4>
            </div>
            <div class="p-6">
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                            <span class="text-xs font-bold text-primary-600 dark:text-primary-400">1</span>
                        </div>
                        <p class="text-sm text-dark-300 dark:text-light-300">
                            Open your cryptocurrency wallet application
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                            <span class="text-xs font-bold text-primary-600 dark:text-primary-400">2</span>
                        </div>
                        <p class="text-sm text-dark-300 dark:text-light-300">
                            Select "Send" or "Pay" in your wallet app
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                            <span class="text-xs font-bold text-primary-600 dark:text-primary-400">3</span>
                        </div>
                        <p class="text-sm text-dark-300 dark:text-light-300">
                            Scan the QR code or paste the address shown above
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                            <span class="text-xs font-bold text-primary-600 dark:text-primary-400">4</span>
                        </div>
                        <p class="text-sm text-dark-300 dark:text-light-300">
                            Enter the exact amount: <span class="font-semibold text-dark dark:text-white">{{ $amount }}</span>
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                            <span class="text-xs font-bold text-primary-600 dark:text-primary-400">5</span>
                        </div>
                        <p class="text-sm text-dark-300 dark:text-light-300">
                            Confirm and send the transaction
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endif

<!-- Add required scripts to handle clipboard functionality -->
<script>
   function copyToClipboard(text) {
    // Check if 'text' is an element ID string
    const element = document.getElementById(text);
    if (element && element.value) {
        navigator.clipboard.writeText(element.value)
            .then(() => showToast('success', 'Copied!', 'Text copied to clipboard'))
            .catch(() => showToast('danger', 'Error!', 'Failed to copy text'));
    } else {
        showToast('danger', 'Error!', 'Element not found or has no value');
    }
}


    // Toast notification system
    function showToast(type, title, message) {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-3';
            document.body.appendChild(toastContainer);
        }

        const isDarkMode = document.documentElement.classList.contains('dark') ||
                          window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Determine toast class based on type and dark mode
        let toastClass = type;
        if (isDarkMode) {
            toastClass = 'dark-' + type;
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${toastClass}`;

        // Set icon based on type
        let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        // Build toast HTML
        toast.innerHTML = `
            <div class="icon">
                <i class="fas ${icon}"></i>
            </div>
            <div class="content">
                <div class="title">${title}</div>
                <div class="message">${message}</div>
            </div>
            <div class="close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </div>
            <div class="progress">
                <div class="progress-bar"></div>
            </div>
        `;

        // Add to container
        toastContainer.appendChild(toast);

        // Remove after animation completes
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>

<style>
    /* Animated floating elements */
    .floating-element {
        position: absolute;
        border-radius: 50%;
        opacity: 0.7;
        animation-duration: 15s;
        animation-iteration-count: infinite;
        animation-timing-function: ease-in-out;
    }

    /* Modern Toast Notifications */
    .toast {
        min-width: 300px;
        max-width: 400px;
        border-radius: 10px;
        margin-bottom: 1rem;
        padding: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateX(100%);
        animation: slideIn 0.3s forwards, fadeOut 0.5s 2.5s forwards;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .toast.success {
        background: linear-gradient(to right, #E8F5F0, #D1EBE1);
        border-left: 5px solid #4A9D7F;
        color: #2E614D;
    }

    .toast.success .icon {
        background-color: #A3D7C3;
        color: #3C7F65;
    }

    .toast.danger {
        background: linear-gradient(to right, #FFF0F0, #FFE0E0);
        border-left: 5px solid #FF6B6B;
        color: #D10000;
    }

    .toast.danger .icon {
        background-color: #FFC2C2;
        color: #FF3838;
    }

    .toast.dark-success {
        background: linear-gradient(to right, rgba(46, 97, 77, 0.8), rgba(33, 68, 53, 0.6));
        border-left: 5px solid #4A9D7F;
        color: #ffffff;
    }

    .toast.dark-success .icon {
        background-color: rgba(74, 157, 127, 0.3);
        color: #76C3A5;
    }

    .toast.dark-danger {
        background: linear-gradient(to right, rgba(209, 0, 0, 0.8), rgba(158, 0, 0, 0.6));
        border-left: 5px solid #FF6B6B;
        color: #ffffff;
    }

    .toast.dark-danger .icon {
        background-color: rgba(255, 56, 56, 0.3);
        color: #FFA3A3;
    }

    .toast .icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    .toast .content {
        flex-grow: 1;
    }

    .toast .title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .toast .close {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.15s ease;
    }

    .toast .close:hover {
        opacity: 1;
    }

    .toast .progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
        background: rgba(255, 255, 255, 0.3);
    }

    .toast .progress-bar {
        height: 100%;
        background: currentColor;
        animation: progress 3s linear forwards;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }

    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }

    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>

<!-- Add required FontAwesome CDN link -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
