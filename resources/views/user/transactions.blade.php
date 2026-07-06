@extends('layouts.dash')
@section('title', $title)
@section('content')
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold dark:text-white text-dark">Transaction Records</h1>
        <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">View all your financial activities</p>
    </div>
    
    <x-danger-alert />
    <x-success-alert />
    
    <!-- Transaction Records Card -->
    <div class="dark:bg-dark-50 bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Transaction Tabs - Scrollable container for mobile -->
        <div class="px-2 sm:px-6 border-b dark:border-dark-100 border-light-200">
            <div class="flex overflow-x-auto py-3 sm:py-4 no-scrollbar" role="tablist">
                <!-- Deposit Tab -->
                <button type="button" 
                        class="mr-3 pb-3 px-1 inline-flex flex-col items-center text-sm font-medium border-b-2 border-primary text-primary focus:outline-none whitespace-nowrap tab-button active" 
                        data-tab="deposit-tab" 
                        role="tab">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-primary/10 mb-1 sm:mb-2">
                        <i data-lucide="download" class="h-4 w-4 text-primary"></i>
                    </div>
                    <span class="text-xs sm:text-sm">Deposits</span>
                </button>
                
                <!-- Withdrawal Tab -->
                <button type="button" 
                        class="mr-3 pb-3 px-1 inline-flex flex-col items-center text-sm font-medium border-b-2 border-transparent dark:text-gray-400 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none whitespace-nowrap tab-button" 
                        data-tab="withdrawal-tab" 
                        role="tab">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full dark:bg-dark-100/80 bg-light-100/80 mb-1 sm:mb-2">
                        <i data-lucide="upload" class="h-4 w-4 dark:text-gray-400 text-gray-500"></i>
                    </div>
                    <span class="text-xs sm:text-sm">Withdrawals</span>
                </button>
                
                <!-- Other Transactions Tab -->
                <button type="button" 
                        class="mr-3 pb-3 px-1 inline-flex flex-col items-center text-sm font-medium border-b-2 border-transparent dark:text-gray-400 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none whitespace-nowrap tab-button" 
                        data-tab="other-tab" 
                        role="tab">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full dark:bg-dark-100/80 bg-light-100/80 mb-1 sm:mb-2">
                        <i data-lucide="repeat" class="h-4 w-4 dark:text-gray-400 text-gray-500"></i>
                    </div>
                    <span class="text-xs sm:text-sm">Others</span>
                </button>
            </div>
        </div>
        
        <!-- Tab Content -->
        <div class="p-0 sm:p-6">
            <!-- Deposit Tab Content -->
            <div id="deposit-tab" class="tab-content block">
                <!-- Mobile cards layout for small screens -->
                <div class="sm:hidden">
                    <ul class="divide-y dark:divide-dark-100 divide-light-200">
                        @forelse ($deposits as $deposit)
                            <li class="p-4">
                                <div class="mb-2 flex justify-between items-center">
                                    <span class="text-base font-medium dark:text-white text-dark">{{ $settings->currency }}{{ $deposit->amount }}</span>
                                    @if ($deposit->status == 'Processed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-400">
                                            {{ $deposit->status }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-400">
                                            {{ $deposit->status }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs dark:text-gray-400 text-gray-600">Payment Method</span>
                                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ $deposit->payment_mode }}</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs dark:text-gray-400 text-gray-600">Date</span>
                                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ \Carbon\Carbon::parse($deposit->created_at)->format('M d, Y H:i') }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="p-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                        <i data-lucide="inbox" class="h-6 w-6 text-primary"></i>
                                    </div>
                                    <p class="text-sm font-medium dark:text-white text-dark mb-1">No deposits found</p>
                                    <p class="text-xs dark:text-gray-400 text-gray-500">Your deposit history will appear here</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                
                <!-- Table layout for larger screens -->
                <div class="hidden sm:block overflow-hidden">
                    <div class="overflow-x-auto sm:mx-0">
                        <table class="min-w-full divide-y dark:divide-dark-100 divide-light-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Payment Mode</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-dark-100 divide-light-200">
                                @forelse ($deposits as $deposit)
                                    <tr class="hover:dark:bg-dark-100/50 hover:bg-light-100/50 transition-colors">
                                        <td class="px-6 py-4 text-sm dark:text-white text-dark">{{ $settings->currency }}{{ $deposit->amount }}</td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-300 text-gray-700">{{ $deposit->payment_mode }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @if ($deposit->status == 'Processed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-400">
                                                    {{ $deposit->status }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-400">
                                                    {{ $deposit->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-300 text-gray-700">{{ \Carbon\Carbon::parse($deposit->created_at)->toDayDateTimeString() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-sm text-center dark:text-gray-400 text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                                    <i data-lucide="inbox" class="h-6 w-6 text-primary"></i>
                                                </div>
                                                <p class="text-sm font-medium dark:text-white text-dark mb-1">No deposits found</p>
                                                <p class="text-xs dark:text-gray-400 text-gray-500">Your deposit history will appear here</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination for Deposits -->
                @if($deposits->hasPages())
                <div class="mt-6 px-6">
                    {{ $deposits->onEachSide(1)->links('components.transaction-pagination') }}
                </div>
                @endif
            </div>
            
            <!-- Withdrawal Tab Content -->
            <div id="withdrawal-tab" class="tab-content hidden">
                <!-- Mobile cards layout for small screens -->
                <div class="sm:hidden">
                    <ul class="divide-y dark:divide-dark-100 divide-light-200">
                        @forelse ($withdrawals as $withdrawal)
                            @php
                                $wLabel = $withdrawal->status == 'Processed' ? 'Received' : $withdrawal->status;
                                $wBadge = $withdrawal->status == 'Processed'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-400'
                                    : ($withdrawal->status == 'Pending'
                                        ? 'bg-amber-100 text-amber-800 dark:bg-amber-800/20 dark:text-amber-400'
                                        : 'bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-400');
                            @endphp
                            <li class="p-4">
                                <div class="mb-2 flex justify-between items-center">
                                    <span class="text-base font-medium dark:text-white text-dark">{{ $settings->currency }}{{ $withdrawal->amount }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wBadge }}">
                                        {{ $wLabel }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs dark:text-gray-400 text-gray-600">Total (incl. charges)</span>
                                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ $settings->currency }}{{ $withdrawal->to_deduct }}</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs dark:text-gray-400 text-gray-600">Payment Method</span>
                                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ $withdrawal->payment_mode }}</span>
                                </div>
                                @if(!empty($withdrawal->paydetails))
                                <div class="flex justify-between gap-3 mt-1">
                                    <span class="text-xs dark:text-gray-400 text-gray-600 shrink-0">Receiving into</span>
                                    <span class="text-xs dark:text-gray-300 text-gray-700 text-right break-all font-mono">{{ $withdrawal->paydetails }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs dark:text-gray-400 text-gray-600">Date</span>
                                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ \Carbon\Carbon::parse($withdrawal->created_at)->format('M d, Y H:i') }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="p-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                        <i data-lucide="inbox" class="h-6 w-6 text-primary"></i>
                                    </div>
                                    <p class="text-sm font-medium dark:text-white text-dark mb-1">No withdrawals found</p>
                                    <p class="text-xs dark:text-gray-400 text-gray-500">Your withdrawal history will appear here</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                
                <!-- Table layout for larger screens -->
                <div class="hidden sm:block overflow-hidden">
                    <div class="overflow-x-auto sm:mx-0">
                        <table class="min-w-full divide-y dark:divide-dark-100 divide-light-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Amount + Charges</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Payment Mode</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Receiving Into</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-dark-100 divide-light-200">
                                @forelse ($withdrawals as $withdrawal)
                                    @php
                                        $wLabel = $withdrawal->status == 'Processed' ? 'Received' : $withdrawal->status;
                                        $wBadge = $withdrawal->status == 'Processed'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-400'
                                            : ($withdrawal->status == 'Pending'
                                                ? 'bg-amber-100 text-amber-800 dark:bg-amber-800/20 dark:text-amber-400'
                                                : 'bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-400');
                                    @endphp
                                    <tr class="hover:dark:bg-dark-100/50 hover:bg-light-100/50 transition-colors">
                                        <td class="px-6 py-4 text-sm dark:text-white text-dark">{{ $settings->currency }}{{ $withdrawal->amount }}</td>
                                        <td class="px-6 py-4 text-sm dark:text-white text-dark">{{ $settings->currency }}{{ $withdrawal->to_deduct }}</td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-300 text-gray-700">{{ $withdrawal->payment_mode }}</td>
                                        <td class="px-6 py-4 text-xs dark:text-gray-300 text-gray-700 font-mono break-all max-w-[200px]">{{ $withdrawal->paydetails ?: '—' }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wBadge }}">
                                                {{ $wLabel }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-300 text-gray-700">{{ \Carbon\Carbon::parse($withdrawal->created_at)->toDayDateTimeString() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-sm text-center dark:text-gray-400 text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                                    <i data-lucide="inbox" class="h-6 w-6 text-primary"></i>
                                                </div>
                                                <p class="text-sm font-medium dark:text-white text-dark mb-1">No withdrawals found</p>
                                                <p class="text-xs dark:text-gray-400 text-gray-500">Your withdrawal history will appear here</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination for Withdrawals -->
                @if($withdrawals->hasPages())
                <div class="mt-6 px-6">
                    {{ $withdrawals->onEachSide(1)->links('components.transaction-pagination') }}
                </div>
                @endif
            </div>
            
            <!-- Other Transactions Tab Content -->
            <div id="other-tab" class="tab-content hidden">
                <!-- Mobile cards layout for small screens -->
                <div class="sm:hidden">
                    <ul class="divide-y dark:divide-dark-100 divide-light-200">
                        @forelse ($t_history as $history)
                            <li class="p-4">
                                <div class="mb-2 flex justify-between items-center">
                                    <span class="text-base font-medium dark:text-white text-dark">{{ $settings->currency }}{{ $history->amount }}</span>
                                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ $history->type }}</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs dark:text-gray-400 text-gray-600">Plan/Narration</span>
                                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ $history->plan }}</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs dark:text-gray-400 text-gray-600">Date</span>
                                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ \Carbon\Carbon::parse($history->created_at)->format('M d, Y H:i') }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="p-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                        <i data-lucide="inbox" class="h-6 w-6 text-primary"></i>
                                    </div>
                                    <p class="text-sm font-medium dark:text-white text-dark mb-1">No transactions found</p>
                                    <p class="text-xs dark:text-gray-400 text-gray-500">Other transactions will appear here</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                
                <!-- Table layout for larger screens -->
                <div class="hidden sm:block overflow-hidden">
                    <div class="overflow-x-auto sm:mx-0">
                        <table class="min-w-full divide-y dark:divide-dark-100 divide-light-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Plan/Narration</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-dark-100 divide-light-200">
                                @forelse ($t_history as $history)
                                    <tr class="hover:dark:bg-dark-100/50 hover:bg-light-100/50 transition-colors">
                                        <td class="px-6 py-4 text-sm dark:text-white text-dark">{{ $settings->currency }}{{ $history->amount }}</td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-300 text-gray-700">{{ $history->type }}</td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-300 text-gray-700">{{ $history->plan }}</td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-300 text-gray-700">{{ \Carbon\Carbon::parse($history->created_at)->toDayDateTimeString() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-sm text-center dark:text-gray-400 text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                                    <i data-lucide="inbox" class="h-6 w-6 text-primary"></i>
                                                </div>
                                                <p class="text-sm font-medium dark:text-white text-dark mb-1">No transactions found</p>
                                                <p class="text-xs dark:text-gray-400 text-gray-500">Other transactions will appear here</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination for Other Transactions -->
                @if($t_history->hasPages())
                <div class="mt-6 px-6">
                    {{ $t_history->onEachSide(1)->links('components.transaction-pagination') }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'text-primary', 'border-primary');
                    btn.classList.add('dark:text-gray-400', 'text-gray-500', 'border-transparent');
                    
                    // Reset icons container
                    const iconContainer = btn.querySelector('div');
                    iconContainer.classList.remove('bg-primary/10');
                    iconContainer.classList.add('dark:bg-dark-100/80', 'bg-light-100/80');
                    
                    // Reset icons
                    const icon = btn.querySelector('[data-lucide]');
                    icon.classList.remove('text-primary');
                    icon.classList.add('dark:text-gray-400', 'text-gray-500');
                });
                
                // Add active class to clicked button
                button.classList.add('active', 'text-primary', 'border-primary');
                button.classList.remove('dark:text-gray-400', 'text-gray-500', 'border-transparent');
                
                // Update icon container and icon
                const iconContainer = button.querySelector('div');
                iconContainer.classList.add('bg-primary/10');
                iconContainer.classList.remove('dark:bg-dark-100/80', 'bg-light-100/80');
                
                const icon = button.querySelector('[data-lucide]');
                icon.classList.add('text-primary');
                icon.classList.remove('dark:text-gray-400', 'text-gray-500');
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show selected tab content
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId).classList.remove('hidden');
            });
        });
    });
</script>

<style>
    /* Hide scrollbar but allow scrolling */
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    
    .no-scrollbar::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
</style>
@endsection