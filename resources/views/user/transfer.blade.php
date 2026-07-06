@extends('layouts.dash')
@section('title', $title)
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold dark:text-white text-dark">Internal Transfer</h1>
    <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Send funds instantly to any registered user on the platform.</p>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center gap-3 animate-in slide-in-from-top">
    <i data-lucide="check-circle" class="h-5 w-5"></i>
    <span class="text-sm font-bold">{{ session('success') }}</span>
</div>
@endif

@if(session('danger'))
<div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-500 flex items-center gap-3 animate-in slide-in-from-top">
    <i data-lucide="alert-circle" class="h-5 w-5"></i>
    <span class="text-sm font-bold">{{ session('danger') }}</span>
</div>
@endif

<div class="mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                <i data-lucide="wallet" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available Balance</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2) }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 max-w-2xl mx-auto p-6 md:p-8">
    <div class="flex items-center mb-6">
        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl mr-4">
            <i data-lucide="send" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Send Funds</h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Transfer funds to another user account instantly</p>
        </div>
    </div>

    <form method="POST" action="{{ route('transfer.process') }}" class="space-y-6">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Recipient Email or Username <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="text" name="email" id="email"
                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                    placeholder="Enter recipient's email or username" required>
            </div>
        </div>

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Amount ({{ $settings->currency }}) <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i data-lucide="banknote" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="number" name="amount" id="amount" min="50"
                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                    placeholder="Enter amount to transfer" required>
            </div>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Minimum transfer amount: {{ $settings->currency }}50.00</p>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Transfer Information</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                        <p>Transfer fee: <span class="font-semibold">0%</span> (Free Inter-Account Transfer)</p>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" 
                onclick="return confirm('Confirm this transfer? This action cannot be undone.')"
                class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
            <i data-lucide="send" class="w-5 h-5"></i>
            <span>Transfer Funds</span>
        </button>
    </form>
</div>

@endsection