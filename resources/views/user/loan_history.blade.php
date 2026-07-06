@extends('layouts.dash')
@section('title', $title)
@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold dark:text-white text-slate-900">
            <i data-lucide="history" class="inline-block w-8 h-8 mr-2 text-primary"></i> Loan History
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Track and manage all your loan applications.</p>
    </div>
    <a href="{{ route('loan.apply') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg hover:scale-[1.02]">
        <i data-lucide="plus-circle" class="w-5 h-5"></i> New Application
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-dark-50 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-dark-200">
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Apps</p>
        <p class="text-2xl font-bold dark:text-white text-slate-900">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white dark:bg-dark-50 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-dark-200">
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Approved</p>
        <p class="text-2xl font-bold text-emerald-500">{{ $stats['approved'] }}</p>
    </div>
    <div class="bg-white dark:bg-dark-50 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-dark-200">
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Pending</p>
        <p class="text-2xl font-bold text-amber-500">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-white dark:bg-dark-50 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-dark-200">
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Value</p>
        <p class="text-2xl font-bold text-primary">{{ $settings->currency }}{{ number_format($stats['value'], 2) }}</p>
    </div>
</div>

<div class="hidden lg:block bg-white dark:bg-dark-50 rounded-2xl shadow-lg border border-slate-200 dark:border-dark-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 dark:bg-dark-100 border-b dark:border-dark-200">
                <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Amount</th>
                <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Purpose</th>
                <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Duration</th>
                <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y dark:divide-dark-200">
            @forelse($loans as $loan)
            <tr class="hover:bg-slate-50 dark:hover:bg-primary/5 transition-colors">
                <td class="p-4 font-bold dark:text-white">{{ $settings->currency }}{{ number_format($loan->amount, 2) }}</td>
                <td class="p-4 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($loan->purpose, 30) }}</td>
                <td class="p-4 text-sm dark:text-white">{{ $loan->duration }} Months</td>
                <td class="p-4">
                    @if($loan->active == 'Pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-black uppercase bg-amber-500/10 text-amber-500 rounded-full border border-amber-500/20">
                            <span class="h-1.5 w-1.5 bg-amber-500 rounded-full animate-pulse"></span> Pending
                        </span>
                    @elseif($loan->active == 'Approved')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-500 rounded-full border border-emerald-500/20">
                            <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full"></span> Approved
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 text-[10px] font-black uppercase bg-rose-500/10 text-rose-500 rounded-full border border-rose-500/20">Rejected</span>
                    @endif
                </td>
                <td class="p-4 text-xs text-slate-400">{{ \Carbon\Carbon::parse($loan->created_at)->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-12 text-center text-slate-400 uppercase text-xs font-black tracking-widest">No loan records found in ledger</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection