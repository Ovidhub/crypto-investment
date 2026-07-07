@extends('layouts.dash')
@section('title', $title)
@section('content')

<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 px-1">
        <div>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20 shadow-inner">
                    <svg class="w-6 h-6 text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.04 13.55C17.62 13.96 17.38 14.55 17.44 15.18C17.53 16.26 18.52 17.05 19.6 17.05H21.5V18.24C21.5 20.31 19.81 22 17.74 22H6.26C4.19 22 2.5 20.31 2.5 18.24V11.51C2.5 9.44 4.19 7.75 6.26 7.75H17.74C19.81 7.75 21.5 9.44 21.5 11.51V12.95H19.48C18.92 12.95 18.41 13.17 18.04 13.55Z" fill="currentColor" fill-opacity="0.2"/>
                        <path d="M14.85 4.97V7.75H6.26C4.19 7.75 2.5 9.44 2.5 11.51V6.44C2.5 5.08 3.6 3.97 4.97 3.97H13.88C14.43 3.97 14.85 4.39 14.85 4.97Z" fill="currentColor" fill-opacity="0.2"/>
                        <path d="M22.5588 13.9702V16.0302C22.5588 16.5802 22.1188 17.0302 21.5588 17.0502H19.5988C18.5188 17.0502 17.5288 16.2602 17.4388 15.1802C17.3788 14.5502 17.6188 13.9602 18.0388 13.5502C18.4088 13.1702 18.9188 12.9502 19.4788 12.9502H21.5588C22.1188 12.9702 22.5588 13.4202 22.5588 13.9702Z" fill="currentColor"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Deposits</h1>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Institutional Funding Terminal</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('accounthistory') }}"
               class="inline-flex items-center justify-center h-10 px-5 text-[11px] font-black rounded-xl
                      bg-slate-100 dark:bg-slate-800
                      text-slate-900 dark:text-white
                      hover:bg-slate-200 dark:hover:bg-slate-700
                      transition-all duration-200
                      uppercase tracking-widest
                      focus:outline-none focus:ring-2 focus:ring-slate-300 dark:focus:ring-slate-600">
                Deposit History
            </a>
        
            <a href="{{ route('deposits') }}"
               class="inline-flex items-center justify-center h-10 px-5 text-[11px] font-black rounded-xl
                      bg-primary text-white
                      hover:bg-primary-600
                      transition-all duration-200
                      uppercase tracking-widest
                      shadow-lg shadow-primary/20
                      focus:outline-none focus:ring-2 focus:ring-primary/50">
                New Deposit
            </a>
        </div>

    </div>
</div>

<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>
<div class="space-y-4 mb-6">
    <x-danger-alert />
    <x-success-alert />
</div>

<div class="bg-white dark:bg-[#0f172a] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden relative">
    <div class="absolute top-0 right-0 p-1 opacity-10">
        <i data-lucide="layers" class="w-20 h-20 text-slate-500"></i>
    </div>

    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
        <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
            <span class="h-4 w-1 bg-primary rounded-full"></span>
            Select Deposit Method
        </h2>
    </div>

    <div class="p-4 bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl w-full md:w-auto">
            <button class="flex-1 md:flex-none px-4 py-2 text-[10px] font-black rounded-lg bg-white dark:bg-slate-700 text-primary shadow-sm uppercase tracking-widest">
                All Methods
            </button>
            <!--<button class="flex-1 md:flex-none px-4 py-2 text-[10px] font-black rounded-lg text-slate-500 dark:text-slate-400 hover:text-primary transition-colors uppercase tracking-widest">-->
            <!--    Crypto-->
            <!--</button>-->
            <!--<button class="flex-1 md:flex-none px-4 py-2 text-[10px] font-black rounded-lg text-slate-500 dark:text-slate-400 hover:text-primary transition-colors uppercase tracking-widest">-->
            <!--    Bank Transfer-->
            <!--</button>-->
        </div>
        <div class="relative w-full md:w-72 group">
            <input type="text" id="methodSearch" placeholder="Search payment methods..." class="w-full pl-10 pr-4 py-2.5 text-xs font-medium rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none text-slate-900 dark:text-white transition-all">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 group-focus-within:text-primary transition-colors" viewBox="0 0 24 24" fill="none">
                <path d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 22L20 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-900/80 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                    <th class="px-6 py-4 text-left border-b border-slate-100 dark:border-slate-800">Assets / Network</th>
                    <th class="px-6 py-4 text-right border-b border-slate-100 dark:border-slate-800">Limits</th>
                    <th class="px-6 py-4 text-right border-b border-slate-100 dark:border-slate-800">Operation</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                @forelse ($dmethods as $method)
                <tr class="group hover:bg-slate-50 dark:hover:bg-primary/5 transition-all">
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                @if (!empty($method->img_url))
                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-2 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                        <img src="{{ $method->img_url }}" alt="{{ $method->name }}" class="h-full w-full object-contain">
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 border border-primary/20 p-2 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-primary" viewBox="0 0 24 24" fill="none">
                                            <path d="M9.5 13.7502C9.5 14.7202 10.25 15.5002 11.17 15.5002H13.05C13.85 15.5002 14.5 14.8202 14.5 13.9702C14.5 13.0602 14.1 12.7302 13.51 12.5202L10.5 11.4702C9.91 11.2602 9.51 10.9402 9.51 10.0202C9.51 9.18023 10.16 8.49023 10.96 8.49023H12.84C13.76 8.49023 14.51 9.27023 14.51 10.2402" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                @endif
                                <span class="absolute -top-1 -right-1 h-3 w-3 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $method->name }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Instant Settlement</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <div class="inline-block px-3 py-1 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50">
                            <span class="text-[9px] font-black text-slate-400 uppercase mr-2 tracking-tighter">Min Threshold</span>
                            <span class="text-sm font-black text-slate-900 dark:text-white font-mono">{{ $settings->currency }}{{ number_format($method->minimum, 2) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <button onclick="checkpamethd('{{ $method->id }}')" class="h-9 px-6 text-[10px] font-black rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-primary dark:hover:bg-primary dark:hover:text-white transition-all uppercase tracking-widest group/btn">
                            Deposit 
                            <i data-lucide="arrow-right" class="inline h-3 w-3 ml-1 group-hover/btn:translate-x-1 transition-transform"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mb-4 border border-amber-100 dark:border-amber-800">
                                <i data-lucide="alert-triangle" class="w-8 h-8 text-amber-600"></i>
                            </div>
                            <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-2">No Payment Methods Available</h4>
                            <p class="text-xs text-slate-500 uppercase tracking-tight max-w-xs leading-relaxed">
                                No payment methods are available at the moment. Please contact our support team for assistance.
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8 relative">
    <div class="bg-white dark:bg-[#0f172a] rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="h-1.5 w-full bg-gradient-to-r from-primary via-secondary to-primary"></div>
        
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/30 flex justify-between items-center">
            <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center">
                <i data-lucide="calculator" class="w-5 h-5 mr-3 text-primary"></i>
                Deposit Details
            </h2>
            <div class="flex gap-1">
                <span class="h-2 w-2 rounded-full bg-slate-200 dark:bg-slate-700"></span>
                <span class="h-2 w-2 rounded-full bg-slate-200 dark:bg-slate-700"></span>
            </div>
        </div>

        <div class="p-8">
            <form action="javascript:;" method="post" id="submitpaymentform" class="max-w-xl mx-auto space-y-8">
                @csrf

                <div class="space-y-4">
                    <div class="flex items-center justify-between px-1">
                        <label for="amount" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Amount to deposit</label>
                        <span class="text-[9px] font-bold text-primary uppercase">USD Settlement</span>
                    </div>

                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <span class="text-xl font-black text-slate-300 dark:text-slate-600 group-focus-within:text-primary transition-colors">{{ $settings->currency }}</span>
                        </div>
                        <input class="block w-full pl-14 pr-12 py-5 text-2xl font-black rounded-2xl bg-slate-50 dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 focus:ring-4 focus:ring-primary/10 focus:border-primary text-slate-900 dark:text-white transition-all placeholder:text-slate-300 dark:placeholder:text-slate-700 font-mono"
                               placeholder="0.00"
                               type="number"
                               name="amount"
                               id="amount"
                               min="{{ $method->minimum ?? 0 }}"
                               required>
                    </div>
                </div>

                <input type="hidden" name="payment_method" id="paymethod" value="">

                <div id="selected-method" class="hidden p-5 rounded-2xl bg-primary/5 border border-primary/20 flex items-center justify-between group animate-in fade-in slide-in-from-top-2">
                    <div class="flex items-center gap-4">
                        <div id="method-icon" class="w-12 h-12 rounded-xl bg-white dark:bg-slate-800 border border-primary/20 p-2.5 flex items-center justify-center shadow-sm"></div>
                        <div>
                            <p class="text-[10px] font-black text-primary uppercase tracking-widest">Active Channel</p>
                            <p id="method-name" class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tighter">-</p>
                        </div>
                    </div>

                    <button type="button" onclick="resetMethod()" class="h-8 px-4 text-[10px] font-black text-slate-500 hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all uppercase tracking-widest border border-slate-200 dark:border-slate-700">
                        Change
                    </button>
                </div>

                <div class="space-y-4 pt-2">
                    <button type="submit" id="submit-btn" class="w-full py-5 px-6 rounded-2xl bg-primary text-white text-xs font-black uppercase tracking-[0.2em] flex items-center justify-center gap-3 transform transition-all duration-300 hover:scale-[1.02] shadow-xl shadow-primary/30 active:scale-95 disabled:opacity-30 disabled:grayscale disabled:cursor-not-allowed group" disabled>
                        <span>Complete Deposit</span>
                        <i data-lucide="chevron-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                    <div class="flex items-center justify-center gap-2">
                        <i data-lucide="shield-check" class="h-3 w-3 text-emerald-500"></i>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Secure Encrypted Gateway
                        </p>
                    </div>
                </div>

                <input type="hidden" id="lastchosen" value="0">
            </form>
        </div>
    </div>
</div>

<!-- Exchange-style Information Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <!-- Deposit Process -->
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
        <div class="p-4 border-b border-light-200 dark:border-dark-200/50 flex items-center">
            <svg class="w-5 h-5 text-primary mr-2" viewBox="0 0 24 24" fill="none">
                <path d="M10.75 2.44995C11.44 1.85995 12.57 1.85995 13.27 2.44995L14.85 3.81001C15.15 4.07001 15.71 4.28002 16.11 4.28002H17.81C18.87 4.28002 19.74 5.14996 19.74 6.20996V7.91003C19.74 8.30003 19.95 8.87001 20.21 9.17001L21.57 10.75C22.16 11.44 22.16 12.57 21.57 13.27L20.21 14.85C19.95 15.15 19.74 15.71 19.74 16.11V17.8101C19.74 18.8701 18.87 19.74 17.81 19.74H16.11C15.72 19.74 15.15 19.95 14.85 20.21L13.27 21.5699C12.58 22.1599 11.45 22.1599 10.75 21.5699L9.17004 20.21C8.87004 19.95 8.31004 19.74 7.91004 19.74H6.18C5.12 19.74 4.25 18.8701 4.25 17.8101V16.1C4.25 15.71 4.04 15.15 3.79 14.85L2.44 13.26C1.86 12.57 1.86 11.45 2.44 10.76L3.79 9.17001C4.04 8.87001 4.25 8.31003 4.25 7.91003V6.20996C4.25 5.14996 5.12 4.28002 6.18 4.28002H7.91004C8.30004 4.28002 8.87004 4.07001 9.17004 3.81001L10.75 2.44995Z" fill="currentColor" fill-opacity="0.2"/>
                <path d="M10.58 16.46L7.34998 13.23C6.94998 12.83 6.94998 12.16 7.34998 11.76C7.74998 11.36 8.41998 11.36 8.81998 11.76L10.58 13.52L15.18 8.91998C15.58 8.51998 16.25 8.51998 16.65 8.91998C17.05 9.31998 17.05 9.98998 16.65 10.39L10.58 16.46Z" fill="currentColor"/>
            </svg>
            <h3 class="text-base font-medium text-dark dark:text-white">Deposit Process</h3>
        </div>
        <div class="p-5">
            <ol class="relative border-l border-light-200 dark:border-dark-200 ml-3 space-y-6">
                <li class="ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-primary-50 dark:bg-primary-900/30 rounded-full -left-3 ring-4 ring-white dark:ring-dark-50">
                        <span class="text-xs font-bold text-primary-600 dark:text-primary-400">1</span>
                    </span>
                    <h3 class="font-medium text-dark dark:text-white">Select Method</h3>
                    <p class="text-xs text-dark-300 dark:text-light-300 mt-1">Choose your preferred deposit method from the available options.</p>
                </li>
                <li class="ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-primary-50 dark:bg-primary-900/30 rounded-full -left-3 ring-4 ring-white dark:ring-dark-50">
                        <span class="text-xs font-bold text-primary-600 dark:text-primary-400">2</span>
                    </span>
                    <h3 class="font-medium text-dark dark:text-white">Enter Amount</h3>
                    <p class="text-xs text-dark-300 dark:text-light-300 mt-1">Specify the amount you wish to deposit to your account.</p>
                </li>
                <li class="ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-primary-50 dark:bg-primary-900/30 rounded-full -left-3 ring-4 ring-white dark:ring-dark-50">
                        <span class="text-xs font-bold text-primary-600 dark:text-primary-400">3</span>
                    </span>
                    <h3 class="font-medium text-dark dark:text-white">Complete Payment</h3>
                    <p class="text-xs text-dark-300 dark:text-light-300 mt-1">Follow the instructions to complete your deposit through the selected method.</p>
                </li>
                <li class="ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-primary-50 dark:bg-primary-900/30 rounded-full -left-3 ring-4 ring-white dark:ring-dark-50">
                        <span class="text-xs font-bold text-primary-600 dark:text-primary-400">4</span>
                    </span>
                    <h3 class="font-medium text-dark dark:text-white">Confirmation</h3>
                    <p class="text-xs text-dark-300 dark:text-light-300 mt-1">Your deposit will be confirmed and credited to your account.</p>
                </li>
            </ol>
        </div>
    </div>

    <!-- Security Tips -->
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
        <div class="p-4 border-b border-light-200 dark:border-dark-200/50 flex items-center">
            <svg class="w-5 h-5 text-accent mr-2" viewBox="0 0 24 24" fill="none">
                <path d="M10.49 2.23006L5.50003 4.10004C4.35003 4.53004 3.41003 5.89004 3.41003 7.12004V14.55C3.41003 15.73 4.19005 17.28 5.14005 17.99L9.44003 21.2C10.85 22.26 13.17 22.26 14.58 21.2L18.88 17.99C19.83 17.28 20.61 15.73 20.61 14.55V7.12004C20.61 5.89004 19.67 4.53004 18.52 4.10004L13.53 2.23006C12.68 1.92006 11.32 1.92006 10.49 2.23006Z" fill="currentColor" fill-opacity="0.2"/>
                <path d="M12 12.5C11.59 12.5 11.25 12.16 11.25 11.75V8.75C11.25 8.34 11.59 8 12 8C12.41 8 12.75 8.34 12.75 8.75V11.75C12.75 12.16 12.41 12.5 12 12.5Z" fill="currentColor"/>
                <path d="M12 16C11.44 16 11 15.55 11 15C11 14.45 11.45 14 12 14C12.55 14 13 14.45 13 15C13 15.55 12.56 16 12 16Z" fill="currentColor"/>
            </svg>
            <h3 class="text-base font-medium text-dark dark:text-white">Security Tips</h3>
        </div>
        <div class="p-5">
            <ul class="space-y-3">
                <li class="flex">
                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-accent-50 dark:bg-accent-900/30 flex items-center justify-center mr-3 mt-0.5">
                        <svg class="w-3 h-3 text-accent-600 dark:text-accent-400" viewBox="0 0 24 24" fill="none">
                            <path d="M6 10L10.2 14.2L18 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p class="text-xs text-dark-300 dark:text-light-300">Always verify payment details before confirming transactions.</p>
                </li>
                <li class="flex">
                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-accent-50 dark:bg-accent-900/30 flex items-center justify-center mr-3 mt-0.5">
                        <svg class="w-3 h-3 text-accent-600 dark:text-accent-400" viewBox="0 0 24 24" fill="none">
                            <path d="M6 10L10.2 14.2L18 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p class="text-xs text-dark-300 dark:text-light-300">Use secure and private internet connections when making deposits.</p>
                </li>
                <li class="flex">
                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-accent-50 dark:bg-accent-900/30 flex items-center justify-center mr-3 mt-0.5">
                        <svg class="w-3 h-3 text-accent-600 dark:text-accent-400" viewBox="0 0 24 24" fill="none">
                            <path d="M6 10L10.2 14.2L18 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p class="text-xs text-dark-300 dark:text-light-300">For crypto deposits, double-check the network type to avoid loss of funds.</p>
                </li>
                <li class="flex">
                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-accent-50 dark:bg-accent-900/30 flex items-center justify-center mr-3 mt-0.5">
                        <svg class="w-3 h-3 text-accent-600 dark:text-accent-400" viewBox="0 0 24 24" fill="none">
                            <path d="M6 10L10.2 14.2L18 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p class="text-xs text-dark-300 dark:text-light-300">Never share your payment credentials with anyone.</p>
                </li>
            </ul>

            <div class="mt-5 pt-4 border-t border-light-200 dark:border-dark-200/50">
                <div class="flex items-center p-3 rounded-lg bg-tertiary-50 dark:bg-tertiary-900/20 border border-tertiary-100 dark:border-tertiary-800/30">
                    <svg class="w-5 h-5 text-tertiary-600 dark:text-tertiary-400 mr-3 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                        <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                        <path d="M12 6V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="text-xs text-tertiary-700 dark:text-tertiary-300">
                        Need help with your deposit? Contact our support team via the help center.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Deposit Policy -->
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
        <div class="p-4 border-b border-light-200 dark:border-dark-200/50 flex items-center">
            <svg class="w-5 h-5 text-secondary mr-2" viewBox="0 0 24 24" fill="none">
                <path d="M21 7V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V7C3 4 4.5 2 8 2H16C19.5 2 21 4 21 7Z" fill="currentColor" fill-opacity="0.2"/>
                <path d="M14.5 4.5V6.5C14.5 7.6 15.4 8.5 16.5 8.5H18.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 13H12" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 17H16" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3 class="text-base font-medium text-dark dark:text-white">Deposit Policy</h3>
        </div>
        <div class="p-5">
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-secondary-50 dark:bg-secondary-900/30 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-secondary-600 dark:text-secondary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M15.71 15.93L12.61 14.13C12.07 13.83 11.63 13.12 11.63 12.49V7.97" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-dark dark:text-white">Processing Time</h4>
                            <p class="text-xs text-dark-300 dark:text-light-300 mt-1">All payment methods</p>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-dark dark:text-white font-medium">Instant</span>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-secondary-50 dark:bg-secondary-900/30 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-secondary-600 dark:text-secondary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M8 13H16" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 17H16" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 9H12" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-dark dark:text-white">Minimum Deposit</h4>
                            <p class="text-xs text-dark-300 dark:text-light-300 mt-1">Platform requirement</p>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-dark dark:text-white font-medium">{{ $settings->currency }}{{ number_format($moresettings->minamt, 2) }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-secondary-50 dark:bg-secondary-900/30 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-secondary-600 dark:text-secondary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M15.5 12H15.51" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11.5 12H11.51" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7.5 12H7.51" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-dark dark:text-white">Deposit Methods</h4>
                            <p class="text-xs text-dark-300 dark:text-light-300 mt-1">Available options</p>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-dark dark:text-white font-medium">Multiple</span>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <a href="{{ route('accounthistory') }}" class="text-xs text-primary dark:text-primary-400 font-medium flex items-center hover:underline">
                    <span>View deposit history</span>
                    <svg class="w-3 h-3 ml-1" viewBox="0 0 24 24" fill="none">
                        <path d="M8.91003 19.9201L15.43 13.4001C16.2 12.6301 16.2 11.3701 15.43 10.6001L8.91003 4.08008" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modern styles with animations -->
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

    /* Payment methods card styles */
    .payment-method-card.active .payment-radio-inner {
        display: block;
    }

    .payment-method-card.active > div {
        border-color: theme('colors.primary.500');
        box-shadow: 0 0 0 1px theme('colors.primary.500');
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
        z-index: 10000;
    }

    .toast.success {
        background-color: #10b981;
        color: white;
    }

    .toast.danger {
        background-color: #ef4444;
        color: white;
    }

    .toast.dark-success {
        background-color: #065f46;
        color: white;
    }

    .toast.dark-danger {
        background-color: #b91c1c;
        color: white;
    }

    .toast .icon {
        margin-right: 12px;
        font-size: 1.5rem;
    }

    .toast .content {
        flex: 1;
    }

    .toast .title {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .toast .message {
        font-size: 0.75rem;
        opacity: 0.9;
    }

    .toast .close {
        cursor: pointer;
        font-size: 0.75rem;
        padding: 4px;
    }

    .toast .progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
    }

    .toast .progress-bar {
        height: 100%;
        background-color: rgba(255, 255, 255, 0.3);
        width: 100%;
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

<!-- Modern toast notifications and payment method handling -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // Modern Toast Notification System
    function showToast(type, title, message) {
        const toastContainer = document.getElementById('toast-container');
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

        // Allow click to dismiss
        toast.addEventListener('click', function() {
            this.style.animation = 'fadeOut 0.3s forwards';
            setTimeout(() => {
                this.remove();
            }, 300);
        });
    }

    // Function to handle payment method selection
    function checkpamethd(id) {
        // Enable the submit button
        document.getElementById('submit-btn').disabled = false;

        // Show the selected method panel
        document.getElementById('selected-method').classList.remove('hidden');

        // Make the API call to get method details
        let url = "{{url('/dashboard/get-method/')}}" + '/' + id;
        fetch(url)
        .then(function(res){
            return res.json();
        })
        .then(function (response){
            // Update payment method value
            $('#paymethod').val(response);

            // Update method name in the selected panel
            document.getElementById('method-name').textContent = response;

            // Find and update the method icon in the selected panel
            updateMethodIcon(id);

            // Store the last chosen method
            $('#lastchosen').val(id);

            // Show notification
            showToast('success', 'Success', 'You have chosen to pay with ' + response);
        })
        .catch(function(err){
            showToast('danger', 'Error', 'Failed to fetch payment method details');
        });
    }

    // Function to update the method icon based on the selected method
    function updateMethodIcon(id) {
        // Find the selected method's row
        const methodRows = document.querySelectorAll('tr');
        let methodIcon = null;

        methodRows.forEach(row => {
            // Check if this row contains the button with the given ID
            const button = row.querySelector(`button[onclick="checkpamethd('${id}')"]`);
            if (button) {
                // Try to find an image first
                const img = row.querySelector('.flex.items-center img');
                if (img) {
                    methodIcon = img.cloneNode(true);
                } else {
                    // Otherwise try to find the SVG
                    const svg = row.querySelector('.flex.items-center svg');
                    if (svg) {
                        methodIcon = svg.cloneNode(true);
                    }
                }
            }
        });

        // Update the method icon in the selected panel
        const methodIconContainer = document.getElementById('method-icon');
        methodIconContainer.innerHTML = '';

        if (methodIcon) {
            methodIconContainer.appendChild(methodIcon);
        } else {
            // Default icon if no image/svg found
            methodIconContainer.innerHTML = `
                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" viewBox="0 0 24 24" fill="none">
                    <path d="M9.5 13.7502C9.5 14.7202 10.25 15.5002 11.17 15.5002H13.05C13.85 15.5002 14.5 14.8202 14.5 13.9702C14.5 13.0602 14.1 12.7302 13.51 12.5202L10.5 11.4702C9.91 11.2602 9.51 10.9402 9.51 10.0202C9.51 9.18023 10.16 8.49023 10.96 8.49023H12.84C13.76 8.49023 14.51 9.27023 14.51 10.2402" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            `;
        }
    }

    // Function to reset payment method
    function resetMethod() {
        $('#paymethod').val("");
        document.getElementById('selected-method').classList.add('hidden');
        document.getElementById('submit-btn').disabled = true;
        $('#lastchosen').val(0);
    }

    // Document Ready Function
    $(document).ready(function() {
        // Form submission handling - DIRECT APPROACH FROM WORKING CODE
        $('#submitpaymentform').submit(function(e) {
            const paymethod = $('#paymethod');

            if (paymethod.val() == "") {
                e.preventDefault();
                showToast('danger', 'Error', 'Please choose a payment method by clicking on it');
                return false;
            } else {
                // Exactly matching the working version
                let makepayurl = "{{url('/dashboard/newdeposit')}}";
                $("#submitpaymentform").attr("action", makepayurl);
                return true;
            }
        });

        // Directly set form action on page load
        $('#submitpaymentform').attr('action', "{{url('/dashboard/newdeposit')}}");
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('methodSearch');
    // Select only the rows that contain actual method data
    const tableRows = document.querySelectorAll('tbody tr:not(.no-results)');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        let visibleCount = 0;

        tableRows.forEach(row => {
            // Get the name from the paragraph inside the row
            const methodName = row.querySelector('p.font-black').textContent.toLowerCase();
            
            if (methodName.includes(searchTerm)) {
                row.style.display = ''; // Show row
                visibleCount++;
            } else {
                row.style.display = 'none'; // Hide row
            }
        });

        // Optional: Handle "No Results" view
        let emptyState = document.getElementById('no-search-results');
        if (visibleCount === 0) {
            if (!emptyState) {
                const tbody = document.querySelector('tbody');
                const noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-search-results';
                noResultsRow.innerHTML = `
                    <td colspan="3" class="px-6 py-10 text-center text-slate-500 text-xs uppercase font-black tracking-widest">
                        No methods match "${e.target.value}"
                    </td>
                `;
                tbody.appendChild(noResultsRow);
            }
        } else if (emptyState) {
            emptyState.remove();
        }
    });
});
</script>
@endsection
