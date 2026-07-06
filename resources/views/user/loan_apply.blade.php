@extends('layouts.dash')
@section('title', $title)
@section('content')

<div x-data="{ 
    loanAmount: 1000, 
    duration: '', 
    monthlyPayment: 0, 
    acceptedTerms: false,
    calculateMonthlyPayment() {
        if (this.loanAmount >= 1000 && this.duration > 0) {
            let annualRate = 0.085; // 8.5% Interest
            let monthlyRate = annualRate / 12;
            let n = parseInt(this.duration);
            this.monthlyPayment = (this.loanAmount * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -n));
        } else {
            this.monthlyPayment = 0;
        }
    }
}" class="pb-12">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold dark:text-white text-slate-900">
            <i data-lucide="credit-card" class="inline-block w-8 h-8 mr-2 text-primary"></i> Loan Application
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2">Apply for competitive rates and flexible repayment terms.</p>
    </div>

    <x-danger-alert />
    <x-success-alert />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-dark-50 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-dark-200 text-center">
            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl w-fit mx-auto mb-4">
                <i data-lucide="percent" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <h3 class="font-bold dark:text-white">5.5% APR</h3>
            <p class="text-sm text-slate-500">Starting interest rates</p>
        </div>
        <div class="bg-white dark:bg-dark-50 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-dark-200 text-center">
            <div class="p-3 bg-primary/10 rounded-xl w-fit mx-auto mb-4">
                <i data-lucide="clock" class="w-6 h-6 text-primary"></i>
            </div>
            <h3 class="font-bold dark:text-white">Quick Approval</h3>
            <p class="text-sm text-slate-500">24-48 hours processing</p>
        </div>
        <div class="bg-white dark:bg-dark-50 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-dark-200 text-center">
            <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-xl w-fit mx-auto mb-4">
                <i data-lucide="shield-check" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
            </div>
            <h3 class="font-bold dark:text-white">Secure</h3>
            <p class="text-sm text-slate-500">Fully encrypted process</p>
        </div>
    </div>

    <div class="bg-white dark:bg-dark-50 rounded-2xl shadow-lg border border-slate-200 dark:border-dark-200 overflow-hidden">
        <div class="bg-primary px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5"></i> Loan Application Form
            </h2>
        </div>

        <form action="{{ route('loan.store') }}" method="POST" class="p-6 space-y-8">
            @csrf
            
            <div class="space-y-4">
                <h3 class="font-bold dark:text-white border-b dark:border-dark-200 pb-2">Loan Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Loan Amount ({{ $settings->currency }}) *</label>
                        <input type="number" name="amount" x-model="loanAmount" @input="calculateMonthlyPayment()" required min="1000" class="w-full p-3 rounded-xl border dark:bg-dark-100 dark:text-white dark:border-dark-200 outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Duration *</label>
                        <select name="duration" x-model="duration" @change="calculateMonthlyPayment()" required class="w-full p-3 rounded-xl border dark:bg-dark-100 dark:text-white dark:border-dark-200 outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select Duration</option>
                            <option value="6">6 Months</option>
                            <option value="12">12 Months</option>
                            <option value="24">24 Months</option>
                            <option value="36">36 Months</option>
                        </select>
                    </div>
                </div>

                <div x-show="monthlyPayment > 0" x-transition class="bg-primary/5 rounded-xl p-4 border border-primary/20">
                    <p class="text-sm text-primary font-bold">Estimated Monthly Payment</p>
                    <p class="text-3xl font-black text-primary" x-text="'{{ $settings->currency }}' + monthlyPayment.toFixed(2)"></p>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="font-bold dark:text-white border-b dark:border-dark-200 pb-2">Credit Facility</h3>
                <select name="facility" required class="w-full p-3 rounded-xl border dark:bg-dark-100 dark:text-white dark:border-dark-200">
                    <option value="">Select Loan Type</option>
                    <option value="Personal Home Loans">🏠 Personal Home Loans</option>
                    <option value="Automobile Loans">🚗 Automobile Loans</option>
                    <option value="Salary loans">💼 Salary Loans</option>
                    <option value="StartUp Financing">🚀 StartUp Financing</option>
                </select>
            </div>

            <div class="space-y-4">
                <h3 class="font-bold dark:text-white border-b dark:border-dark-200 pb-2">Financial Information</h3>
                <div>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Monthly Net Income *</label>
                    <select name="income" required class="w-full p-3 rounded-xl border dark:bg-dark-100 dark:text-white dark:border-dark-200">
                        <option value="">Select Range</option>
                        <option value="2000-5000">$2,000 - $5,000</option>
                        <option value="5000+">$5,000+</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Purpose of Loan *</label>
                    <textarea name="purpose" required rows="3" class="w-full p-3 rounded-xl border dark:bg-dark-100 dark:text-white dark:border-dark-200 outline-none focus:ring-2 focus:ring-primary" placeholder="Details..."></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-slate-50 dark:bg-dark-100 p-4 rounded-xl">
                <input type="checkbox" id="terms" x-model="acceptedTerms" required class="w-5 h-5 text-primary rounded">
                <label for="terms" class="text-sm dark:text-white">I accept the <a href="#" class="text-primary hover:underline">terms and conditions</a></label>
            </div>

            <button type="submit" :disabled="!acceptedTerms" 
                class="w-full py-4 rounded-xl font-bold text-white transition-all shadow-lg flex items-center justify-center gap-2"
                :class="acceptedTerms ? 'bg-primary hover:scale-[1.02]' : 'bg-slate-400 cursor-not-allowed'">
                <i data-lucide="send" class="w-5 h-5"></i> Submit Application
            </button>
        </form>
    </div>
</div>

@endsection