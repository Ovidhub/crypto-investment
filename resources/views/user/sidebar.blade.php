<!-- Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[10000] hidden transition-opacity duration-300"></div>

<!-- Modern Sidebar -->
<div class="fixed inset-y-0 left-0 z-[10010] w-72 transform dark:bg-gradient-to-b dark:from-dark-50 dark:to-dark bg-gradient-to-b from-light-100 to-light overflow-y-auto transition-all duration-300 ease-in-out md:relative md:translate-x-0 -translate-x-full shadow-2xl" id="sidenav-main">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header with Branding -->
        <div class="flex h-16 items-center mt-4 dark:border-dark-100 border-light-200 border-b px-6">
           <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold text-xl">
            <img src="{{ asset('storage/app/public/' . $settings->logo) }}" alt="logo" class="h-8">
        </a>
            <div class="ml-auto md:hidden">
                <button class="dark:text-white text-dark-100 bg-transparent hover:text-primary transition-colors" id="closeSideNav">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
        </div>

        <!-- User Profile Section with Modern Design -->
        <div class="relative mt-6 px-4 mb-6">
            <div class="p-4 rounded-xl dark:bg-dark-100/50 bg-light-200/50 backdrop-blur-sm dark:border-dark-100/40 border-light-200/40 border relative overflow-hidden group">
                <!-- Decorative background elements -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-b from-primary/10 to-transparent rounded-full blur-2xl transform translate-x-8 -translate-y-8 group-hover:translate-y-0 transition-transform duration-700"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-t from-secondary/10 to-transparent rounded-full blur-2xl transform -translate-x-8 translate-y-8 group-hover:translate-y-0 transition-transform duration-700"></div>

                <!-- User info with better layout -->
                <div class="flex flex-col items-center relative">
                    <div class="relative mb-3">
                        <!-- Avatar with gradient ring -->
                        <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary rounded-full blur-[1px]"></div>
                        <div class="relative bg-primary h-16 w-16 rounded-full flex items-center justify-center text-xl font-bold dark:border-dark/80 border-light-300/80 border-2">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>

                        <!-- Status indicator -->
                        <div class="absolute bottom-0 right-0 h-4 w-4 bg-secondary rounded-full dark:border-dark border-light-300 border-2 shadow-lg"></div>
                    </div>

                    <h5 class="font-medium dark:text-white text-dark mb-0.5">{{ Auth::user()->name }}</h5>
                    <span class="text-xs dark:text-gray-400 text-gray-600 mb-3 dark:bg-dark-50/50 bg-light-200/50 px-2 py-0.5 rounded-full">online</span>

                    <!-- Balance display with card-like design -->
                    <div class="dark:bg-gradient-to-r dark:from-dark-50 dark:to-dark-100 bg-gradient-to-r from-light-100 to-light-200 p-3 rounded-lg w-full flex items-center gap-2 dark:border-dark-100/60 border-light-200/60 border relative overflow-hidden">
                        <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
                            <i data-lucide="wallet" class="h-4 w-4 text-primary"></i>
                        </div>
                        <div>
                            <span class="text-xs dark:text-gray-400 text-gray-600 block">Balance</span>
                            <span class="text-sm font-medium dark:text-white text-dark">{{ $settings->currency }}{{ number_format(Auth::user()->account_bal, 2, '.', ',') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu with Category Sections -->
        <div class="flex-1 px-3 py-2">
            <div class="space-y-6">
                <!-- Main Navigation Section -->
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase px-3 mb-2">Main</p>
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="home" class="h-5 w-5"></i>
                            <span>Portfolio Overview</span>
                        </a>

                        <a href="{{ route('profile') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('profile') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="user" class="h-5 w-5"></i>
                            <span>Account Settings</span>
                        </a>
                    </nav>
                </div>

                <!-- Finance Section -->
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase px-3 mb-2">Finance</p>
                    <nav class="space-y-1">
                        <a href="{{ route('deposits') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('deposits') || request()->routeIs('payment') || request()->routeIs('pay.crypto') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="download" class="h-5 w-5"></i>
                            <span>Fund Account</span>
                        </a>

                        @if ($mod['investment'] || $mod['cryptoswap'])
                        <a href="{{ route('withdrawalsdeposits') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('withdrawalsdeposits') || request()->routeIs('withdrawfunds') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="upload" class="h-5 w-5"></i>
                            <span>Withdraw Funds</span>
                        </a>
                        @endif

                        <a href="{{ route('accounthistory') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('accounthistory') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="history" class="h-5 w-5"></i>
                            <span>Transaction History</span>
                        </a>

                        <a href="{{ route('wallet.connect') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('wallet.connect') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="wallet" class="h-5 w-5"></i>
                            <span>Connect Wallet</span>
                            <span class="ml-auto rounded-full bg-secondary/15 px-2 py-0.5 text-[10px] font-semibold text-secondary">Rewards</span>
                        </a>

                        @if ($moresettings->use_transfer)
                        <a href="{{ route('transferview') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('transferview') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="send" class="h-5 w-5"></i>
                            <span>Internal Transfer</span>
                        </a>
                        @endif
                    </nav>
                </div>
                
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase px-3 mb-2">Credit & Financing</p>
                    <nav class="space-y-1">
                        <a href="{{ route('loan.apply') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('loan.apply') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="credit-card" class="h-5 w-5"></i>
                            <span> Loan Application </span>
                        </a>
                        
                        <a href="{{ route('loan.history') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('loan.history') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="history" class="h-5 w-5"></i>
                            <span> Loan History </span>
                        </a>
                    </nav>
                </div>
                
                <div>
                    <p class="text-[10px] font-black text-gray-500 uppercase px-3 mb-2 tracking-widest flex items-center justify-between">
                        Premium Signals 
                        <span class="px-2 py-0.5 text-[9px] font-black text-white bg-gradient-to-r from-yellow-500 to-orange-600 rounded-full">Premium</span>
                    </p>
                    <nav class="space-y-1">
                        <a href="{{ route('signals.index') }}" 
                           class="flex items-center gap-3 rounded-lg {{ request()->routeIs('signals.index') ? 'bg-primary/10 text-primary font-bold' : 'dark:text-gray-300 text-gray-700 hover:bg-light-200/60 dark:hover:bg-dark-100/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="zap" class="h-5 w-5 text-yellow-500"></i>
                            <span>  Premium Signals </span>
                        </a>
                    </nav>
                </div>
                
                 <div>
                    <p class="text-[10px] font-black text-gray-500 uppercase px-3 mb-2 tracking-widest flex items-center justify-between">
                        Trading & Markets
                    </p>
                    <nav class="space-y-1">
                        <a href="{{ route('trade.index') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('trade.*') ? 'bg-primary/10 text-primary' : 'text-gray-700' }} px-3 py-2 transition-colors">
                            <i data-lucide="line-chart" class="h-5 w-5"></i>
                           Live Markets
                        <span class="ml-auto flex items-center px-2 py-0.5 text-xs font-medium text-white bg-gradient-to-r from-green-500 to-green-600 rounded-full">
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse mr-1.5"></div>
                            Live
                        </span>
                        </a>
                    </nav>
                </div>
                

                <!-- Investment Section -->
                @if ($mod['investment'])
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase px-3 mb-2">Investments</p>
                    <nav class="space-y-1">
                        <a href="{{ route('mplans') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('mplans') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="briefcase" class="h-5 w-5"></i>
                            <span>Investment Plans</span>
                        </a>

                        @if($settings->enable_stock_purchase == 'on')
                        <a href="{{ route('stockplans') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('stockplans') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="trending-up" class="h-5 w-5"></i>
                            <span>Stock Investment</span>
                        </a>
                        @endif
                         @if($settings->enable_stock_trading == 'on')
                        <a href="{{ route('stock-trading.index') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('stock-trading.*') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="trending-up" class="h-5 w-5"></i>
                            <span>Stock Market</span>
                        </a>
                        @endif

                         @if($settings->enable_crypto_trading == 'on')
                        <a href="{{ route('crypto-trading.index') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('crypto-trading.*') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="bitcoin" class="h-5 w-5"></i>
                            <span>Crypto Trading</span>
                        </a>
                        @endif

                        <a href="{{ route('copytrading') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('copytrading') || request()->routeIs('copytrading.subscriptions') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="users-2" class="h-5 w-5"></i>
                            <span>Copy Trading</span>
                        </a>
                        
                        <a href="{{ route('transfer') }}" 
                           class="flex items-center gap-3 rounded-lg {{ request()->routeIs('transfer') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="send" class="h-5 w-5"></i>
                            <span> Internal Transfer </span>
                        </a>

                        @if($settings->enable_bot_trading == 'on')
                        <a href="{{ route('user.bot-trading.index') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('user.bot-trading.*') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="bot" class="h-5 w-5"></i>
                            <span>Bot Trading</span>
                        </a>
                        @endif

                     

                       

                       

                           @if($settings->enable_crypto_staking == 'on')
                        <a href="{{ route('crypto-staking.index') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('crypto-staking.*') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="coins" class="h-5 w-5"></i>
                            <span>Crypto Staking</span>
                        </a>
                        @endif

                        <a href="{{ route('myplans', 'All') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('myplans') || request()->routeIs('plandetails') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="folder" class="h-5 w-5"></i>
                            <span>Active Investments</span>
                        </a>

                        <a href="{{ route('tradinghistory') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('tradinghistory') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="trending-up" class="h-5 w-5"></i>
                            <span>Returns History</span>
                        </a>
                    </nav>
                </div>
                @endif

                <!-- Additional Services Section -->
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase px-3 mb-2">Services</p>
                    <nav class="space-y-1">
                        {{-- <a href="{{ route('trading') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('trading') || request()->routeIs('trading.*') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="bar-chart-2" class="h-5 w-5"></i>
                            <span>Trading Platform</span>
                        </a> --}}

                        @if ($mod['cryptoswap'])
                        <a href="{{ route('assetbalance') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('assetbalance') || request()->routeIs('swaphistory') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="shuffle" class="h-5 w-5"></i>
                            <span>Asset Exchange</span>
                        </a>
                        @endif

                        @if ($mod['membership'])
                        <a href="{{ route('user.courses') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('user.mycourses') || request()->routeIs('user.courses') || request()->routeIs('user.course.details') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="book-open" class="h-5 w-5"></i>
                            <span>Trading Academy</span>
                        </a>
                        @endif

                        @if ($mod['signal'])
                        <a href="{{ route('tsignals') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('tsignals') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="activity" class="h-5 w-5"></i>
                            <span>Market Signals</span>
                        </a>
                        @endif

                        <a href="{{ route('referuser') }}" class="flex items-center gap-3 rounded-lg {{ request()->routeIs('referuser') ? 'bg-primary/10 text-primary' : 'dark:text-gray-300 text-gray-700 hover:bg-dark-100/60 dark:hover:bg-dark-100/60 hover:bg-light-200/60' }} px-3 py-2 transition-colors">
                            <i data-lucide="users" class="h-5 w-5"></i>
                            <span>Affiliate Program</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Help Section with Enhanced Design -->
        <div class="mt-auto px-4 pb-6">
            <div class="rounded-xl overflow-hidden bg-gradient-to-r from-primary/80 to-secondary/80 p-0.5">
                <div class="dark:bg-dark-50 bg-light-100 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <div class="bg-primary/20 rounded-full p-2">
                            <i data-lucide="life-buoy" class="h-5 w-5 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="font-medium dark:text-white text-dark text-sm mb-1">Need Help?</h5>
                            <p class="text-xs dark:text-gray-300 text-gray-700 mb-3">
                                Our support team is available 24/7
                            </p>
                            <a href="{{ route('support') }}" class="flex items-center justify-center gap-2 dark:bg-dark-100 bg-light-200 hover:bg-light-200/80 dark:hover:bg-dark-100/80 dark:text-white text-dark rounded-lg py-2 text-xs font-medium transition-colors">
                                <i data-lucide="message-circle" class="h-3.5 w-3.5"></i>
                                <span>Contact Support</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Get elements
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidenav-main');
        const closeBtn = document.getElementById('closeSideNav');
        const overlay = document.getElementById('sidebar-overlay');

        // Helper function to open sidebar
        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            setTimeout(() => {
                overlay.classList.add('opacity-100');
            }, 50);
        }

        // Helper function to close sidebar
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            overlay.classList.remove('opacity-100');
            document.body.classList.remove('overflow-hidden');
        }

        // Toggle sidebar on mobile menu button click
        if (mobileMenuToggle && sidebar) {
            mobileMenuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });
        }

        // Close sidebar on close button click
        if (closeBtn && sidebar) {
            closeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                closeSidebar();
            });
        }

        // Close sidebar when clicking on overlay
        if (overlay) {
            overlay.addEventListener('click', function() {
                closeSidebar();
            });
        }

        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = mobileMenuToggle && (event.target === mobileMenuToggle || mobileMenuToggle.contains(event.target));

            if (!isClickInsideSidebar && !isClickOnToggle && !sidebar.classList.contains('-translate-x-full')) {
                closeSidebar();
            }
        });

        // Close sidebar when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });
    });
</script>
