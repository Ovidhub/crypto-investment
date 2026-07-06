@extends('layouts.dash')
@section('title', $title)
@section('content')

<div x-data="{ 
    showModal: false, 
    selectedSignal: { id: '', name: '', price: '' },
    selectedMethod: '',
    openSubscriptionModal(id, name, price) {
        this.selectedSignal = { id, name, price };
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
        this.selectedMethod = '';
    }
}" class="pb-20">

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold dark:text-white text-slate-900 mb-2">
                    <i data-lucide="signal" class="w-8 h-8 inline mr-3 text-primary"></i>
                    Premium Trading Signals
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-lg">Subscribe to professional signals and enhance your trading success.</p>
            </div>
            <div class="bg-white dark:bg-dark-50 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-dark-200">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <i data-lucide="trending-up" class="w-5 h-5 text-primary"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold dark:text-white">{{ count($signals) }}</p>
                        <p class="text-sm text-slate-500">Available Signals</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-danger-alert />
    <x-success-alert />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($signals as $signal)
        <div class="group relative bg-white dark:bg-dark-50 rounded-2xl shadow-sm border border-slate-200 dark:border-dark-200 hover:shadow-xl hover:border-primary/50 transition-all duration-300 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-primary/10 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="radio" class="w-6 h-6 text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold dark:text-white">{{ $signal->name }}</h3>
                </div>

                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-3xl font-black dark:text-white">{{ $settings->currency }}{{ number_format($signal->price, 2) }}</span>
                        <span class="text-sm text-slate-400">/mo</span>
                    </div>
                </div>

                <div class="space-y-3 mb-8">
                    <div class="flex items-center gap-2 text-sm dark:text-slate-300">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                        <span>Win Rate: {{ $signal->increment_amount }}%</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm dark:text-slate-300">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                        <span>24/7 Real-time Alerts</span>
                    </div>
                </div>

                <button @click="openSubscriptionModal('{{ $signal->id }}', '{{ $signal->name }}', '{{ $signal->price }}')"
                        class="w-full py-4 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                    <i data-lucide="zap" class="w-4 h-4"></i> Subscribe Now
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center bg-white dark:bg-dark-50 rounded-3xl border-2 border-dashed border-slate-200 dark:border-dark-200">
             <i data-lucide="signal-off" class="w-16 h-16 mx-auto mb-4 text-slate-300"></i>
             <p class="text-slate-500 font-bold">No trading signals available at the moment.</p>
        </div>
        @endforelse
    </div>

    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal()"></div>
        
        <div class="relative bg-white dark:bg-dark-50 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-in zoom-in-95">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-black dark:text-white">Subscribe</h3>
                    <button @click="closeModal()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('newdeposit') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="asset" :value="selectedSignal.name">
                    
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Selected Signal</label>
                        <p class="text-xl font-bold dark:text-white" x-text="selectedSignal.name"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-4">
                            <i data-lucide="credit-card" class="w-3 h-3 inline mr-1"></i> Choose Payment Method
                        </label>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($dmethods as $method)
                            <div @click="selectedMethod = '{{ $method->name }}'" 
                                 :class="selectedMethod === '{{ $method->name }}' ? 'ring-2 ring-primary bg-primary/5 border-primary' : 'bg-slate-50 dark:bg-dark-100 border-transparent'"
                                 class="relative p-4 rounded-2xl cursor-pointer border transition-all hover:shadow-md group">
                                
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white dark:bg-dark-200 rounded-lg shadow-sm group-hover:scale-110 transition-transform">
                                        <i data-lucide="wallet-2" class="w-5 h-5 text-primary"></i>
                                    </div>
                                    <span class="font-bold text-sm dark:text-white text-slate-900">{{ $method->name }}</span>
                                </div>

                                <div x-show="selectedMethod === '{{ $method->name }}'" class="absolute top-2 right-2">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-primary"></i>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="payment_method" :value="selectedMethod" required>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Amount</label>
                        <input type="text" name="amount" :value="selectedSignal.price" readonly class="w-full p-4 rounded-xl bg-slate-50 dark:bg-dark-100 dark:text-white font-bold text-lg">
                    </div>

                    <button type="submit" 
                            :disabled="!selectedMethod"
                            :class="!selectedMethod ? 'opacity-50 cursor-not-allowed' : 'hover:scale-[1.02]'"
                            class="w-full py-4 bg-primary text-white font-black uppercase tracking-widest rounded-xl shadow-xl shadow-primary/30 transition-all">
                        Complete Purchase
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection