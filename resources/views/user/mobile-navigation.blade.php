<div class="fixed bottom-8 left-6 right-6 z-[9990] md:hidden">
    <div class="relative h-16 bg-dark/80 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] px-2">
        
        <div id="nav-indicator" class="absolute top-1.5 bottom-1.5 w-[18%] bg-gradient-to-b from-primary/20 to-primary/5 border border-primary/30 rounded-2xl transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] z-0"></div>

        <div class="flex items-center justify-between h-full relative z-10">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-[18%] nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-index="0">
                <i data-lucide="layout-grid" class="h-5 w-5 transition-transform duration-300 {{ request()->routeIs('dashboard') ? 'text-primary scale-110' : 'text-slate-400' }}"></i>
                <span class="text-[9px] mt-1 font-bold tracking-widest uppercase {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-slate-500' }}">Dash</span>
            </a>

            <a href="{{ route('deposits') }}" class="flex flex-col items-center justify-center w-[18%] nav-item {{ request()->routeIs('deposits') ? 'active' : '' }}" data-index="1">
                <i data-lucide="wallet" class="h-5 w-5 transition-transform duration-300 {{ request()->routeIs('deposits') ? 'text-primary scale-110' : 'text-slate-400' }}"></i>
                <span class="text-[9px] mt-1 font-bold tracking-widest uppercase {{ request()->routeIs('deposits') ? 'text-primary' : 'text-slate-500' }}">Fund</span>
            </a>

            <div class="flex flex-col items-center justify-center w-[20%] -mt-10">
                <div class="absolute h-16 w-16 bg-primary/20 rounded-full blur-2xl animate-pulse"></div>
                <button id="fab-trigger" class="relative h-14 w-14 bg-gradient-to-tr from-primary via-primary to-secondary rounded-2xl rotate-45 shadow-lg shadow-primary/40 flex items-center justify-center border-2 border-white/20 active:scale-90 transition-all">
                    <i data-lucide="zap" class="h-6 w-6 text-white -rotate-45 fill-white"></i>
                </button>
            </div>

            <a href="{{ route('accounthistory') }}" class="flex flex-col items-center justify-center w-[18%] nav-item {{ request()->routeIs('accounthistory') ? 'active' : '' }}" data-index="3">
                <i data-lucide="activity" class="h-5 w-5 transition-transform duration-300 {{ request()->routeIs('accounthistory') ? 'text-primary scale-110' : 'text-slate-400' }}"></i>
                <span class="text-[9px] mt-1 font-bold tracking-widest uppercase {{ request()->routeIs('accounthistory') ? 'text-primary' : 'text-slate-500' }}">Logs</span>
            </a>

            <a href="{{ route('profile') }}" class="flex flex-col items-center justify-center w-[18%] nav-item {{ request()->routeIs('profile') ? 'active' : '' }}" data-index="4">
                <i data-lucide="user-cog" class="h-5 w-5 transition-transform duration-300 {{ request()->routeIs('profile') ? 'text-primary scale-110' : 'text-slate-400' }}"></i>
                <span class="text-[9px] mt-1 font-bold tracking-widest uppercase {{ request()->routeIs('profile') ? 'text-primary' : 'text-slate-500' }}">Self</span>
            </a>
        </div>
    </div>
</div>

<div id="action-sheet" class="hidden fixed inset-0 z-[9991]">
    <div id="sheet-backdrop" class="absolute inset-0 bg-dark/60 backdrop-blur-md opacity-0 transition-opacity duration-300"></div>
    
    <div id="sheet-content" class="absolute inset-x-0 bottom-0 bg-[#0A0F1C] border-t border-white/10 rounded-t-[2.5rem] p-8 translate-y-full transition-transform duration-500 cubic-bezier(0.32, 0.72, 0, 1)">
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-8"></div>
        
        <div class="grid grid-cols-2 gap-4">
            @if ($mod['investment'])
            <a href="{{ route('mplans') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-primary/50 transition-colors">
                <div class="h-12 w-12 rounded-xl bg-primary/20 flex items-center justify-center">
                    <i data-lucide="trending-up" class="text-primary"></i>
                </div>
                <div>
                    <p class="text-white font-bold">Invest</p>
                    <p class="text-slate-400 text-[10px]">Grow wealth</p>
                </div>
            </a>
            @endif

            @if ($mod['investment'] || $mod['cryptoswap'])
            <a href="{{ route('withdrawalsdeposits') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-secondary/50 transition-colors">
                <div class="h-12 w-12 rounded-xl bg-secondary/20 flex items-center justify-center">
                    <i data-lucide="arrow-up-right" class="text-secondary"></i>
                </div>
                <div>
                    <p class="text-white font-bold">Withdraw</p>
                    <p class="text-slate-400 text-[10px]">Cash out</p>
                </div>
            </a>
            @endif

            @if ($mod['cryptoswap'])
            <a href="{{ route('assetbalance') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-amber-500/50 transition-colors">
                <div class="h-12 w-12 rounded-xl bg-amber-500/20 flex items-center justify-center">
                    <i data-lucide="refresh-cw" class="text-amber-500"></i>
                </div>
                <div>
                    <p class="text-white font-bold">Swap</p>
                    <p class="text-slate-400 text-[10px]">Convert crypto</p>
                </div>
            </a>
            @endif

            <a href="{{ route('referuser') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-blue-500/50 transition-colors">
                <div class="h-12 w-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                    <i data-lucide="users" class="text-blue-500"></i>
                </div>
                <div>
                    <p class="text-white font-bold">Refer</p>
                    <p class="text-slate-400 text-[10px]">Earn bonus</p>
                </div>
            </a>

            <a href="{{ route('support') }}" class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-danger/50 transition-colors">
                <div class="h-12 w-12 rounded-xl bg-danger/20 flex items-center justify-center">
                    <i data-lucide="help-circle" class="text-danger"></i>
                </div>
                <div>
                    <p class="text-white font-bold">Support</p>
                    <p class="text-slate-400 text-[10px]">Get help</p>
                </div>
            </a>

            <a href="#" class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-purple-500/50 transition-colors">
                <div class="h-12 w-12 rounded-xl bg-purple-500/20 flex items-center justify-center">
                    <i data-lucide="globe" class="text-purple-500"></i>
                </div>
                <div>
                    <p class="text-white font-bold">News</p>
                    <p class="text-slate-400 text-[10px]">Market updates</p>
                </div>
            </a>
        </div>
        <button id="close-sheet" class="w-full mt-8 py-4 bg-white/5 rounded-2xl text-slate-400 font-bold hover:text-white transition-colors">Dismiss</button>
    </div>
</div>

<style>
/* Position Logic for the Segmented Pill */
.nav-item[data-index="0"].active ~ #nav-indicator { left: 1%; width: 18.5%; }
.nav-item[data-index="1"].active ~ #nav-indicator { left: 20%; width: 18.5%; }
.nav-item[data-index="3"].active ~ #nav-indicator { left: 61.5%; width: 18.5%; }
.nav-item[data-index="4"].active ~ #nav-indicator { left: 80.5%; width: 18.5%; }

/* Smooth scale for active icons */
.nav-item.active i { transform: translateY(-2px) scale(1.1); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('fab-trigger');
    const sheet = document.getElementById('action-sheet');
    const backdrop = document.getElementById('sheet-backdrop');
    const content = document.getElementById('sheet-content');
    const closeBtn = document.getElementById('close-sheet');

    function openSheet() {
        sheet.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.replace('opacity-0', 'opacity-100');
            content.classList.replace('translate-y-full', 'translate-y-0');
        }, 10);
    }

    function closeSheet() {
        backdrop.classList.replace('opacity-100', 'opacity-0');
        content.classList.replace('translate-y-0', 'translate-y-full');
        setTimeout(() => sheet.classList.add('hidden'), 500);
    }

    trigger.addEventListener('click', openSheet);
    backdrop.addEventListener('click', closeSheet);
    closeBtn.addEventListener('click', closeSheet);

    // Initial Indicator Position
    const activeItem = document.querySelector('.nav-item.active');
    const indicator = document.getElementById('nav-indicator');
    if (activeItem) {
        const idx = activeItem.dataset.index;
        const pos = { "0": "1%", "1": "20%", "3": "61.5%", "4": "80.5%" };
        indicator.style.left = pos[idx];
    } else {
        indicator.style.display = 'none';
    }
});
</script>