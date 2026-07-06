@extends('layouts.dash')
@section('title', $title)
@section('content')
    <!-- Page Header -->
    <div class="relative mb-8">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-secondary/20 rounded-3xl -z-10 blur-xl opacity-50"></div>
        <div class="px-6 py-8 rounded-3xl bg-gradient-to-r from-primary/5 to-secondary/5 backdrop-blur-sm border border-white/10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold dark:text-white text-dark">Investment Hub</h1>
                    <p class="mt-2 text-base dark:text-gray-300 text-gray-600">Grow your wealth with our curated investment plans</p>
                </div>
                <div>
                    <div class="flex items-center gap-3 px-4 py-2 rounded-full bg-white/10 dark:bg-dark-50/50 backdrop-blur-sm border border-white/20 dark:border-dark-200/50">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary/20 text-primary">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <p class="text-xs dark:text-gray-400 text-gray-500">Your Balance</p>
                            <p class="text-lg font-semibold dark:text-white text-dark">{{ $settings->currency }}{{ number_format(Auth::user()->account_bal) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Investment Plans Section -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Investment Plans Card -->
            <div class="rounded-2xl overflow-hidden bg-white dark:bg-dark-50 shadow-xl shadow-primary/5 dark:shadow-primary/10 border border-light-200/50 dark:border-dark-200/50">
                <div class="p-6 border-b border-light-200/50 dark:border-dark-200/50">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h2 class="text-xl font-semibold dark:text-white text-dark flex items-center">
                            <i class="fas fa-chart-line mr-2 text-primary"></i>
                            Curated Investment Plans
                        </h2>
                        
                        <!-- Market Pulse Indicator -->
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 dark:bg-primary/10">
                            <div class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-xs font-medium text-primary">Markets active</span>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Selection Component -->
                <div class="p-6">
                    <livewire:user.investment-plan />
                </div>
            </div>
            
            <!-- Investment Insights Card -->
            <div class="rounded-2xl overflow-hidden bg-white dark:bg-dark-50 shadow-xl shadow-primary/5 dark:shadow-primary/10 border border-light-200/50 dark:border-dark-200/50">
                <div class="p-6 border-b border-light-200/50 dark:border-dark-200/50">
                    <h2 class="text-xl font-semibold dark:text-white text-dark flex items-center">
                        <i data-lucide="trending-up" class="h-5 w-5 mr-2 text-primary"></i>
                        Investment Insights
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Performance Card -->
                        <div class="rounded-xl overflow-hidden p-4 border border-light-200/50 dark:border-dark-200/50 bg-gradient-to-br from-white/50 to-white/20 dark:from-dark-50/50 dark:to-dark-50/20">
                            <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-green-100 dark:bg-green-900/30 mb-4">
                                <i class="fas fa-chart-bar text-green-600 dark:text-green-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold dark:text-white text-dark mb-2">Performance</h3>
                            <p class="text-sm dark:text-gray-300 text-gray-600 mb-4">
                                Our investment plans have consistently outperformed market benchmarks.
                            </p>
                            <div class="flex items-center">
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">+18.7%</span>
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">avg. annual return</span>
                            </div>
                        </div>
                        
                        <!-- Risk Level Card -->
                        <div class="rounded-xl overflow-hidden p-4 border border-light-200/50 dark:border-dark-200/50 bg-gradient-to-br from-white/50 to-white/20 dark:from-dark-50/50 dark:to-dark-50/20">
                            <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 mb-4">
                                <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold dark:text-white text-dark mb-2">Risk Management</h3>
                            <p class="text-sm dark:text-gray-300 text-gray-600 mb-4">
                                Our diversified approach minimizes risk while maximizing returns.
                            </p>
                            <div class="w-full bg-gray-200 dark:bg-dark-200 rounded-full h-2.5">
                                <div class="bg-blue-600 dark:bg-blue-400 h-2.5 rounded-full" style="width: 35%"></div>
                            </div>
                            <div class="flex justify-between mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span>Low</span>
                                <span>Moderate</span>
                                <span>High</span>
                            </div>
                        </div>
                        
                        <!-- Liquidity Card -->
                        <div class="rounded-xl overflow-hidden p-4 border border-light-200/50 dark:border-dark-200/50 bg-gradient-to-br from-white/50 to-white/20 dark:from-dark-50/50 dark:to-dark-50/20">
                            <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 mb-4">
                                <i class="fas fa-clock text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold dark:text-white text-dark mb-2">Liquidity</h3>
                            <p class="text-sm dark:text-gray-300 text-gray-600 mb-4">
                                Access your funds on your terms with flexible withdrawal options.
                            </p>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span class="text-sm dark:text-white text-dark">Withdraw anytime</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Investment Journey + FAQ -->
        <div class="space-y-6">
            <!-- Your Investment Journey Card -->
            <div class="rounded-2xl overflow-hidden bg-white dark:bg-dark-50 shadow-xl shadow-primary/5 dark:shadow-primary/10 border border-light-200/50 dark:border-dark-200/50">
                <div class="p-6 border-b border-light-200/50 dark:border-dark-200/50">
                    <h2 class="text-xl font-semibold dark:text-white text-dark flex items-center">
                        <i class="fas fa-map mr-2 text-primary"></i>
                        Your Investment Journey
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-8">
                        <!-- Step 1 -->
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-0 flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-sm font-bold">1</div>
                            <div class="absolute left-3 top-6 w-px h-16 bg-gray-200 dark:bg-dark-200"></div>
                            <h3 class="font-semibold dark:text-white text-dark text-lg mb-2">Choose Your Plan</h3>
                            <p class="dark:text-gray-300 text-gray-600 text-sm">
                                Select from our range of carefully crafted investment plans that align with your financial goals and risk tolerance.
                            </p>
                        </div>
                        
                        <!-- Step 2 -->
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-0 flex items-center justify-center w-6 h-6 rounded-full bg-secondary text-white text-sm font-bold">2</div>
                            <div class="absolute left-3 top-6 w-px h-16 bg-gray-200 dark:bg-dark-200"></div>
                            <h3 class="font-semibold dark:text-white text-dark text-lg mb-2">Fund Your Investment</h3>
                            <p class="dark:text-gray-300 text-gray-600 text-sm">
                                Deposit funds into your selected plan using our secure payment methods. Start with as little as {{ $settings->currency }}100.
                            </p>
                        </div>
                        
                        <!-- Step 3 -->
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-0 flex items-center justify-center w-6 h-6 rounded-full bg-tertiary text-white text-sm font-bold">3</div>
                            <h3 class="font-semibold dark:text-white text-dark text-lg mb-2">Track & Grow</h3>
                            <p class="dark:text-gray-300 text-gray-600 text-sm">
                                Monitor your portfolio's performance in real-time and watch your wealth grow with our competitive returns.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Card -->
            <div class="rounded-2xl overflow-hidden bg-white dark:bg-dark-50 shadow-xl shadow-primary/5 dark:shadow-primary/10 border border-light-200/50 dark:border-dark-200/50">
                <div class="p-6 border-b border-light-200/50 dark:border-dark-200/50">
                    <h2 class="text-xl font-semibold dark:text-white text-dark flex items-center">
                        <i class="fas fa-question-circle mr-2 text-primary"></i>
                        Common Questions
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- FAQ Item 1 -->
                        <div x-data="{ open: false }" class="rounded-xl overflow-hidden border border-light-200/50 dark:border-dark-200/50">
                            <button 
                                @click="open = !open" 
                                class="flex justify-between items-center w-full px-4 py-3 text-left dark:text-white text-dark font-medium hover:bg-light-100/50 dark:hover:bg-dark-100/50 transition-colors"
                            >
                                <span>How are returns calculated?</span>
                                <i class="fas fa-chevron-down dark:text-gray-400 text-gray-500 transition-transform duration-200" :class="{'transform rotate-180': open}"></i>
                            </button>
                            <div x-show="open" x-cloak x-collapse>
                                <div class="px-4 pb-4 pt-0 dark:text-gray-300 text-gray-700 text-sm">
                                    Returns are calculated based on the performance of underlying assets and market conditions. Each plan has a specified return rate, which may be fixed or variable depending on the plan you choose.
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 2 -->
                        <div x-data="{ open: false }" class="rounded-xl overflow-hidden border border-light-200/50 dark:border-dark-200/50">
                            <button 
                                @click="open = !open" 
                                class="flex justify-between items-center w-full px-4 py-3 text-left dark:text-white text-dark font-medium hover:bg-light-100/50 dark:hover:bg-dark-100/50 transition-colors"
                            >
                                <span>How secure are my investments?</span>
                                <i data-lucide="chevron-down" class="h-5 w-5 dark:text-gray-400 text-gray-500 transition-transform duration-200" :class="{'transform rotate-180': open}"></i>
                            </button>
                            <div x-show="open" x-cloak x-collapse>
                                <div class="px-4 pb-4 pt-0 dark:text-gray-300 text-gray-700 text-sm">
                                    Your investments are protected by our robust security measures and risk management strategies. We employ diversification, market hedging, and liquidity reserves to ensure your capital is safeguarded.
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 3 -->
                        <div x-data="{ open: false }" class="rounded-xl overflow-hidden border border-light-200/50 dark:border-dark-200/50">
                            <button 
                                @click="open = !open" 
                                class="flex justify-between items-center w-full px-4 py-3 text-left dark:text-white text-dark font-medium hover:bg-light-100/50 dark:hover:bg-dark-100/50 transition-colors"
                            >
                                <span>Can I withdraw before the term ends?</span>
                                <i data-lucide="chevron-down" class="h-5 w-5 dark:text-gray-400 text-gray-500 transition-transform duration-200" :class="{'transform rotate-180': open}"></i>
                            </button>
                            <div x-show="open" x-cloak x-collapse>
                                <div class="px-4 pb-4 pt-0 dark:text-gray-300 text-gray-700 text-sm">
                                    Yes, most of our plans allow early withdrawals. However, early withdrawal may affect your returns. Please check the specific terms of each investment plan for details on withdrawal policies.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection