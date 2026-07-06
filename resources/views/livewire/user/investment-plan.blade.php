<div>
    @if (count($plans) > 0)
        <div class="space-y-6">
            <!-- Plan Selection Area -->
            <div class="space-y-5">
                <!-- Plan Selection Cards Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3" x-data="{ activePlan: '{{ $planSelected ? $planSelected->id : 0 }}' }">
                    @foreach ($plans as $index => $plan)
                        <button 
                            wire:click="selectPlan({{ $plan->id }})"
                            @click="activePlan = '{{ $plan->id }}'"
                            class="group relative rounded-2xl transition-all duration-300 focus:outline-none transform hover:scale-105 hover:shadow-2xl"
                            :class="activePlan == '{{ $plan->id }}' 
                                ? 'bg-gradient-to-br from-primary to-primary/80 text-white shadow-xl scale-105' 
                                : 'bg-white dark:bg-dark-50 border border-light-200 dark:border-dark-200 text-gray-700 dark:text-gray-300 hover:border-primary/30'"
                        >
                            <!-- Card Content -->
                            <div class="p-5 space-y-3">
                                <!-- Icon -->
                                <div class="flex justify-center">
                                    <div class="relative flex items-center justify-center w-14 h-14 rounded-2xl transition-all duration-300" 
                                        :class="activePlan == '{{ $plan->id }}' 
                                            ? 'bg-white/20 backdrop-blur-sm' 
                                            : 'bg-gradient-to-br from-primary/10 to-primary/5 group-hover:from-primary/20 group-hover:to-primary/10'">
                                        <i class="fas fa-chart-line text-2xl"
                                            :class="activePlan == '{{ $plan->id }}' ? 'text-white' : 'text-primary'"></i>
                                        <!-- Active indicator badge -->
                                        <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full transition-all duration-200"
                                            :class="activePlan == '{{ $plan->id }}' ? 'bg-green-400 animate-pulse' : 'bg-transparent'"></div>
                                    </div>
                                </div>
                                
                                <!-- Plan Name -->
                                <div class="text-center">
                                    <h4 class="font-bold text-base mb-1 line-clamp-2 min-h-[2.5rem]" 
                                        :class="activePlan == '{{ $plan->id }}' ? 'text-white' : 'text-dark dark:text-white'">
                                        {{ $plan->name }}
                                    </h4>
                                    <p class="text-xs font-medium" 
                                        :class="activePlan == '{{ $plan->id }}' ? 'text-white/80' : 'text-gray-500 dark:text-gray-400'">
                                        From {{ $settings->currency }}{{ number_format($plan->min_price) }}
                                    </p>
                                </div>
                                
                                <!-- ROI Badge -->
                                <div class="flex justify-center">
                                    <div class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200"
                                        :class="activePlan == '{{ $plan->id }}' 
                                            ? 'bg-white/20 text-white backdrop-blur-sm' 
                                            : 'bg-green-500/10 text-green-600 dark:text-green-400 group-hover:bg-green-500/20'">
                                        {{ $plan->minr }}% - {{ $plan->maxr }}%
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Selected indicator border glow -->
                            <div class="absolute inset-0 rounded-2xl transition-all duration-300 pointer-events-none"
                                :class="activePlan == '{{ $plan->id }}' ? 'ring-2 ring-primary ring-offset-2 dark:ring-offset-dark' : ''"></div>
                        </button>
                    @endforeach
                </div>

                <!-- Plan Details Card -->
                @if ($planSelected)
                <div class="rounded-xl overflow-hidden border border-light-200/50 dark:border-dark-200/50 bg-gradient-to-br from-white/50 to-white/20 dark:from-dark-50/50 dark:to-dark-50/20 p-5">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div>
                            <h3 class="font-semibold text-xl dark:text-white text-dark">{{ $planSelected->name }}</h3>
                            <p class="text-sm dark:text-gray-400 text-gray-600 mt-1">
                                {{ $planSelected->expiration }} plan with 
                                @if ($planSelected->increment_type == 'Fixed')
                                    {{ $settings->currency }}{{ $planSelected->increment_amount }} {{ $planSelected->increment_interval }}
                                @else
                                    {{ $planSelected->increment_amount }}% {{ $planSelected->increment_interval }}
                                @endif
                                returns
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <div class="rounded-lg px-3 py-1.5 bg-primary/10 text-primary text-xs font-medium">
                                Min: {{ $settings->currency }}{{ number_format($planSelected->min_price) }}
                            </div>
                            <div class="rounded-lg px-3 py-1.5 bg-primary/10 text-primary text-xs font-medium">
                                Max: {{ $settings->currency }}{{ number_format($planSelected->max_price) }}
                            </div>
                            <div class="rounded-lg px-3 py-1.5 bg-green-500/10 text-green-500 text-xs font-medium">
                                {{ $planSelected->minr }}% - {{ $planSelected->maxr }}% ROI
                            </div>
                        </div>
                    </div>

                    <!-- Plan Benefits -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <i class="fas fa-calendar text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <p class="text-xs dark:text-gray-400 text-gray-500">Duration</p>
                                <p class="text-sm font-medium dark:text-white text-dark">{{ $planSelected->expiration }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <i class="fas fa-chart-bar text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <p class="text-xs dark:text-gray-400 text-gray-500">Return Rate</p>
                                <p class="text-sm font-medium dark:text-white text-dark">
                                    @if ($planSelected->increment_type == 'Fixed')
                                        {{ $settings->currency }}{{ $planSelected->increment_amount }} {{ $planSelected->increment_interval }}
                                    @else
                                        {{ $planSelected->increment_amount }}% {{ $planSelected->increment_interval }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <i class="fas fa-gift text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div>
                                <p class="text-xs dark:text-gray-400 text-gray-500">Bonus</p>
                                <p class="text-sm font-medium dark:text-white text-dark">{{ $settings->currency }}{{ $planSelected->gift }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- Investment Configuration -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Amount Selection Column -->
                <div class="space-y-6">
                    <!-- Quick Amount Selection -->
                    <div>
                        <p class="text-sm font-medium dark:text-white text-dark mb-3">
                            Quick Amount Selection
                        </p>
                        <div class="grid grid-cols-3 gap-2">
                            <button class="py-3 rounded-xl text-center transition-all duration-200 hover:scale-105 hover:shadow-md dark:text-white text-dark
                                        border border-light-200/50 dark:border-dark-200/50 hover:border-primary dark:hover:border-primary"
                                wire:click="selectAmount('100')">{{ $settings->currency }}100</button>
                            <button class="py-3 rounded-xl text-center transition-all duration-200 hover:scale-105 hover:shadow-md dark:text-white text-dark
                                        border border-light-200/50 dark:border-dark-200/50 hover:border-primary dark:hover:border-primary"
                                wire:click="selectAmount('250')">{{ $settings->currency }}250</button>
                            <button class="py-3 rounded-xl text-center transition-all duration-200 hover:scale-105 hover:shadow-md dark:text-white text-dark
                                        border border-light-200/50 dark:border-dark-200/50 hover:border-primary dark:hover:border-primary"
                                wire:click="selectAmount('500')">{{ $settings->currency }}500</button>
                            <button class="py-3 rounded-xl text-center transition-all duration-200 hover:scale-105 hover:shadow-md dark:text-white text-dark
                                        border border-light-200/50 dark:border-dark-200/50 hover:border-primary dark:hover:border-primary"
                                wire:click="selectAmount('1000')">{{ $settings->currency }}1K</button>
                            <button class="py-3 rounded-xl text-center transition-all duration-200 hover:scale-105 hover:shadow-md dark:text-white text-dark
                                        border border-light-200/50 dark:border-dark-200/50 hover:border-primary dark:hover:border-primary"
                                wire:click="selectAmount('2000')">{{ $settings->currency }}2K</button>
                            <button class="py-3 rounded-xl text-center transition-all duration-200 hover:scale-105 hover:shadow-md dark:text-white text-dark
                                        border border-light-200/50 dark:border-dark-200/50 hover:border-primary dark:hover:border-primary"
                                wire:click="selectAmount('5000')">{{ $settings->currency }}5K</button>
                        </div>
                    </div>

                    <!-- Custom Amount Input -->
                    <div>
                        <p class="text-sm font-medium dark:text-white text-dark mb-3">
                            Or Enter Custom Amount
                        </p>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 text-lg font-medium">{{ $settings->currency }}</span>
                            </div>
                            <input 
                                type="number" 
                                required 
                                wire:model='amountToInvest'
                                wire:keyup="checkIfAmountIsEmpty" 
                                class="block w-full pl-10 pr-4 py-4 text-lg font-medium rounded-xl bg-light-100/50 dark:bg-dark-100/50 border border-light-200/50 dark:border-dark-200/50 focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white text-dark transition-all duration-200" 
                                placeholder="Enter amount"
                                min="{{ $planSelected ? $planSelected->min_price : '0' }}"
                                max="{{ $planSelected ? $planSelected->max_price : '10000000000' }}"
                            >
                        </div>
                        
                        <!-- Min/Max Slider (if plan selected) -->
                        @if ($planSelected && $planSelected->min_price < $planSelected->max_price)
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span>{{ $settings->currency }}{{ number_format($planSelected->min_price) }}</span>
                                <span>{{ $settings->currency }}{{ number_format($planSelected->max_price) }}</span>
                            </div>
                            <div class="w-full h-1 bg-gray-200 dark:bg-dark-200 rounded-full overflow-hidden">
                                @if ($amountToInvest > 0 && $planSelected->max_price > 0)
                                    @php
                                        $percentage = min(100, max(0, ($amountToInvest / $planSelected->max_price) * 100));
                                    @endphp
                                    <div class="h-full bg-primary rounded-full" style="width: {{ $percentage }}%"></div>
                                @else
                                    <div class="h-full bg-primary rounded-full" style="width: 0%"></div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Expected Returns Preview (if amount and plan selected) -->
                    @if ($planSelected && $amountToInvest > 0)
                    <div class="rounded-xl overflow-hidden border border-light-200/50 dark:border-dark-200/50 bg-light-100/50 dark:bg-dark-100/50 p-4">
                        <p class="text-sm font-medium dark:text-white text-dark mb-3">
                            Estimated Returns
                        </p>
                        
                        <div class="space-y-2">
                            @php
                                // Calculate estimated returns based on plan type and duration
                                $duration = trim(preg_replace('/[^0-9]/', '', $planSelected->expiration));
                                $durationUnit = strpos(strtolower($planSelected->expiration), 'day') !== false ? 'days' : 
                                               (strpos(strtolower($planSelected->expiration), 'week') !== false ? 'weeks' : 
                                               (strpos(strtolower($planSelected->expiration), 'month') !== false ? 'months' : 'years'));
                                
                                // Calculate total return percentage based on min and max returns
                                $avgReturnPercentage = ($planSelected->minr + $planSelected->maxr) / 2;
                                $totalReturn = $amountToInvest * ($avgReturnPercentage / 100);
                                
                                // Calculate total profit (return + bonus)
                                $totalProfit = $totalReturn + $planSelected->gift;
                                
                                // Calculate final amount
                                $finalAmount = $amountToInvest + $totalProfit;
                            @endphp
                            
                            <div class="flex justify-between items-center text-sm">
                                <span class="dark:text-gray-400 text-gray-600">Initial Investment:</span>
                                <span class="font-medium dark:text-white text-dark">{{ $settings->currency }}{{ number_format($amountToInvest, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center text-sm">
                                <span class="dark:text-gray-400 text-gray-600">Est. Return ({{ $avgReturnPercentage }}%):</span>
                                <span class="font-medium text-green-500">+{{ $settings->currency }}{{ number_format($totalReturn, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center text-sm">
                                <span class="dark:text-gray-400 text-gray-600">Bonus:</span>
                                <span class="font-medium text-green-500">+{{ $settings->currency }}{{ number_format($planSelected->gift, 2) }}</span>
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-light-200/50 dark:border-dark-200/50">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium dark:text-white text-dark">Total Value:</span>
                                    <span class="font-bold text-lg text-primary">{{ $settings->currency }}{{ number_format($finalAmount, 2) }}</span>
                                </div>
                                <div class="text-xs text-center dark:text-gray-400 text-gray-600 mt-1">
                                    After {{ $duration }} {{ $durationUnit }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Payment Method Column -->
                <div class="space-y-6">
                    <!-- Payment Method Selection -->
                    <div>
                        <p class="text-sm font-medium dark:text-white text-dark mb-3">
                            Payment Method
                        </p>
                        
                        <div class="space-y-3">
                            <!-- Account Balance Option -->
                            <div 
                                class="relative rounded-xl overflow-hidden border-2 transition-all duration-200 cursor-pointer {{ $paymentMethod == 'Account Balance' ? 'border-primary dark:border-primary shadow-lg shadow-primary/10' : 'border-light-200/50 dark:border-dark-200/50 hover:border-primary/50 dark:hover:border-primary/50' }}"
                                wire:click="chanegePaymentMethod('Account Balance')"
                            >
                                <div class="flex items-center gap-4 p-4">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $paymentMethod == 'Account Balance' ? 'bg-primary text-white' : 'bg-primary/10 text-primary' }} flex items-center justify-center">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium dark:text-white text-dark">Account Balance</p>
                                        <p class="text-sm dark:text-gray-400 text-gray-600">
                                            {{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2) }} available
                                        </p>
                                    </div>
                                    @if ($paymentMethod == 'Account Balance')
                                        <div class="absolute right-3 top-3">
                                            <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Available Balance Progress Bar -->
                                <div class="px-4 pb-4">
                                    @if (Auth::user()->account_bal > 0 && $amountToInvest > 0)
                                        @php
                                            $balancePercentage = min(100, max(0, ($amountToInvest / Auth::user()->account_bal) * 100));
                                        @endphp
                                        <div class="w-full h-1.5 bg-gray-200 dark:bg-dark-200 rounded-full overflow-hidden">
                                            <div class="h-full {{ $balancePercentage > 90 ? 'bg-red-500' : 'bg-green-500' }} rounded-full" style="width: {{ $balancePercentage }}%"></div>
                                        </div>
                                        <div class="flex justify-end">
                                            <span class="text-xs mt-1 {{ $balancePercentage > 90 ? 'text-red-500' : 'text-green-500' }}">
                                                {{ $balancePercentage }}% of balance
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- You can uncomment and adapt this for other payment methods
                            <!-- BTC Wallet Option -->
                            <div 
                                class="relative rounded-xl overflow-hidden border-2 transition-all duration-200 cursor-pointer {{ $paymentMethod == 'BTC Wallet' ? 'border-primary dark:border-primary shadow-lg shadow-primary/10' : 'border-light-200/50 dark:border-dark-200/50 hover:border-primary/50 dark:hover:border-primary/50' }}"
                                wire:click="chanegePaymentMethod('BTC Wallet')"
                            >
                                <div class="flex items-center gap-4 p-4">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $paymentMethod == 'BTC Wallet' ? 'bg-orange-500 text-white' : 'bg-orange-500/10 text-orange-500' }} flex items-center justify-center">
                                        <i class="fab fa-bitcoin"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium dark:text-white text-dark">Bitcoin Wallet</p>
                                        <p class="text-sm dark:text-gray-400 text-gray-600">
                                            0.00038828 BTC available
                                        </p>
                                    </div>
                                    @if ($paymentMethod == 'BTC Wallet')
                                        <div class="absolute right-3 top-3">
                                            <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            --}}
                        </div>
                    </div>
                     <!-- Alerts -->
                <div>
                    <x-danger-alert />
                    <x-success-alert />
                </div>
                    
                    <!-- Investment Summary -->
                    <div class="rounded-xl overflow-hidden border border-light-200/50 dark:border-dark-200/50 bg-light-100/50 dark:bg-dark-100/50">
                        <div class="p-4 border-b border-light-200/50 dark:border-dark-200/50">
                            <p class="font-medium dark:text-white text-dark">Investment Summary</p>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm dark:text-gray-400 text-gray-600">Selected Plan:</span>
                                    <span class="text-sm font-medium dark:text-white text-dark">{{ $planSelected ? $planSelected->name : '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm dark:text-gray-400 text-gray-600">Investment Amount:</span>
                                    <span class="text-sm font-medium dark:text-white text-dark">{{ $settings->currency }}{{ $amountToInvest ? number_format($amountToInvest, 2) : '0.00' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm dark:text-gray-400 text-gray-600">Payment Method:</span>
                                    <span class="text-sm font-medium dark:text-white text-dark">{{ $paymentMethod ? $paymentMethod : '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm dark:text-gray-400 text-gray-600">Duration:</span>
                                    <span class="text-sm font-medium dark:text-white text-dark">{{ $planSelected ? $planSelected->expiration : '-' }}</span>
                                </div>
                            </div>
                            
                            
                            <!-- Invest Button -->
                            <div class="mt-4">
                                <form action="" wire:submit.prevent="joinPlan">
                                    <button 
                                        type="submit"
                                        class="w-full py-3 rounded-xl bg-gradient-to-r from-primary to-primary/90 text-white font-medium flex items-center justify-center gap-2 hover:shadow-lg hover:shadow-primary/20 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
                                        {{ $disabled }}
                                    >
                                        <i class="fas fa-chart-line"></i>
                                        Confirm & Invest Now
                                    </button>
                                </form>
                                <p class="mt-2 text-sm text-center text-primary" wire:loading wire:target="joinPlan">
                                    {{ $feedback }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Plans Available State -->
        <div class="rounded-xl overflow-hidden bg-gradient-to-br from-white/50 to-white/20 dark:from-dark-50/50 dark:to-dark-50/20 border border-light-200/50 dark:border-dark-200/50">
            <div class="p-8 text-center">
                <div class="w-20 h-20 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-circle fa-3x text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <h3 class="text-xl font-semibold dark:text-white text-dark mb-2">No Investment Plans Available</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-6">
                    We're currently updating our investment offerings to bring you better opportunities. Please check back soon or contact our support team for assistance.
                </p>
                <div class="flex flex-wrap gap-3 justify-center">
                    <button class="px-5 py-2.5 rounded-xl bg-primary text-white font-medium flex items-center gap-2 hover:bg-primary/90 transition-colors">
                        <i class="fas fa-comment"></i>
                        Contact Support
                    </button>
                    <button class="px-5 py-2.5 rounded-xl border border-light-200 dark:border-dark-200 dark:text-white text-dark font-medium flex items-center gap-2 hover:bg-light-100 dark:hover:bg-dark-100 transition-colors">
                        <i class="fas fa-sync-alt"></i>
                        Refresh Page
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>