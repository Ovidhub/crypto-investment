@extends('layouts.dash')
@section('title', $title)
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold dark:text-white text-dark font-display">How to Send &amp; Receive Crypto</h1>
    <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Watch the short video, then follow the steps to receive and send coins on {{ $settings->site_name }}</p>
</div>

<div class="max-w-4xl mx-auto space-y-6">

    {{-- ===================== VIDEO ===================== --}}
    <div class="rounded-2xl border border-dark-200 dark:border-dark-200 border-light-300 bg-dark-50 dark:bg-dark-50 bg-white overflow-hidden">
        <div class="p-5 border-b border-dark-200 dark:border-dark-200 border-light-300 flex items-center gap-3">
            <div class="grid h-10 w-10 place-items-center rounded-lg bg-primary/15 text-primary">
                <i data-lucide="play-circle" class="h-5 w-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold dark:text-white text-dark font-display">Video Tutorial</h2>
                <p class="text-xs dark:text-gray-400 text-gray-500">How to receive and send coins online</p>
            </div>
        </div>
        <div class="p-5">
            @include('partials.video-embed')
        </div>
    </div>

    {{-- ===================== STEP CARDS ===================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Receiving --}}
        <div class="rounded-2xl border border-dark-200 dark:border-dark-200 border-light-300 bg-dark-50 dark:bg-dark-50 bg-white p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-secondary/15 text-secondary">
                    <i data-lucide="arrow-down-left" class="h-5 w-5"></i>
                </div>
                <h3 class="text-lg font-bold dark:text-white text-dark font-display">Receiving Coins</h3>
            </div>
            <ol class="space-y-4">
                @php
                    $receiveSteps = [
                        'Open the Deposit page and choose the coin/network you want to receive.',
                        'Copy the wallet address (or scan the QR code) shown for that coin.',
                        'Send the coins from your external wallet or exchange to that address.',
                        'Wait for network confirmations — your balance updates once confirmed.',
                    ];
                @endphp
                @foreach($receiveSteps as $i => $step)
                <li class="flex gap-3">
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-primary/15 text-primary text-xs font-bold">{{ $i + 1 }}</span>
                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ $step }}</span>
                </li>
                @endforeach
            </ol>
            <a href="{{ route('deposits') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-400">
                Go to Deposit <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </a>
        </div>

        {{-- Sending --}}
        <div class="rounded-2xl border border-dark-200 dark:border-dark-200 border-light-300 bg-dark-50 dark:bg-dark-50 bg-white p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-primary/15 text-primary">
                    <i data-lucide="arrow-up-right" class="h-5 w-5"></i>
                </div>
                <h3 class="text-lg font-bold dark:text-white text-dark font-display">Sending Coins</h3>
            </div>
            <ol class="space-y-4">
                @php
                    $sendSteps = [
                        'Open the Withdraw page and pick the coin/method you want to send.',
                        'Enter the amount and paste the receiving wallet address.',
                        'Confirm the address is correct — transfers cannot be reversed.',
                        'Submit the request. You can track its status (Received / Pending / Declined) in your history.',
                    ];
                @endphp
                @foreach($sendSteps as $i => $step)
                <li class="flex gap-3">
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-primary/15 text-primary text-xs font-bold">{{ $i + 1 }}</span>
                    <span class="text-sm dark:text-gray-300 text-gray-700">{{ $step }}</span>
                </li>
                @endforeach
            </ol>
            <a href="{{ route('withdrawalsdeposits') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-400">
                Go to Withdraw <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </a>
        </div>
    </div>

    {{-- ===================== SAFETY ===================== --}}
    <div class="rounded-2xl border border-dark-200 dark:border-dark-200 border-light-300 bg-dark-50 dark:bg-dark-50 bg-white p-6">
        <h3 class="text-lg font-bold dark:text-white text-dark mb-4 font-display">Stay Safe</h3>
        <ul class="space-y-3 text-sm dark:text-gray-400 text-gray-600">
            <li class="flex gap-3">
                <i data-lucide="alert-triangle" class="h-5 w-5 shrink-0 text-secondary"></i>
                Always double-check the wallet address and network before sending — a wrong address means lost funds.
            </li>
            <li class="flex gap-3">
                <i data-lucide="shield" class="h-5 w-5 shrink-0 text-primary"></i>
                Never share your recovery phrase, seed phrase, or private key with anyone — {{ $settings->site_name }} will never ask for it.
            </li>
            <li class="flex gap-3">
                <i data-lucide="send" class="h-5 w-5 shrink-0 text-primary"></i>
                Start with a small test amount when using a new address for the first time.
            </li>
        </ul>
    </div>
</div>

<script> if (window.lucide) lucide.createIcons(); </script>
@endsection
