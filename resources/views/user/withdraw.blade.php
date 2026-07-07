@extends('layouts.dash')
@section('title', $title)
@section('content')

<!-- Modern Exchange-Style Header -->
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
                    <div class="flex items-center">
                        <a href="#" class="mr-3 bg-white/80 dark:bg-dark-100/80 rounded-full p-2 text-dark dark:text-white hover:bg-white dark:hover:bg-dark-50 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path d="M9.57 5.92999L3.5 12L9.57 18.07" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20.5 12H3.67" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold text-dark dark:text-white">Withdrawal Details</h1>
                    </div>
                    <p class="mt-2 text-base text-dark-300 dark:text-light-300">Complete your withdrawal request</p>
                </div>
                <div>
                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-white/80 dark:bg-dark-50/80 backdrop-blur-sm border border-white/20 dark:border-dark-200/30 shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-secondary text-white">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                <path d="M16.44 8.8999C20.04 9.2099 21.51 11.0599 21.51 15.1099V15.2399C21.51 19.7099 19.72 21.4999 15.25 21.4999H8.73998C4.26998 21.4999 2.47998 19.7099 2.47998 15.2399V15.1099C2.47998 11.0899 3.92998 9.2399 7.46998 8.9099" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 15.0001V3.62012" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.35 5.8501L12 2.5001L8.65002 5.8501" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-dark-300 dark:text-light-300">{{ $payment_mode }} Withdrawal</p>
                            <p class="text-xl font-bold text-dark dark:text-white">{{ $settings->currency }} <span id="amount-display">0.00</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>

<!-- Alert Messages -->
<x-danger-alert/>
<x-success-alert/>

<!-- Status Alert for Session Messages -->
<div id="status-alert" class="hidden"></div>

<!-- Main Content -->
<div class="max-w-4xl mx-auto">
    <!-- Withdrawal Form Card -->
    <div class="bg-white dark:bg-dark-50 rounded-2xl shadow-xl border border-light-200 dark:border-dark-200/50 overflow-hidden">
        <!-- Card Header -->
        <div class="border-b border-light-200 dark:border-dark-200/50">
            <div class="flex items-center px-6 py-4">
                <!-- Payment Method Chip -->
                <div class="flex items-center px-4 py-2 bg-secondary-50 dark:bg-secondary-900/30 rounded-full">
                    @if(in_array($payment_mode, ['Bitcoin', 'Ethereum', 'USDT', 'Litecoin', 'BUSD']))
                        <div class="w-6 h-6 rounded-full bg-{{ strtolower($payment_mode) == 'bitcoin' ? 'accent' : (strtolower($payment_mode) == 'ethereum' ? 'tertiary' : 'primary') }}-100 dark:bg-{{ strtolower($payment_mode) == 'bitcoin' ? 'accent' : (strtolower($payment_mode) == 'ethereum' ? 'tertiary' : 'primary') }}-900/30 flex items-center justify-center mr-2">
                            <svg class="w-4 h-4 text-{{ strtolower($payment_mode) == 'bitcoin' ? 'accent' : (strtolower($payment_mode) == 'ethereum' ? 'tertiary' : 'primary') }}-600 dark:text-{{ strtolower($payment_mode) == 'bitcoin' ? 'accent' : (strtolower($payment_mode) == 'ethereum' ? 'tertiary' : 'primary') }}-400" viewBox="0 0 24 24" fill="none">
                                <path d="M23 12C23 18.0751 18.0751 23 12 23C5.92487 23 1 18.0751 1 12C1 5.92487 5.92487 1 12 1C18.0751 1 23 5.92487 23 12Z" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M16.75 8H11.75C9.95507 8 8.5 9.45507 8.5 11.25C8.5 13.0449 9.95507 14.5 11.75 14.5H12.25C14.0449 14.5 15.5 15.9551 15.5 17.75C15.5 19.5449 14.0449 21 12.25 21H7.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M12 21L12 23" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M12 1L12 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M12 8L12 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M12 24L12 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                    @else
                        <div class="w-6 h-6 rounded-full bg-secondary-100 dark:bg-secondary-900/30 flex items-center justify-center mr-2">
                            <svg class="w-4 h-4 text-secondary-600 dark:text-secondary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M18.04 13.55C17.62 13.96 17.38 14.55 17.44 15.18C17.53 16.26 18.52 17.05 19.6 17.05H21.5V18.24C21.5 20.31 19.81 22 17.74 22H6.26C4.19 22 2.5 20.31 2.5 18.24V11.51C2.5 9.44 4.19 7.75 6.26 7.75H17.74C19.81 7.75 21.5 9.44 21.5 11.51V12.95H19.48C18.92 12.95 18.41 13.17 18.04 13.55Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.85 4.97V7.75H6.26C4.19 7.75 2.5 9.44 2.5 11.51V6.44C2.5 5.08 3.6 3.97 4.97 3.97H13.88C14.43 3.97 14.85 4.39 14.85 4.97Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22.5588 13.9702V16.0302C22.5588 16.5802 22.1188 17.0302 21.5588 17.0502H19.5988C18.5188 17.0502 17.5288 16.2602 17.4388 15.1802C17.3788 14.5502 17.6188 13.9602 18.0388 13.5502C18.4088 13.1702 18.9188 12.9502 19.4788 12.9502H21.5588C22.1188 12.9702 22.5588 13.4202 22.5588 13.9702Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 12H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    @endif
                    <span class="text-sm font-medium text-dark dark:text-white">{{ $payment_mode }}</span>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            @if ($payment_mode == "USDT" and $settings->auto_merchant_option == 'Binance' and $settings->withdrawal_option == 'auto')
                <livewire:user.crypto-withdaw :payment_mode="$payment_mode"/>
            @else
                <!-- Exchange-Style Withdrawal Form -->
                <form action="{{route('completewithdrawal')}}" method="post" id="withdrawal-form" class="space-y-6">
                    @csrf

                    <!-- Withdrawal Amount -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="amount" class="text-sm font-medium text-dark dark:text-white">Amount to withdraw</label>

                            <div class="flex items-center">
                                <span class="text-xs text-dark-300 dark:text-light-300 mr-2">Available:</span>
                                <span class="text-sm font-medium text-dark dark:text-white">{{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2, '.', ',') }}</span>
                                <button type="button" id="max-amount" class="ml-2 px-2 py-0.5 text-xs font-medium rounded-md bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-300 hover:bg-primary-100 dark:hover:bg-primary-800/40 transition-colors">
                                    MAX
                                </button>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-dark-300 dark:text-light-300">{{ $settings->currency }}</span>
                            </div>
                            <input class="block w-full pl-10 pr-20 py-3 text-lg rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all"
                                   placeholder="0.00"
                                   type="number"
                                   name="amount"
                                   id="amount"
                                   required
                                   step="0.01"
                                   min="0"
                                   oninput="updateAmountDisplay(this.value)">

                            @if (isset($min_withdrawal) && isset($max_withdrawal))
                                <div class="absolute inset-y-0 right-3 flex items-center text-xs text-dark-300 dark:text-light-300">
                                    Min: {{ $settings->currency }}{{ number_format($min_withdrawal, 2) }}
                                </div>
                            @endif
                        </div>

                        <!-- Withdrawal Fee -->
                        <div class="flex justify-between text-xs">
                            <span class="text-dark-300 dark:text-light-300">Fee:</span>
                            <span class="text-dark-300 dark:text-light-300">
                                @if (isset($fee_percentage))
                                    {{ $fee_percentage }}% (~{{ $settings->currency }}<span id="fee-amount">0.00</span>)
                                @else
                                    Varies by amount
                                @endif
                            </span>
                        </div>

                        <!-- You Will Receive -->
                        <div class="flex justify-between text-xs">
                            <span class="text-dark-300 dark:text-light-300">You will receive:</span>
                            <span class="font-medium text-dark dark:text-white">{{ $settings->currency }}<span id="receive-amount">0.00</span></span>
                        </div>

                        <!-- Receiving Into (destination address) -->
                        <div class="flex justify-between gap-3 text-xs pt-2 mt-2 border-t border-light-200 dark:border-dark-200">
                            <span class="text-dark-300 dark:text-light-300 shrink-0">Receiving into:</span>
                            <span class="font-medium text-dark dark:text-white text-right break-all font-mono" id="receiving-address">your {{ $payment_mode }} address</span>
                        </div>
                    </div>

                    <input value="{{$payment_mode}}" type="hidden" name="method">

                    <!-- Estimated Processing Time -->
                    <div class="flex items-center p-4 rounded-xl bg-light-50 dark:bg-dark-100 border border-light-200 dark:border-dark-200 gap-3">
                        <div class="w-10 h-10 rounded-full bg-tertiary-50 dark:bg-tertiary-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-tertiary-600 dark:text-tertiary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M15.71 15.93L12.61 14.13C12.07 13.83 11.63 13.12 11.63 12.49V7.97" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-dark dark:text-white">Estimated processing time</h3>
                            <p class="text-xs text-dark-300 dark:text-light-300">Your withdrawal will be processed within 24-48 hours</p>
                        </div>
                    </div>

                    <!-- OTP Verification (if enabled) -->
                    @if (Auth::user()->sendotpemail == 'Yes')
                        <div class="p-5 rounded-xl bg-white dark:bg-dark-100 border border-light-200 dark:border-dark-200">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium text-dark dark:text-white"> Withdrawal Code</label>
                                <a href="{{route('getotp')}}" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-primary text-white hover:bg-primary-600 transition-colors flex items-center gap-1">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                        <path d="M12 16V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M9 10.5L12 7.5L15 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                Generate Withdrawal Code
                                </a>
                            </div>

                            <div class="relative">
                                <input class="block w-full pl-4 pr-4 py-3 text-lg rounded-xl bg-light-100 dark:bg-dark-100/80 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all"
                                       placeholder="Enter Withdrawal Code"
                                       type="text"
                                       name="otpcode"
                                       required>
                            </div>
                            <p class="mt-2 text-xs text-dark-300 dark:text-light-300">  A Withdrawal Code is required to complete this transaction. Please contact support via live chat or email us at {{$settings->contact_email}} for assistance. </p>
                        </div>
                    @endif

                    <!-- Withdrawal Address/Details Section -->
                    @if (!$default or $payment_mode == "BUSD")
                        @if ($methodtype == 'crypto')
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-dark dark:text-white">{{ $payment_mode }} Wallet Address</label>
                                <div class="relative">
                                    <input class="block w-full pl-4 pr-4 py-3 text-md rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white font-mono transition-all"
                                           placeholder="Enter valid {{ $payment_mode }} address"
                                           type="text"
                                           name="details"
                                           required>
                                </div>
                                <div class="p-3 rounded-lg bg-accent-50 dark:bg-accent-900/20 border border-accent-100 dark:border-accent-800/30">
                                    <div class="flex gap-2">
                                        <svg class="w-5 h-5 text-accent-600 dark:text-accent-400 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M11.9946 16H12.0036" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <p class="text-xs text-accent-700 dark:text-accent-300">
                                            Make sure to enter the correct {{ $payment_mode }} address. Transfers sent to incorrect addresses cannot be recovered.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-dark dark:text-white">{{ $payment_mode }} Details</label>
                                <textarea class="block w-full p-4 text-md rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all"
                                          rows="4"
                                          name="details"
                                          placeholder="BankName: Name, Account Number: Number, Account name: Name, Swift Code: Code"
                                          required></textarea>
                                <div class="p-3 rounded-lg bg-secondary-50 dark:bg-secondary-900/20 border border-secondary-100 dark:border-secondary-800/30">
                                    <div class="flex gap-2">
                                        <svg class="w-5 h-5 text-secondary-600 dark:text-secondary-400 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M11.9946 16H12.0036" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div>
                                            <p class="text-xs text-secondary-700 dark:text-secondary-300">
                                                Please enter your complete bank details separated by commas in the format:
                                            </p>
                                            <p class="text-xs font-medium text-secondary-700 dark:text-secondary-300 mt-1">
                                                BankName: Name, Account Number: Number, Account name: Name, Swift Code: Code
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button class="w-full py-4 px-4 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium flex items-center justify-center gap-2 transform transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-primary/20" type='submit'>
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path d="M9.5 13.7502C9.5 14.7202 10.25 15.5002 11.17 15.5002H13.05C13.85 15.5002 14.5 14.8202 14.5 13.9702C14.5 13.0602 14.1 12.7302 13.51 12.5202L10.5 11.4702C9.91 11.2602 9.51 10.9402 9.51 10.0202C9.51 9.18023 10.16 8.49023 10.96 8.49023H12.84C13.76 8.49023 14.51 9.27023 14.51 10.2402" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17 3V7H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22 2L17 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Complete Withdrawal Request</span>
                        </button>
                        <p class="mt-3 text-center text-xs text-dark-300 dark:text-light-300">
                            By proceeding, you confirm that the provided information is correct
                        </p>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Exchange-style Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Important Notes -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
            <div class="p-4 border-b border-light-200 dark:border-dark-200/50 flex items-center">
                <svg class="w-5 h-5 text-accent mr-2" viewBox="0 0 24 24" fill="none">
                    <path d="M10.49 2.23006L5.50003 4.10004C4.35003 4.53004 3.41003 5.89004 3.41003 7.12004V14.55C3.41003 15.73 4.19005 17.28 5.14005 17.99L9.44003 21.2C10.85 22.26 13.17 22.26 14.58 21.2L18.88 17.99C19.83 17.28 20.61 15.73 20.61 14.55V7.12004C20.61 5.89004 19.67 4.53004 18.52 4.10004L13.53 2.23006C12.68 1.92006 11.32 1.92006 10.49 2.23006Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M12 15.5C11.59 15.5 11.25 15.16 11.25 14.75V14.75C11.25 14.34 11.59 14 12 14C12.41 14 12.75 14.34 12.75 14.75C12.75 15.16 12.41 15.5 12 15.5Z" fill="currentColor"/>
                    <path d="M12 12C11.59 12 11.25 11.66 11.25 11.25V8.25C11.25 7.84 11.59 7.5 12 7.5C12.41 7.5 12.75 7.84 12.75 8.25V11.25C12.75 11.66 12.41 12 12 12Z" fill="currentColor"/>
                </svg>
                <h3 class="text-base font-medium text-dark dark:text-white">Important Information</h3>
            </div>
            <div class="p-5">
                <ul class="space-y-3">
                    <li class="flex">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-secondary-50 dark:bg-secondary-900/30 flex items-center justify-center mr-3 mt-0.5">
                            <svg class="w-3 h-3 text-secondary-600 dark:text-secondary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M6 10L10.2 14.2L18 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <p class="text-xs text-dark-300 dark:text-light-300">Withdrawal requests are typically processed within 24-48 hours.</p>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-secondary-50 dark:bg-secondary-900/30 flex items-center justify-center mr-3 mt-0.5">
                            <svg class="w-3 h-3 text-secondary-600 dark:text-secondary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M6 10L10.2 14.2L18 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <p class="text-xs text-dark-300 dark:text-light-300">Minimum withdrawal amount varies by payment method.</p>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-secondary-50 dark:bg-secondary-900/30 flex items-center justify-center mr-3 mt-0.5">
                            <svg class="w-3 h-3 text-secondary-600 dark:text-secondary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M6 10L10.2 14.2L18 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <p class="text-xs text-dark-300 dark:text-light-300">Withdrawal fees are automatically calculated and deducted from your withdrawal amount.</p>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-secondary-50 dark:bg-secondary-900/30 flex items-center justify-center mr-3 mt-0.5">
                            <svg class="w-3 h-3 text-secondary-600 dark:text-secondary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M6 10L10.2 14.2L18 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <p class="text-xs text-dark-300 dark:text-light-300">Once submitted, withdrawal requests cannot be canceled.</p>
                    </li>
                </ul>

                <div class="mt-5 pt-4 border-t border-light-200 dark:border-dark-200/50">
                    <div class="flex items-center p-3 rounded-lg bg-tertiary-50 dark:bg-tertiary-900/20 border border-tertiary-100 dark:border-tertiary-800/30">
                        <svg class="w-5 h-5 text-tertiary-600 dark:text-tertiary-400 mr-3 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                            <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.9946 16H12.0036" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="text-xs text-tertiary-700 dark:text-tertiary-300">
                            Need help with your withdrawal? Contact our support team via the help center.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawal Verification -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
            <div class="p-4 border-b border-light-200 dark:border-dark-200/50 flex items-center">
                <svg class="w-5 h-5 text-primary mr-2" viewBox="0 0 24 24" fill="none">
                    <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M8.9 12L10.36 13.46L14.0001 10.87" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.0495 6H13.9495C15.9495 6 15.9495 5 15.9495 4C15.9495 2 14.9495 2 13.9495 2H10.0495C9.04953 2 9.04953 3 9.04953 4C9.04953 6 10.0495 6 10.0495 6Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 4.02002C19.33 4.20002 21 5.43002 21 10V15" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 15V10C3 5.97 4.36 4.57 7 4.18" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 17C14.2091 17 16 15.2091 16 13C16 10.7909 14.2091 9 12 9C9.79086 9 8 10.7909 8 13C8 15.2091 9.79086 17 12 17Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h3 class="text-base font-medium text-dark dark:text-white">Verification Details</h3>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <!-- Verification Status -->
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" fill="currentColor" fill-opacity="0.2"/>
                                    <path d="M8.9 12L10.36 13.46L14.0001 10.87" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-dark dark:text-white">Two-Factor Authentication</h4>
                                <p class="text-xs text-dark-300 dark:text-light-300 mt-1">Email OTP verification</p>
                            </div>
                        </div>
                        <div>
                            @if (Auth::user()->sendotpemail == 'Yes')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">Enabled</span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400">Disabled</span>
                            @endif
                        </div>
                    </div>

                    <!-- Withdrawal Limit -->
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" viewBox="0 0 24 24" fill="none">
                                    <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" fill="currentColor" fill-opacity="0.2"/>
                                    <path d="M15.71 15.93L12.61 14.13C12.07 13.83 11.63 13.12 11.63 12.49V7.97" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-dark dark:text-white">24h Withdrawal Limit</h4>
                                <p class="text-xs text-dark-300 dark:text-light-300 mt-1">Maximum daily withdrawal</p>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-dark dark:text-white">
                                {{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2, '.', ',') }}
                            </span>
                        </div>
                    </div>

                    <!-- Default Method -->
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" viewBox="0 0 24 24" fill="none">
                                    <path d="M18.04 13.55C17.62 13.96 17.38 14.55 17.44 15.18C17.53 16.26 18.52 17.05 19.6 17.05H21.5V18.24C21.5 20.31 19.81 22 17.74 22H6.26C4.19 22 2.5 20.31 2.5 18.24V11.51C2.5 9.44 4.19 7.75 6.26 7.75H17.74C19.81 7.75 21.5 9.44 21.5 11.51V12.95H19.48C18.92 12.95 18.41 13.17 18.04 13.55Z" fill="currentColor" fill-opacity="0.2"/>
                                    <path d="M14.85 4.97V7.75H6.26C4.19 7.75 2.5 9.44 2.5 11.51V6.44C2.5 5.08 3.6 3.97 4.97 3.97H13.88C14.43 3.97 14.85 4.39 14.85 4.97Z" fill="currentColor" fill-opacity="0.2"/>
                                    <path d="M22.5588 13.9702V16.0302C22.5588 16.5802 22.1188 17.0302 21.5588 17.0502H19.5988C18.5188 17.0502 17.5288 16.2602 17.4388 15.1802C17.3788 14.5502 17.6188 13.9602 18.0388 13.5502C18.4088 13.1702 18.9188 12.9502 19.4788 12.9502H21.5588C22.1188 12.9702 22.5588 13.4202 22.5588 13.9702Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-dark dark:text-white">Default Method</h4>
                                <p class="text-xs text-dark-300 dark:text-light-300 mt-1">Saved withdrawal method</p>
                            </div>
                        </div>
                        <div>
                            @if ($default)
                            <span class="text-xs font-medium text-dark dark:text-white">{{ $payment_mode }}</span>
                            @else
                            <span class="text-xs text-dark-300 dark:text-light-300">Not set</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <a href="#" class="text-xs text-primary dark:text-primary-400 font-medium flex items-center hover:underline">
                        <span>Manage security settings</span>
                        <svg class="w-3 h-3 ml-1" viewBox="0 0 24 24" fill="none">
                            <path d="M8.91003 19.9201L15.43 13.4001C16.2 12.6301 16.2 11.3701 15.43 10.6001L8.91003 4.08008" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for functionality - Final Fixed version -->
<script>
    // Live-update the "Receiving into" destination address as the user types.
    document.addEventListener('DOMContentLoaded', function () {
        const detailsField = document.querySelector('[name="details"]');
        const receivingAddress = document.getElementById('receiving-address');
        const placeholder = 'your {{ $payment_mode }} address';
        if (detailsField && receivingAddress) {
            const sync = function () {
                const val = (detailsField.value || '').trim();
                receivingAddress.textContent = val.length ? val : placeholder;
            };
            detailsField.addEventListener('input', sync);
            sync();
        }
    });

    // Define updateAmountDisplay in the global scope
    function updateAmountDisplay(value) {
        const amountDisplay = document.getElementById('amount-display');
        const feeAmount = document.getElementById('fee-amount');
        const receiveAmount = document.getElementById('receive-amount');

        // Only proceed if all elements exist
        if (amountDisplay && feeAmount && receiveAmount) {
            const amount = parseFloat(value) || 0;

            // Update amount display
            amountDisplay.textContent = amount.toFixed(2);

            // Calculate and update fee using the fee percentage from PHP template
            @if (isset($fee_percentage))
                const feePercentage = {{ $fee_percentage }};
            @else
                const feePercentage = 1; // Default to 1% if not set
            @endif

            const fee = (amount * feePercentage / 100).toFixed(2);
            feeAmount.textContent = fee;

            // Calculate and update receive amount
            const receiveAmount = (amount - parseFloat(fee)).toFixed(2);
            document.getElementById('receive-amount').textContent = receiveAmount;

            // Validate against available balance
            const availableBalance = {{ Auth::user()->account_bal ?? 0 }};
            const totalCost = amount + parseFloat(fee);

            // Get the submit button
            const submitButton = document.querySelector('#withdrawal-form button[type="submit"]');

            if (totalCost > availableBalance) {
                // Show error
                const errorMsg = document.createElement('div');
                errorMsg.id = 'amount-error';
                errorMsg.className = 'text-sm text-red-600 dark:text-red-400 mt-1';
                errorMsg.textContent = 'Amount + fee exceeds your available balance';

                // Remove existing error if present
                const existingError = document.getElementById('amount-error');
                if (existingError) {
                    existingError.remove();
                }

                // Add error after the amount input
                document.getElementById('amount').parentNode.after(errorMsg);

                // Disable submit button
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                }
            } else {
                // Remove error if amount is valid
                const existingError = document.getElementById('amount-error');
                if (existingError) {
                    existingError.remove();
                }

                // Enable submit button
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Get required elements
        const amountInput = document.getElementById('amount');
        const maxButton = document.getElementById('max-amount');

        // Add event listener to amount input (instead of using inline oninput)
        if (amountInput) {
            // Remove any inline oninput attribute
            amountInput.removeAttribute('oninput');

            // Add event listener
            amountInput.addEventListener('input', function() {
                updateAmountDisplay(this.value);
            });

            // Initialize with current value (if any)
            updateAmountDisplay(amountInput.value);
        }

        // Handle "MAX" button click if it exists
        if (maxButton) {
            maxButton.addEventListener('click', function() {
                // This would be the user's available balance
                const maxBalance = {{ Auth::user()->account_bal ?? 0 }};

                // Calculate maximum withdrawable amount accounting for fees
                @if (isset($fee_percentage) && $fee_percentage > 0)
                    // Formula: max_withdraw = account_balance / (1 + fee_percentage/100)
                    // This ensures that amount + fee <= account_balance
                    const maxWithdrawable = (maxBalance / (1 + {{ $fee_percentage }}/100)).toFixed(2);
                @else
                    const maxWithdrawable = maxBalance;
                @endif

                if (amountInput) {
                    // Set the input value to max withdrawable amount
                    amountInput.value = maxWithdrawable;

                    // Update displays
                    updateAmountDisplay(maxWithdrawable);
                }
            });
        }
    });

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
        }, 5000);
    }

    // Handle session status if it exists
    @if (session('status'))
        // Wrap in a setTimeout to ensure DOM is fully loaded
        setTimeout(() => {
            showToast('danger', 'Error', '{{ session('status') }}');
        }, 300);
    @endif
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

    .elem-1 {
        width: 100px;
        height: 100px;
        top: 10%;
        left: 5%;
        animation-name: float1;
    }

    .elem-2 {
        width: 70px;
        height: 70px;
        top: 20%;
        right: 10%;
        animation-name: float2;
    }

    .elem-3 {
        width: 50px;
        height: 50px;
        bottom: 20%;
        left: 15%;
        animation-name: float3;
    }

    .elem-4 {
        width: 80px;
        height: 80px;
        bottom: 10%;
        right: 5%;
        animation-name: float4;
    }

    @keyframes float1 {
        0% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(15px, 15px) rotate(90deg); }
        50% { transform: translate(0, 30px) rotate(180deg); }
        75% { transform: translate(-15px, 15px) rotate(270deg); }
        100% { transform: translate(0, 0) rotate(360deg); }
    }

    @keyframes float2 {
        0% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(-20px, 10px) rotate(-90deg); }
        50% { transform: translate(0, 20px) rotate(-180deg); }
        75% { transform: translate(20px, 10px) rotate(-270deg); }
        100% { transform: translate(0, 0) rotate(-360deg); }
    }

    @keyframes float3 {
        0% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(15px, -15px) rotate(120deg); }
        66% { transform: translate(-15px, -15px) rotate(240deg); }
        100% { transform: translate(0, 0) rotate(360deg); }
    }

    @keyframes float4 {
        0% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(-20px, -10px) rotate(-120deg); }
        66% { transform: translate(20px, -20px) rotate(-240deg); }
        100% { transform: translate(0, 0) rotate(-360deg); }
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
        animation: slideIn 0.3s forwards, fadeOut 0.5s 4.5s forwards;
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
        animation: progress 5s linear forwards;
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
