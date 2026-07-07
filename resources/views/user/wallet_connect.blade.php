@extends('layouts.dash')
@section('title', $title)
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold dark:text-white text-dark font-display">Connect Wallet</h1>
    <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Link a public wallet address to your {{ $settings->site_name }} account</p>
</div>

<x-danger-alert />
<x-success-alert />

@php
    $approved = $wallets->where('status', 'approved')->count();
    $isConnected = $approved > 0;
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    {{-- ===================== DAILY REWARDS BANNER ===================== --}}
    <div class="rounded-2xl border border-dark-200 dark:border-dark-200 border-light-300 bg-gradient-to-br from-primary/10 via-dark-50 to-dark-50 dark:from-primary/10 p-6 relative overflow-hidden">
        <div class="absolute -top-16 -right-10 h-48 w-48 rounded-full bg-primary/20 blur-3xl"></div>
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-widest text-primary">Daily Rewards</p>
            <h2 class="mt-2 text-2xl font-bold dark:text-white text-dark font-display">Connect your wallet to unlock rewards</h2>
            <p class="mt-2 max-w-xl text-sm dark:text-gray-400 text-gray-600">
                Submit a verified public crypto wallet address. Once approved by admin, you will start receiving daily rewards directly to your account.
            </p>

            <div class="mt-5 flex flex-wrap items-center gap-6">
                @if($isConnected)
                <span class="inline-flex items-center gap-2 rounded-full bg-secondary/15 px-4 py-1.5 text-sm font-semibold text-secondary">
                    <span class="h-2 w-2 rounded-full bg-secondary"></span> Connected
                </span>
                @else
                <span class="inline-flex items-center gap-2 rounded-full bg-white/5 border border-dark-200 px-4 py-1.5 text-sm font-semibold dark:text-gray-300 text-gray-600">
                    <span class="h-2 w-2 rounded-full bg-gray-400"></span> Not connected
                </span>
                @endif

                <div>
                    <p class="text-xs dark:text-gray-400 text-gray-500">Current daily reward</p>
                    <p class="text-2xl font-bold dark:text-white text-dark font-display">
                        ${{ number_format((float) (Auth::user()->wallet_daily_reward ?? 0), 2) }} <span class="text-sm font-medium dark:text-gray-400 text-gray-500">/ day</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== VIDEO: HOW TO SEND & RECEIVE ===================== --}}
    <div class="rounded-2xl border border-dark-200 dark:border-dark-200 border-light-300 bg-dark-50 dark:bg-dark-50 bg-white overflow-hidden">
        <div class="flex items-center justify-between gap-4 p-5 border-b border-dark-200 dark:border-dark-200 border-light-300">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-primary/15 text-primary">
                    <i data-lucide="play-circle" class="h-5 w-5"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold dark:text-white text-dark font-display">How to Send &amp; Receive Coins</h2>
                    <p class="text-xs dark:text-gray-400 text-gray-500">Quick video walkthrough</p>
                </div>
            </div>
            <a href="{{ route('send.receive.guide') }}" class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-400">
                Full guide <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </a>
        </div>
        <div class="p-5">
            @include('partials.video-embed')
        </div>
    </div>

    {{-- ===================== ADD WALLET FORM ===================== --}}
    <div class="rounded-2xl border border-dark-200 dark:border-dark-200 border-light-300 bg-dark-50 dark:bg-dark-50 bg-white overflow-hidden">
        <div class="flex items-center justify-between gap-4 p-6 border-b border-dark-200 dark:border-dark-200 border-light-300">
            <div>
                <h2 class="text-xl font-bold dark:text-white text-dark font-display">Add Wallet Address</h2>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-semibold text-dark">
                <i data-lucide="shield-check" class="h-4 w-4"></i> Secure • No recovery phrases
            </span>
        </div>

        <form method="POST" action="{{ route('wallet.store') }}" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">Wallet Provider</label>
                <select name="provider" required
                        class="w-full px-4 py-3 rounded-lg bg-dark-100 dark:bg-dark-100 bg-light-50 border border-dark-200 dark:border-dark-200 border-light-300 dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors">
                    <option value="" disabled selected>Select Provider</option>
                    <option value="MetaMask">MetaMask</option>
                    <option value="Trust Wallet">Trust Wallet</option>
                    <option value="Coinbase Wallet">Coinbase Wallet</option>
                    <option value="Binance Wallet">Binance Wallet</option>
                    <option value="Exodus">Exodus</option>
                    <option value="Ledger">Ledger</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">Network (optional)</label>
                <input type="text" name="network" value="{{ old('network') }}"
                       placeholder="e.g. Ethereum, BSC"
                       class="w-full px-4 py-3 rounded-lg bg-dark-100 dark:bg-dark-100 bg-light-50 border border-dark-200 dark:border-dark-200 border-light-300 dark:text-white text-dark placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">Public Wallet Address</label>
                <textarea name="address" rows="3" required
                          placeholder="Paste your public wallet address here"
                          class="w-full px-4 py-3 rounded-lg bg-dark-100 dark:bg-dark-100 bg-light-50 border border-dark-200 dark:border-dark-200 border-light-300 dark:text-white text-dark placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors resize-none">{{ old('address') }}</textarea>
                <p class="mt-2 text-xs dark:text-gray-400 text-gray-500">
                    Only paste public addresses. Never share your recovery phrase or private key with any website or person.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">Label (optional)</label>
                <input type="text" name="label" value="{{ old('label') }}"
                       placeholder="e.g. Main Trust Wallet"
                       class="w-full px-4 py-3 rounded-lg bg-dark-100 dark:bg-dark-100 bg-light-50 border border-dark-200 dark:border-dark-200 border-light-300 dark:text-white text-dark placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-dark hover:bg-gray-100 transition-colors">
                    <i data-lucide="link" class="h-4 w-4"></i> Connect Wallet
                </button>
            </div>
        </form>
    </div>

    {{-- ===================== SECURITY REMINDER ===================== --}}
    <div class="rounded-2xl border border-dark-200 dark:border-dark-200 border-light-300 bg-dark-50 dark:bg-dark-50 bg-white p-6">
        <h3 class="text-lg font-bold dark:text-white text-dark mb-4 font-display">Security Reminder</h3>
        <ul class="space-y-3 text-sm dark:text-gray-400 text-gray-600">
            <li class="flex gap-3">
                <i data-lucide="alert-triangle" class="h-5 w-5 shrink-0 text-secondary"></i>
                Never enter your recovery phrase, seed phrase, or private key anywhere on this site.
            </li>
            <li class="flex gap-3">
                <i data-lucide="database" class="h-5 w-5 shrink-0 text-primary"></i>
                We only store public addresses to help identify your wallets.
            </li>
            <li class="flex gap-3">
                <i data-lucide="lock" class="h-5 w-5 shrink-0 text-primary"></i>
                Your funds remain in your own wallets; this is not a custodial transfer.
            </li>
        </ul>
    </div>

    {{-- ===================== CONNECTED WALLETS LIST ===================== --}}
    <div class="rounded-2xl border border-dark-200 dark:border-dark-200 border-light-300 bg-dark-50 dark:bg-dark-50 bg-white overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-dark-200 dark:border-dark-200 border-light-300">
            <h3 class="text-lg font-bold dark:text-white text-dark font-display">Connected Wallets</h3>
            <span class="text-sm dark:text-gray-400 text-gray-500">{{ $wallets->count() }} saved</span>
        </div>

        @forelse($wallets as $wallet)
        <div class="flex items-center gap-4 p-5 border-b border-dark-200 dark:border-dark-200 border-light-300 last:border-b-0">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-primary/15 text-primary font-bold">
                {{ strtoupper(substr($wallet->provider, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold dark:text-white text-dark truncate">{{ $wallet->label ?: $wallet->provider }}</p>
                <p class="text-xs dark:text-gray-400 text-gray-500">{{ $wallet->network ?: 'Network not specified' }}</p>
                <p class="text-xs dark:text-gray-500 text-gray-500 font-mono truncate">{{ $wallet->address }}</p>
                <div class="mt-1">
                    @if($wallet->status === 'approved')
                        <span class="inline-flex items-center rounded-md bg-secondary/15 px-2 py-0.5 text-xs font-semibold text-secondary">Connected</span>
                    @elseif($wallet->status === 'rejected')
                        <span class="inline-flex items-center rounded-md bg-danger/15 px-2 py-0.5 text-xs font-semibold text-danger">Rejected</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-amber-500/15 px-2 py-0.5 text-xs font-semibold text-amber-400">Waiting for approval</span>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('wallet.destroy', $wallet->id) }}"
                  onsubmit="return confirm('Remove this wallet?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-semibold text-danger hover:underline">Remove</button>
            </form>
        </div>
        @empty
        <div class="p-8 text-center text-sm dark:text-gray-400 text-gray-500">
            No wallets connected yet. Add one above to start earning daily rewards.
        </div>
        @endforelse
    </div>

</div>

<script> if (window.lucide) lucide.createIcons(); </script>
@endsection
