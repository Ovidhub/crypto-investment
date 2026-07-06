<header class="h-20 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-[9999] bg-white/70 dark:bg-[#0f172a]/70 backdrop-blur-xl border-b border-slate-200/60 dark:border-slate-800/50 shadow-sm transition-all duration-300">
    
    <div class="flex items-center gap-5">
        <button class="md:hidden flex items-center justify-center h-10 w-10 rounded-full dark:text-slate-300 text-slate-600 bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 hover:scale-105 active:scale-95 transition-all" id="mobileMenuToggle">
            <i class="fas fa-bars-staggered h-4 w-4"></i>
        </button>
        
        <div class="flex items-center md:hidden">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 p-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
                <img src="{{ asset('storage/app/public/' . $settings->logo) }}" alt="logo" class="h-7 w-auto object-contain">
            </a>
        </div>

        <div class="hidden md:block">
            <h1 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Overview</h1>
            <p class="text-sm font-bold text-slate-900 dark:text-white">Welcome back, {{ Auth::user()->username }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-2 sm:gap-4">
        
        <button id="themeToggle" onclick="toggleTheme()" class="h-10 w-10 flex items-center justify-center rounded-xl dark:text-amber-400 text-slate-500 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 hover:border-primary transition-all duration-300 group" title="Toggle Theme">
            <i data-lucide="sun" class="h-4 w-4 hidden dark:block group-hover:rotate-45 transition-transform"></i>
            <i data-lucide="moon" class="h-4 w-4 dark:hidden group-hover:-rotate-12 transition-transform"></i>
        </button>
        
        @if ($settings->enable_kyc == 'yes')
        <div class="relative" x-data="{ open: false }">
            @if (Auth::user()->account_verify == 'Verified')
            <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-200/50 dark:border-emerald-500/20">
                <i class="fas fa-check-circle h-3 w-3"></i>
                <span class="hidden sm:inline-block">Verified</span>
            </div>
            @else
            <button @click="open = !open" class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider border border-amber-200/50 dark:border-amber-500/20 hover:scale-105 transition-transform">
                <i class="fas fa-shield-halved h-3 w-3"></i>
                <span class="hidden sm:inline-block">Identity KYC</span>
            </button>
            
            <div x-show="open" x-cloak @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute right-0 mt-3 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden ring-1 ring-black/5">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    <h6 class="text-xs font-black uppercase tracking-widest text-slate-800 dark:text-white">Security Shield</h6>
                </div>
                <div class="p-5 text-center">
                    @if (Auth::user()->account_verify == 'Under review')
                        <div class="h-12 w-12 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-hourglass-half text-primary animate-pulse"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Submission Pending</p>
                        <p class="text-[10px] text-slate-500 mt-1">Our compliance team is currently reviewing your documents.</p>
                    @else
                        <div class="h-12 w-12 bg-amber-500/10 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-user-shield text-amber-500"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Verification Required</p>
                        <a href="{{ route('account.verify') }}" class="mt-4 block w-full py-2 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:shadow-lg hover:shadow-primary/30 transition-all">
                            Start KYC
                        </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @endif
        
        <div class="relative" 
            x-data="{ 
                open: false, 
                notifications: [], 
                unreadCount: 0, 
                loading: true, 
                init() { 
                    this.fetchNotifications(); 
                    this.setupPolling(); 
                }, 
                fetchNotifications() { 
                    this.loading = true; 
                    fetch('{{ route('notifications.latest') }}') 
                        .then(response => response.json()) 
                        .then(data => { 
                            this.notifications = data.notifications; 
                            this.unreadCount = data.unread_count; 
                            this.loading = false; 
                        }) 
                        .catch(error => { 
                            console.error('Error fetching notifications:', error); 
                            this.loading = false; 
                        }); 
                }, 
                setupPolling() { 
                    setInterval(() => { this.fetchNotifications(); }, 30000); 
                } 
            }"
            x-init="init()"
        >
            <button @click="open = !open; if(open) fetchNotifications();" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 text-slate-500 dark:text-slate-400 relative hover:border-primary transition-all">
                <i data-lucide="bell" class="h-4 w-4"></i>
                <span x-show="unreadCount > 0" x-cloak class="absolute top-2.5 right-2.5 h-2 w-2 bg-primary rounded-full border-2 border-white dark:border-slate-900"></span>
            </button>
            
            <div x-show="open" x-cloak @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute right-0 mt-3 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
                
                <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    <h6 class="text-xs font-black uppercase tracking-widest text-slate-800 dark:text-white">Activity</h6>
                    <span x-show="unreadCount > 0" x-text="unreadCount + ' New'" class="text-[9px] font-black bg-primary/20 text-primary px-2 py-0.5 rounded-full uppercase"></span>
                </div>
                
                <div class="max-h-[320px] overflow-y-auto">
                    <template x-if="loading">
                        <div class="p-8 text-center flex flex-col items-center">
                            <div class="h-5 w-5 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </template>
                    
                    <template x-if="!loading && notifications.length === 0">
                        <div class="p-8 text-center">
                            <i data-lucide="mail-open" class="h-8 w-8 text-slate-300 mx-auto mb-2"></i>
                            <p class="text-xs font-bold text-slate-400">All caught up!</p>
                        </div>
                    </template>
                    
                    <template x-if="!loading && notifications.length > 0">
                        <div class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            <template x-for="notification in notifications" :key="notification.id">
                                <a :href="'{{ route('notifications.mark', '') }}/' + notification.id" class="flex gap-4 p-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors" :class="{ 'bg-primary/[0.02]': !notification.is_read }">
                                    <div :class="notification.icon_bg_color || 'bg-primary/10'" class="h-9 w-9 rounded-xl flex items-center justify-center shrink-0">
                                        <i :class="'fas ' + (notification.icon || 'fa-bell')" class="text-[14px] text-primary"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white leading-none" x-text="notification.title"></p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2" x-text="notification.message"></p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter" x-text="new Date(notification.created_at).toLocaleDateString()"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
                
                <div class="p-3 bg-slate-50/50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <a x-show="unreadCount > 0" href="{{ route('notifications.markAll') }}" class="text-[10px] font-black uppercase text-primary hover:underline px-2">Clear All</a>
                    <a href="{{ route('notifications') }}" class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 hover:text-primary px-2 ml-auto">Inbox</a>
                </div>
            </div>
        </div>
        
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2.5 p-1 sm:pr-4 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary transition-all group">
                <div class="h-8 w-8 sm:h-9 sm:w-9 bg-gradient-to-tr from-primary to-secondary rounded-full p-[2px]">
                    <div class="h-full w-full bg-white dark:bg-slate-900 rounded-full flex items-center justify-center text-xs font-black text-primary uppercase">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                </div>
                <div class="hidden md:flex flex-col items-start leading-tight">
                    <span class="text-xs font-black text-slate-900 dark:text-white">{{ Auth::user()->username }}</span>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter">Account Settings</span>
                </div>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-slate-400 group-hover:text-primary transition-colors hidden md:block"></i>
            </button>
            
            <div x-show="open" x-cloak @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 mt-3 w-60 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
                
                <div class="p-5 bg-gradient-to-br from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-900 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-1">Standard User</p>
                    <h6 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</h6>
                    <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                </div>
                
                <div class="p-2 space-y-0.5">
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-primary dark:hover:text-white rounded-xl transition-all">
                        <i data-lucide="user-circle" class="h-4 w-4"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="{{ route('accounthistory') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-primary dark:hover:text-white rounded-xl transition-all">
                        <i data-lucide="history" class="h-4 w-4"></i>
                        <span>Transaction History</span>
                    </a>
                    <a href="{{ route('support') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-primary dark:hover:text-white rounded-xl transition-all">
                        <i data-lucide="help-circle" class="h-4 w-4"></i>
                        <span>Support Center</span>
                    </a>
                </div>
                
                <div class="p-2 mt-2 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="flex items-center gap-3 px-4 py-3 text-xs font-black text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all uppercase tracking-widest">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        <span>Secure Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </div>
        </div>
    </div>
</header>