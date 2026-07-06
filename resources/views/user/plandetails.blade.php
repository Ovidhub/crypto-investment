@extends('layouts.dash')
@section('title', $title)
@section('content')


<div class="mb-6">
    <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-3 md:mb-0">
            <h5 class="text-dark-300 dark:text-light-300 text-2xl font-semibold">Your {{ $plan->dplan->name }} Plan</h5>
        </div>
    </div>
</div>

<x-danger-alert />
<x-success-alert />

<div class="mt-6">
    <div class="w-full">
        <div class="px-6 py-8 rounded-3xl bg-gradient-to-r from-white/80 to-white/60 dark:from-dark-50/80 dark:to-dark-100/60 backdrop-blur-md border border-white/20 dark:border-dark-200/30 shadow-lg">

            <!-- Back Button -->
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('myplans', 'All') }}" class="text-base text-dark-300 dark:text-light-300  hover:text-dark dark:hover:text-white focus:outline-none">
                    <i class="fas fa-arrow-circle-left fa-2x p-2 bg-white dark:bg-dark-50 rounded-lg"></i>
                </a>
            </div>

            <!-- Plan Title -->
            <h2 class="text-xl font-bold text-dark-300 dark:text-light-300">
                {{ $plan->dplan->name }} -
                {{ $plan->dplan->increment_type === 'Fixed' ? $settings->currency : '' }}{{ $plan->dplan->increment_amount }}{{ $plan->dplan->increment_type === 'Percentage' ? '%' : '' }}
                {{ $plan->dplan->increment_interval }}
                for {{ $plan->dplan->expiration }}
            </h2>

            <!-- Plan Status & Cancel Button -->
            <div class="flex justify-between items-center mt-3">
                <div>
                    @if ($plan->active === 'yes')
                        <span class="px-3 py-1 text-sm bg-green-200 text-green-800 rounded-full">Active</span>
                    @elseif($plan->active === 'expired')
                        <span class="px-3 py-1 text-sm bg-red-200 text-red-800 rounded-full">Expired</span>
                    @else
                        <span class="px-3 py-1 text-sm bg-red-200 text-red-800 rounded-full">Inactive</span>
                    @endif
                </div>

                @if ($settings->should_cancel_plan && $plan->active === 'yes')
                    <button data-toggle="modal" data-target="#exampleModal"
                        class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700 flex items-center">
                        <i class="fas fa-times mr-2"></i> Cancel this Plan
                    </button>

                    <!-- Cancel Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Cancel Plan</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to cancel your {{ $plan->dplan->name }} plan?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <a href="{{ route('cancelplan', $plan->id) }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <hr class="my-6">

            <!-- Plan Summary -->
            <div class="mt-5">
                <h4 class="text-lg font-semibold mb-4">Plan Information</h4>
                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-base text-dark-300 dark:text-light-300">
                            {{ $settings->currency }}{{ number_format($plan->amount, 2) }}
                        </h2>
                        <p class="text-sm text-dark-300 dark:text-light-300">Invested Amount</p>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-green-600">
                            {{ $settings->currency }}{{ number_format($plan->profit_earned, 2) }}
                        </h2>
                        <p class="text-sm text-dark-300 dark:text-light-300">Profit Earned</p>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-green-700">
                            @if ($settings->return_capital)
                                {{ $settings->currency }}{{ number_format($plan->amount + $plan->profit_earned, 2) }}
                            @else
                                {{ $settings->currency }}{{ number_format($plan->profit_earned, 2) }}
                            @endif
                        </h2>
                        <p class="text-sm text-dark-300 dark:text-light-300">Total Return</p>
                    </div>
                </div>

                <!-- Dates and Details -->
                <div class="grid md:grid-cols-3 gap-4 border-b pb-4">
                    <div>
                        <p class="text-base text-dark-300 dark:text-light-300">Duration</p>
                        <strong>{{ $plan->dplan->expiration }}</strong>
                    </div>
                    <div>
                        <p class="text-base text-dark-300 dark:text-light-300">Start Date</p>
                        <strong>{{ $plan->created_at->addHour()->toDayDateTimeString() }}</strong>
                    </div>
                    <div>
                        <p class="text-base text-dark-300 dark:text-light-300">End Date</p>
                        <strong>{{ \Carbon\Carbon::parse($plan->expire_date)->addHour()->toDayDateTimeString() }}</strong>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4 border-b mt-4 pb-4">
                    <div>
                        <p class="text-base text-dark-300 dark:text-light-300">Minimum Return</p>
                        <strong>{{ $plan->dplan->minr }}%</strong>
                    </div>
                    <div>
                        <p class="text-base text-dark-300 dark:text-light-300">Maximum Return</p>
                        <strong>{{ $plan->dplan->maxr }}%</strong>
                    </div>
                    <div>
                        <p class="text-base text-dark-300 dark:text-light-300">ROI Interval</p>
                        <strong>{{ $plan->dplan->increment_interval }}</strong>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="mt-6">
                <h4 class="text-lg font-semibold mb-4">Transactions</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-white dark:bg-dark-50 text-base text-dark-300 dark:text-light-300">
                            <tr>
                                <th class="py-2 px-4">Type</th>
                                <th class="py-2 px-4">Date</th>
                                <th class="py-2 px-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $history)
                                <tr class="border-b">
                                    <td class="py-2 px-4">Profit</td>
                                    <td class="py-2 px-4">{{ $history->created_at->addHour()->toDayDateTimeString() }}</td>
                                    <td class="py-2 px-4">
                                        {{ $settings->currency }}{{ number_format($history->amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-dark-300 dark:text-light-300">No transaction record yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
<div class="mt-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('myplans', 'All') }}" class="text-base text-dark-300 dark:text-light-300  hover:text-dark dark:hover:text-white focus:outline-none">
            <i class="fas fa-arrow-circle-left fa-2x p-2 bg-white dark:bg-dark-50 rounded-lg"></i>
        </a>
    </div>

@endsection
