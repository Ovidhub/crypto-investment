@extends('layouts.dash')
@section('title', $title)
@section('content')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
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
                    <h1 class="text-3xl font-bold text-dark dark:text-white">Investment Plans</h1>
                    <p class="mt-2 text-base text-dark-300 dark:text-light-300">
                        {{ request()->route('sort') == 'yes' ? 'Active Plans' : (request()->route('sort') == 'All' ? 'All Plans' : request()->route('sort') . ' Plans') }}
                    </p>
                </div>
                <div>
                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-white/80 dark:bg-dark-50/80 backdrop-blur-sm border border-white/20 dark:border-dark-200/30 shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-accent to-tertiary text-white">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M12 6V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15 9.5C15 8.57 14.36 7.5 12.77 7.5H10.73C9.14 7.5 8.5 8.57 8.5 9.5C8.5 10.43 9.14 11.5 10.73 11.5H13.27C14.86 11.5 15.5 12.57 15.5 13.5C15.5 14.43 14.86 15.5 13.27 15.5H11.23C9.64 15.5 9 14.43 9 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-dark-300 dark:text-light-300">Total Investments</p>
                            <p class="text-xl font-bold text-dark dark:text-white">{{ $settings->currency }}{{ number_format($plans->sum('amount'), 2) }}</p>
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

<!-- Main Content -->
<div class="space-y-6">
    <!-- Filter Section -->
    @if ($numOfPlan > 0)
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex space-x-2">
                <a href="{{ url('/dashboard/sort-plans/All') }}" class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->route('sort') == 'All' || !request()->route('sort') ? 'bg-primary text-white' : 'bg-light-100 dark:bg-dark-100 text-dark-300 dark:text-light-300 hover:bg-light-200 dark:hover:bg-dark-200' }} transition-colors">
                    All Plans
                </a>
                <a href="{{ url('/dashboard/sort-plans/yes') }}" class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->route('sort') == 'yes' ? 'bg-primary text-white' : 'bg-light-100 dark:bg-dark-100 text-dark-300 dark:text-light-300 hover:bg-light-200 dark:hover:bg-dark-200' }} transition-colors">
                    Active
                </a>
                <a href="{{ url('/dashboard/sort-plans/expired') }}" class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->route('sort') == 'expired' ? 'bg-primary text-white' : 'bg-light-100 dark:bg-dark-100 text-dark-300 dark:text-light-300 hover:bg-light-200 dark:hover:bg-dark-200' }} transition-colors">
                    Expired
                </a>
                <a href="{{ url('/dashboard/sort-plans/cancelled') }}" class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->route('sort') == 'cancelled' ? 'bg-primary text-white' : 'bg-light-100 dark:bg-dark-100 text-dark-300 dark:text-light-300 hover:bg-light-200 dark:hover:bg-dark-200' }} transition-colors">
                    Cancelled
                </a>
            </div>
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <select id="sortvalue" class="pl-4 pr-10 py-2 text-sm rounded-lg bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white appearance-none">
                        <option value="All" {{ request()->route('sort') == 'All' ? 'selected' : '' }}>All Plans</option>
                        <option value="yes" {{ request()->route('sort') == 'yes' ? 'selected' : '' }}>Active</option>
                        <option value="cancelled" {{ request()->route('sort') == 'cancelled' ? 'selected' : '' }}>Cancelled/Inactive</option>
                        <option value="expired" {{ request()->route('sort') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-dark-300 dark:text-light-300" viewBox="0 0 24 24" fill="none">
                            <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <a href="javascript:;" id="sortform" class="px-4 py-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-600 transition-colors">
                    Filter
                </a>
                <a href="{{ route('mplans') }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-tertiary text-white hover:bg-tertiary-600 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none">
                        <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor" fill-opacity="0.2"/>
                        <path d="M3.40991 22C3.40991 18.13 7.25991 15 11.9999 15C12.9599 15 13.8899 15.13 14.7599 15.37" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 18C22 18.32 21.96 18.63 21.88 18.93C21.79 19.33 21.63 19.72 21.42 20.06C20.73 21.22 19.46 22 18 22C16.97 22 16.04 21.61 15.34 20.97C15.04 20.71 14.78 20.4 14.58 20.06C14.21 19.46 14 18.75 14 18C14 16.92 14.43 15.93 15.13 15.21C15.86 14.46 16.88 14 18 14C19.18 14 20.25 14.51 20.97 15.33C21.61 16.04 22 16.98 22 18Z" fill="currentColor" fill-opacity="0.2"/>
                        <path d="M19.49 17.98H16.51" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 16.52V19.51" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    New Plan
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Plans Container -->
    <div class="space-y-4">
        @forelse ($plans as $plan)
            <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden hover:shadow-md transition-all duration-300 transform hover:scale-[1.01] hover:border-primary/30 dark:hover:border-primary/30">
                <div class="p-5">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <!-- Plan Info -->
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-xl {{ $plan->active == 'yes' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : ($plan->active == 'expired' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400') }} flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                    @if($plan->active == 'yes')
                                        <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" fill="currentColor" fill-opacity="0.2"/>
                                        <path d="M8.9 12L10.36 13.46L14.0001 10.87" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    @elseif($plan->active == 'expired')
                                        <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                        <path d="M15.71 15.93L12.61 14.13C12.07 13.83 11.63 13.12 11.63 12.49V7.97" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    @else
                                        <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                        <path d="M15 9L9 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M15 15L9 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    @endif
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-dark dark:text-white">{{ $plan->dplan->name }}</h2>
                                <p class="text-sm text-dark-300 dark:text-light-300">
                                    <span class="font-medium text-dark dark:text-white">{{ $settings->currency }}{{ number_format($plan->amount) }}</span> investment
                                </p>
                            </div>
                        </div>

                        <!-- Plan Dates -->
                        <div class="hidden md:flex items-center space-x-6">
                            <div>
                                <p class="text-xs text-dark-300 dark:text-light-300">Start Date</p>
                                <p class="text-sm font-medium text-dark dark:text-white">{{ $plan->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-dark-300 dark:text-light-300">{{ $plan->created_at->format('h:i A') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-300 dark:text-light-300" viewBox="0 0 24 24" fill="none">
                                <path d="M8.91003 19.9201L15.43 13.4001C16.2 12.6301 16.2 11.3701 15.43 10.6001L8.91003 4.08008" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <p class="text-xs text-dark-300 dark:text-light-300">End Date</p>
                                <p class="text-sm font-medium text-dark dark:text-white">{{ \Carbon\Carbon::parse($plan->expire_date)->format('M d, Y') }}</p>
                                <p class="text-xs text-dark-300 dark:text-light-300">{{ \Carbon\Carbon::parse($plan->expire_date)->format('h:i A') }}</p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex items-center justify-end md:w-auto">
                            @if($plan->active == 'yes')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 dark:bg-green-400 rounded-full"></span>
                                    Active
                                </span>
                            @elseif($plan->active == 'expired')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-amber-500 dark:bg-amber-400 rounded-full"></span>
                                    Expired
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-red-500 dark:bg-red-400 rounded-full"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>

                        <!-- View Details Button -->
                        <a href="{{ route('plandetails', $plan->id) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-light-100 dark:bg-dark-100 hover:bg-light-200 dark:hover:bg-dark-200 text-dark dark:text-white transition-colors">
                            <span class="mr-2">View Details</span>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                <path d="M8.91003 19.9201L15.43 13.4001C16.2 12.6301 16.2 11.3701 15.43 10.6001L8.91003 4.08008" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Mobile Timeline -->
                    <div class="mt-4 pt-4 border-t border-light-200 dark:border-dark-200/50 md:hidden">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-xs text-dark-300 dark:text-light-300">Start Date</p>
                                <p class="text-sm font-medium text-dark dark:text-white">{{ $plan->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-dark-300 dark:text-light-300">End Date</p>
                                <p class="text-sm font-medium text-dark dark:text-white">{{ \Carbon\Carbon::parse($plan->expire_date)->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-10 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-20 h-20 rounded-full bg-light-100 dark:bg-dark-100 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-dark-300 dark:text-light-300" viewBox="0 0 24 24" fill="none">
                            <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.9946 16H12.0036" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-dark dark:text-white mb-2">No Investment Plans</h3>
                    <p class="text-dark-300 dark:text-light-300 mb-6 max-w-md mx-auto">
                        You do not have an investment plan at the moment or no value match your query.
                    </p>
                    <a href="{{ route('mplans') }}" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium inline-flex items-center gap-2 transform transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-primary/20">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M3.40991 22C3.40991 18.13 7.25991 15 11.9999 15C12.9599 15 13.8899 15.13 14.7599 15.37" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M22 18C22 18.32 21.96 18.63 21.88 18.93C21.79 19.33 21.63 19.72 21.42 20.06C20.73 21.22 19.46 22 18 22C16.97 22 16.04 21.61 15.34 20.97C15.04 20.71 14.78 20.4 14.58 20.06C14.21 19.46 14 18.75 14 18C14 16.92 14.43 15.93 15.13 15.21C15.86 14.46 16.88 14 18 14C19.18 14 20.25 14.51 20.97 15.33C21.61 16.04 22 16.98 22 18Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M19.49 17.98H16.51" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 16.52V19.51" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Buy a plan</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if (count($plans) > 0)
        <div class="flex justify-center mt-6">
            {{ $plans->links('pagination::tailwind') }}
        </div>
    @endif
</div>

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
</style>

@endsection

@section('scripts')
    @parent
    <script>
        var sortvalue = document.getElementById('sortvalue');
        var sortform = document.getElementById('sortform');
        let makepayurl = "{{ url('/dashboard/sort-plans/All') }}";
        sortform.href = makepayurl;
        sortvalue.addEventListener('change', function() {
            makepayurl = "{{ url('/dashboard/sort-plans/') }}" + '/' + sortvalue.value;
            sortform.href = makepayurl;
        })
    </script>
@endsection
