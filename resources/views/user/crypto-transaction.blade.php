@extends('layouts.dash')
@section('title', $title)
@section('content')
<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col items-end space-y-4"></div>

<!-- Meta Tag for CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Page Container -->
<div class="container-fluid" style="max-width: 1400px;">
    <!-- Breadcrumb Navigation -->
    <div class="flex items-center text-sm text-dark-400 dark:text-light-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <svg class="w-4 h-4 mx-2" viewBox="0 0 16 16" fill="none">
            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <a href="{{ route('assetbalance') }}" class="hover:text-primary transition-colors">Asset Balance</a>
        <svg class="w-4 h-4 mx-2" viewBox="0 0 16 16" fill="none">
            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="text-dark dark:text-white">Transaction History</span>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-5">
        <div class="flex items-center mb-4 sm:mb-0">
            <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M17.7501 7.05029H6.25006C5.85006 7.05029 5.50006 6.70029 5.50006 6.30029C5.50006 5.90029 5.85006 5.55029 6.25006 5.55029H17.7501C18.1501 5.55029 18.5001 5.90029 18.5001 6.30029C18.5001 6.70029 18.1501 7.05029 17.7501 7.05029Z" fill="currentColor"/>
                    <path d="M15.75 12.7003H8.25C7.85 12.7003 7.5 12.3503 7.5 11.9503C7.5 11.5503 7.85 11.2003 8.25 11.2003H15.75C16.15 11.2003 16.5 11.5503 16.5 11.9503C16.5 12.3503 16.15 12.7003 15.75 12.7003Z" fill="currentColor"/>
                    <path d="M13.75 18.3502H10.25C9.85 18.3502 9.5 18.0002 9.5 17.6002C9.5 17.2002 9.85 16.8502 10.25 16.8502H13.75C14.15 16.8502 14.5 17.2002 14.5 17.6002C14.5 18.0002 14.15 18.3502 13.75 18.3502Z" fill="currentColor"/>
                    <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-dark dark:text-white">Swap Transaction History</h1>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('assetbalance') }}" class="px-4 py-2 text-sm font-medium text-dark-500 dark:text-light-400 bg-light-100 dark:bg-dark-200 hover:bg-light-200 dark:hover:bg-dark-300 rounded-lg flex items-center transition-colors">
                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none">
                    <path d="M9.57 5.92999L3.5 12L9.57 18.07" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.5 12H3.67" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to Asset Balance
            </a>
        </div>
    </div>
    
    <!-- Alert Messages -->
    <x-danger-alert/>
	<x-success-alert/>

    <!-- Crypto Transactions Table Card -->
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
        <div class="p-4 border-b border-light-200 dark:border-dark-200/50">
            <h2 class="text-base font-bold text-dark dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M16.44 8.90002C20.04 9.21002 21.51 11.06 21.51 15.11V15.24C21.51 19.71 19.72 21.5 15.25 21.5H8.73998C4.26998 21.5 2.47998 19.71 2.47998 15.24V15.11C2.47998 11.09 3.92998 9.24002 7.46998 8.91002" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15.0001V3.62012" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.35 5.85L12 2.5L8.65002 5.85" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Transaction Records</span>
            </h2>
                    </div>
        
        <!-- Table Container -->
        <div class="p-5">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-light-200 dark:divide-dark-200/50">
                                    <thead>
                        <tr class="bg-light-50 dark:bg-dark-200/50">
                            <th class="px-4 py-3.5 text-left text-xs font-medium text-dark-500 dark:text-light-400 uppercase tracking-wider">Source</th>
                            <th class="px-4 py-3.5 text-left text-xs font-medium text-dark-500 dark:text-light-400 uppercase tracking-wider">Destination</th>
                            <th class="px-4 py-3.5 text-left text-xs font-medium text-dark-500 dark:text-light-400 uppercase tracking-wider">Amount (Source)</th>
                            <th class="px-4 py-3.5 text-left text-xs font-medium text-dark-500 dark:text-light-400 uppercase tracking-wider">Quantity (Dest)</th>
                            <th class="px-4 py-3.5 text-left text-xs font-medium text-dark-500 dark:text-light-400 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                    <tbody class="bg-white dark:bg-dark-50 divide-y divide-light-200 dark:divide-dark-200/30">
                                        @forelse($transactions as $tran)
                        <tr class="hover:bg-light-50 dark:hover:bg-dark-200/30 transition-colors">
                            <td class="px-4 py-4 text-sm text-dark dark:text-white">
                                <div class="flex items-center">
                                    @if(strtolower($tran->source) == 'usd')
                                    <div class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mr-2">
                                        <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.5 13.75C9.5 14.72 10.25 15.5 11.17 15.5H13.05C13.85 15.5 14.5 14.82 14.5 13.97C14.5 13.06 14.1 12.73 13.51 12.52L10.5 11.47C9.91 11.26 9.51001 10.94 9.51001 10.02C9.51001 9.18 10.16 8.49001 10.96 8.49001H12.84C13.76 8.49001 14.51 9.27001 14.51 10.24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    @elseif(strtolower($tran->source) == 'btc')
                                    <div class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mr-2">
                                        <img class="w-5 h-5" src="https://img.icons8.com/color/48/000000/bitcoin--v1.png" alt="BTC" />
                                    </div>
                                    @elseif(strtolower($tran->source) == 'eth')
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mr-2">
                                        <img class="w-5 h-5" src="https://img.icons8.com/fluency/48/000000/ethereum.png" alt="ETH" />
                                    </div>
                                    @elseif(strtolower($tran->source) == 'ltc')
                                    <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-gray-900/30 flex items-center justify-center mr-2">
                                        <img class="w-5 h-5" src="https://img.icons8.com/fluency/48/000000/litecoin.png" alt="LTC" />
                                    </div>
                                    @elseif(strtolower($tran->source) == 'usdt')
                                    <div class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mr-2">
                                        <img class="w-5 h-5" src="https://img.icons8.com/color/48/000000/tether--v2.png" alt="USDT" />
                                    </div>
                                    @else
                                    <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mr-2">
                                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none">
                                            <path d="M15.1047 7.00001C14.5347 6.37001 13.8247 5.88002 13.0047 5.57002C12.1947 5.26002 11.3047 5.10002 10.4147 5.11002H7.42471C7.00471 5.11002 6.61472 5.18999 6.24472 5.33999C5.87472 5.48999 5.54471 5.70999 5.26471 5.98999C4.98471 6.26999 4.76471 6.59001 4.61471 6.96001C4.46471 7.33001 4.38471 7.72001 4.38471 8.13001V15.77C4.38471 16.18 4.46471 16.57 4.61471 16.94C4.76471 17.31 4.98471 17.64 5.26471 17.92C5.54471 18.2 5.87472 18.42 6.24472 18.57C6.61472 18.72 7.00471 18.8 7.42471 18.8H10.4147C11.3047 18.81 12.1947 18.65 13.0047 18.34C13.8247 18.03 14.5347 17.54 15.1047 16.91C16.2647 15.67 16.9047 14 16.9047 12.1C16.9047 10.2 16.2647 8.36001 15.1047 7.00001Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M19.9851 9.11L16.9851 12.11L19.9851 15.11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    @endif
                                    <span class="font-medium">{{strtoupper($tran->source)}}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-dark dark:text-white">
                                <div class="flex items-center">
                                    @if(strtolower($tran->dest) == 'usd')
                                    <div class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mr-2">
                                        <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.5 13.75C9.5 14.72 10.25 15.5 11.17 15.5H13.05C13.85 15.5 14.5 14.82 14.5 13.97C14.5 13.06 14.1 12.73 13.51 12.52L10.5 11.47C9.91 11.26 9.51001 10.94 9.51001 10.02C9.51001 9.18 10.16 8.49001 10.96 8.49001H12.84C13.76 8.49001 14.51 9.27001 14.51 10.24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    @elseif(strtolower($tran->dest) == 'btc')
                                    <div class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mr-2">
                                        <img class="w-5 h-5" src="https://img.icons8.com/color/48/000000/bitcoin--v1.png" alt="BTC" />
                                    </div>
                                    @elseif(strtolower($tran->dest) == 'eth')
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mr-2">
                                        <img class="w-5 h-5" src="https://img.icons8.com/fluency/48/000000/ethereum.png" alt="ETH" />
                                    </div>
                                    @elseif(strtolower($tran->dest) == 'ltc')
                                    <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-gray-900/30 flex items-center justify-center mr-2">
                                        <img class="w-5 h-5" src="https://img.icons8.com/fluency/48/000000/litecoin.png" alt="LTC" />
                                    </div>
                                    @elseif(strtolower($tran->dest) == 'usdt')
                                    <div class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mr-2">
                                        <img class="w-5 h-5" src="https://img.icons8.com/color/48/000000/tether--v2.png" alt="USDT" />
                                    </div>
                                    @else
                                    <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mr-2">
                                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none">
                                            <path d="M15.1047 7.00001C14.5347 6.37001 13.8247 5.88002 13.0047 5.57002C12.1947 5.26002 11.3047 5.10002 10.4147 5.11002H7.42471C7.00471 5.11002 6.61472 5.18999 6.24472 5.33999C5.87472 5.48999 5.54471 5.70999 5.26471 5.98999C4.98471 6.26999 4.76471 6.59001 4.61471 6.96001C4.46471 7.33001 4.38471 7.72001 4.38471 8.13001V15.77C4.38471 16.18 4.46471 16.57 4.61471 16.94C4.76471 17.31 4.98471 17.64 5.26471 17.92C5.54471 18.2 5.87472 18.42 6.24472 18.57C6.61472 18.72 7.00471 18.8 7.42471 18.8H10.4147C11.3047 18.81 12.1947 18.65 13.0047 18.34C13.8247 18.03 14.5347 17.54 15.1047 16.91C16.2647 15.67 16.9047 14 16.9047 12.1C16.9047 10.2 16.2647 8.36001 15.1047 7.00001Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M19.9851 9.11L16.9851 12.11L19.9851 15.11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    @endif
                                    <span class="font-medium">{{strtoupper($tran->dest)}}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-dark dark:text-white">
                                <span class="font-medium">{{number_format(round($tran->amount, 6), 6, '.', ',')}}</span>
                            </td>
                            <td class="px-4 py-4 text-sm text-dark dark:text-white">
                                <span class="font-medium">{{number_format(round($tran->quantity, 6), 6, '.', ',')}}</span>
                            </td>
                            <td class="px-4 py-4 text-sm text-dark-400 dark:text-light-500">
                                {{ \Carbon\Carbon::parse($tran->created_at)->toDayDateTimeString() }}
                            </td>
                                            </tr>
                                        @empty
                                            <tr>
                            <td colspan="5" class="px-4 py-8 text-sm text-center text-dark-400 dark:text-light-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 mb-3 text-dark-300 dark:text-light-600" viewBox="0 0 24 24" fill="none">
                                        <path d="M19.32 10.7488L20.7066 9.36225C22.2881 7.78108 22.2881 5.21892 20.7066 3.63775C19.125 2.05592 16.5625 2.05592 14.9809 3.63775L13.5943 5.02434" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4.67188 13.2488L3.28545 14.6362C1.70395 16.2174 1.70395 18.7795 3.28545 20.3607C4.86695 21.9427 7.42947 21.9427 9.01097 20.3607L10.3974 18.9741" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10.5574 13.4425L13.4425 10.5574" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M7.40723 10.292L13.707 16.5917" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p>No transaction records found</p>
                                </div>
                            </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                                {{$transactions->links()}}
                            </div>
                        </div>
                    </div>

    <!-- Transaction Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <!-- Total Swaps Card -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5">
            <div class="flex items-start">
                <div class="w-12 h-12 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-primary" viewBox="0 0 24 24" fill="none">
                        <path d="M9.5 13.75C9.5 14.72 10.25 15.5 11.17 15.5H13.05C13.85 15.5 14.5 14.82 14.5 13.97C14.5 13.06 14.1 12.73 13.51 12.52L10.5 11.47C9.91 11.26 9.51001 10.94 9.51001 10.02C9.51001 9.18 10.16 8.49001 10.96 8.49001H12.84C13.76 8.49001 14.51 9.27001 14.51 10.24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 3V7H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 2L17 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-dark-400 dark:text-light-400 mb-1">Total Transactions</h3>
                    <p class="text-2xl font-bold text-dark dark:text-white">{{ $transactions->total() }}</p>
                </div>
            </div>
        </div>
        
        <!-- Last Swap Card -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5">
            <div class="flex items-start">
                <div class="w-12 h-12 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-500" viewBox="0 0 24 24" fill="none">
                        <path d="M8 10V16M12 12V16M16 8V16M7 19H17C18.1046 19 19 18.1046 19 17V7C19 5.89543 18.1046 5 17 5H7C5.89543 5 5 5.89543 5 7V17C5 18.1046 5.89543 19 7 19Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-dark-400 dark:text-light-400 mb-1">Last Transaction</h3>
                    @if(count($transactions) > 0)
                    <p class="text-xl font-bold text-dark dark:text-white">
                        {{ strtoupper($transactions->first()->source) }} → {{ strtoupper($transactions->first()->dest) }}
                    </p>
                    <p class="text-sm text-dark-400 dark:text-light-500">
                        {{ \Carbon\Carbon::parse($transactions->first()->created_at)->diffForHumans() }}
                    </p>
                    @else
                    <p class="text-xl font-bold text-dark dark:text-white">-</p>
                    <p class="text-sm text-dark-400 dark:text-light-500">No transactions yet</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Processing Fees Card -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-5">
            <div class="flex items-start">
                <div class="w-12 h-12 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-amber-500" viewBox="0 0 24 24" fill="none">
                        <path d="M10.75 8.75C10.75 9.44036 10.1904 10 9.5 10C8.80964 10 8.25 9.44036 8.25 8.75C8.25 8.05964 8.80964 7.5 9.5 7.5C10.1904 7.5 10.75 8.05964 10.75 8.75Z" fill="currentColor"/>
                        <path d="M15.75 15.75C15.75 16.4404 15.1904 17 14.5 17C13.8096 17 13.25 16.4404 13.25 15.75C13.25 15.0596 13.8096 14.5 14.5 14.5C15.1904 14.5 15.75 15.0596 15.75 15.75Z" fill="currentColor"/>
                        <path d="M8.52081 9.56219L15.4069 15.0179" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-dark-400 dark:text-light-400 mb-1">Exchange Information</h3>
                    <p class="text-lg font-bold text-dark dark:text-white mb-1">Fixed 0.5% Fee</p>
                    <p class="text-xs text-dark-400 dark:text-light-500">Real-time market rates</p>
                </div>
            </div>
        </div>
	</div>
</div>

<!-- Scripts -->
<script>
    // Toast notification system
    function showToast(message, type = 'success', duration = 5000) {
        const toastContainer = document.getElementById('toast-container');
        
        // Create toast element
        const toast = document.createElement('div');
        
        // Set classes based on type
        let bgClass, iconColor, icon;
        if (type === 'success') {
            bgClass = 'bg-green-500 dark:bg-green-600';
            iconColor = 'text-green-500';
            icon = `<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>`;
        } else if (type === 'error') {
            bgClass = 'bg-red-500 dark:bg-red-600';
            iconColor = 'text-red-500';
            icon = `<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>`;
        } else {
            bgClass = 'bg-blue-500 dark:bg-blue-600';
            iconColor = 'text-blue-500';
            icon = `<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>`;
        }
        
        toast.className = `flex items-center p-4 mb-3 w-full max-w-xs text-white ${bgClass} rounded-lg shadow-md transform transition-all duration-300 translate-x-0 opacity-100`;
        toast.innerHTML = `
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-white ${iconColor}">
                ${icon}
            </div>
            <div class="ml-3 text-sm font-normal">${message}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-white hover:text-gray-100 rounded-lg p-1.5 inline-flex h-8 w-8 focus:outline-none">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        `;
        
        // Add toast to container
        toastContainer.appendChild(toast);
        
        // Add event listener for close button
        toast.querySelector('button').addEventListener('click', () => {
            toast.classList.replace('opacity-100', 'opacity-0');
            toast.classList.replace('translate-x-0', 'translate-x-full');
            setTimeout(() => {
                toastContainer.removeChild(toast);
            }, 300);
        });
        
        // Auto remove toast after duration
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.replace('opacity-100', 'opacity-0');
                toast.classList.replace('translate-x-0', 'translate-x-full');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toastContainer.removeChild(toast);
                    }
                }, 300);
            }
        }, duration);
    }
    
    // Global notify function for compatibility
    window.notify = function(options) {
        const type = options.type || 'info';
        const message = options.message || '';
        
        showToast(message, type);
    };
    
    // Support for jQuery notify if jQuery exists
    if (typeof jQuery !== 'undefined') {
        jQuery.notify = function(message, type) {
            showToast(message, type);
        };
    }
    
    // Check for URL parameters to show success/error messages
    document.addEventListener('DOMContentLoaded', function() {
        // Show any flash messages from session
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            showToast(urlParams.get('success'), 'success');
        }
        if (urlParams.has('error')) {
            showToast(urlParams.get('error'), 'error');
        }
    });
</script>
@endsection