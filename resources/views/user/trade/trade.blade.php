@extends('layouts.dash')
@section('title', 'Trading Markets')
@section('content')

<div class="container mx-auto px-4 py-8" x-data="tradingMarkets()" x-cloak>
    <x-danger-alert />
    <x-success-alert />
    <x-notify-alert />

    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Trading Markets</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Choose from thousands of trading instruments across multiple asset classes</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Search instruments..."
                           class="w-64 pl-10 pr-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white">
                    <i data-lucide="search" class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"></i>
                </div>

                <div class="hidden md:flex items-center gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-gray-900 dark:text-white font-semibold" x-text="totalInstruments"></div>
                        <div class="text-gray-500 dark:text-gray-400">Instruments</div>
                    </div>
                    <div class="w-px h-8 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="text-center">
                        <div class="text-green-600 dark:text-green-400 font-semibold">24/7</div>
                        <div class="text-gray-500 dark:text-gray-400">Trading</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-1.5">
            <div class="flex flex-wrap gap-1">
                <button @click="selectedType = 'all'"
                        :class="selectedType === 'all' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                    <i data-lucide="grid-3x3" class="w-4 h-4"></i>
                    All Markets
                </button>

                <button @click="selectedType = 'crypto'"
                        :class="selectedType === 'crypto' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                    <i data-lucide="bitcoin" class="w-4 h-4"></i>
                    Cryptocurrency
                </button>

                <button @click="selectedType = 'stock'"
                        :class="selectedType === 'stock' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    Stocks
                </button>

                <button @click="selectedType = 'forex'"
                        :class="selectedType === 'forex' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                    <i data-lucide="globe" class="w-4 h-4"></i>
                    Forex
                </button>

                <button @click="selectedType = 'commodity'"
                        :class="selectedType === 'commodity' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                    <i data-lucide="zap" class="w-4 h-4"></i>
                    Commodities
                </button>

                <button @click="selectedType = 'bond'"
                        :class="selectedType === 'bond' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                    <i data-lucide="landmark" class="w-4 h-4"></i>
                    Bonds
                </button>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div x-show="loading" class="flex items-center justify-center py-12">
            <div class="flex items-center gap-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <span class="text-gray-600 dark:text-gray-400">Loading instruments...</span>
            </div>
        </div>

        <div x-show="!loading && filteredInstruments.length === 0" class="text-center py-12" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8">
                <i data-lucide="search-x" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No instruments found</h3>
                <p class="text-gray-600 dark:text-gray-400">Try adjusting your search or filter criteria</p>
            </div>
        </div>

        <template x-for="group in groupedInstrumentsArray" :key="group.type">
            <div x-show="selectedType === 'all' || selectedType === group.type" class="space-y-4">
                
                <div class="flex items-center gap-3 px-2">
                    <div class="flex items-center gap-2">
                        <template x-if="group.type === 'crypto'">
                            <i data-lucide="bitcoin" class="w-5 h-5 text-orange-500"></i>
                        </template>
                        <template x-if="group.type === 'stock'">
                            <i data-lucide="trending-up" class="w-5 h-5 text-green-500"></i>
                        </template>
                        <template x-if="group.type === 'forex'">
                            <i data-lucide="globe" class="w-5 h-5 text-blue-500"></i>
                        </template>
                        <template x-if="group.type === 'commodity'">
                            <i data-lucide="zap" class="w-5 h-5 text-yellow-500"></i>
                        </template>
                        <template x-if="group.type === 'bond'">
                            <i data-lucide="landmark" class="w-5 h-5 text-purple-500"></i>
                        </template>

                        <h2 class="text-xl font-bold text-gray-900 dark:text-white capitalize" x-text="getTypeDisplayName(group.type)"></h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400" x-text="`(${group.items.length} instruments)`"></span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                        <div class="grid grid-cols-12 gap-4 items-center text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                            <div class="col-span-8 md:col-span-3">Asset</div>
                            <div class="col-span-2 text-right hidden md:block">Price</div>
                            <div class="col-span-2 text-right hidden md:block">24h Change</div>
                            <div class="col-span-2 text-right hidden md:block">Volume</div>
                            <div class="col-span-4 md:col-span-3 text-right">Action</div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-600">
                        <template x-for="instrument in group.items" :key="instrument.id">
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                <div class="grid grid-cols-12 gap-4 items-center">
                                    
                                    <div class="col-span-8 md:col-span-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                                <template x-if="instrument.logo">
                                                    <img :src="instrument.logo" :alt="instrument.name" class="w-8 h-8 rounded-full object-cover">
                                                </template>
                                                <template x-if="!instrument.logo">
                                                    <span class="text-gray-500 dark:text-gray-400 font-semibold text-sm" x-text="(instrument.symbol || '??').substring(0, 2)"></span>
                                                </template>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-gray-900 dark:text-white truncate" x-text="instrument.name || 'Unknown'"></div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400" x-text="instrument.symbol || 'N/A'"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-2 text-right hidden md:block">
                                        <div class="font-semibold text-gray-900 dark:text-white" x-text="formatPrice(instrument.price)"></div>
                                    </div>

                                    <div class="col-span-2 text-right hidden md:block">
                                        <div class="flex flex-col items-end gap-1">
                                            <span :class="parseFloat(instrument.percent_change_24h || 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                                                  class="font-semibold flex items-center gap-1">
                                                <template x-if="parseFloat(instrument.percent_change_24h || 0) >= 0">
                                                    <i data-lucide="trending-up" class="w-3 h-3"></i>
                                                </template>
                                                <template x-if="parseFloat(instrument.percent_change_24h || 0) < 0">
                                                    <i data-lucide="trending-down" class="w-3 h-3"></i>
                                                </template>
                                                <span x-text="formatPercentage(instrument.percent_change_24h)"></span>
                                            </span>
                                            <span :class="parseFloat(instrument.change || 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                                                  class="text-sm" x-text="formatChange(instrument.change)"></span>
                                        </div>
                                    </div>

                                    <div class="col-span-2 text-right hidden md:block">
                                        <div class="text-gray-600 dark:text-gray-400" x-text="formatVolume(instrument.volume)"></div>
                                    </div>

                                    <div class="col-span-4 md:col-span-3 text-right">
                                        <a :href="`{{ route('trade.single', '') }}/${instrument.id}`"
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 shadow-sm hover:shadow-md">
                                            <i data-lucide="trending-up" class="w-4 h-4"></i>
                                            <span>Trade</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function tradingMarkets() {
    return {
        instruments: @json($instruments ?? []),
        selectedType: 'all',
        searchQuery: '',
        loading: false,

        get totalInstruments() {
            return this.instruments.length;
        },

        get filteredInstruments() {
            let filtered = this.instruments;

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(instrument => {
                    const name = (instrument.name || '').toLowerCase();
                    const symbol = (instrument.symbol || '').toLowerCase();
                    return name.includes(query) || symbol.includes(query);
                });
            }

            if (this.selectedType !== 'all') {
                filtered = filtered.filter(instrument => {
                    const type = (instrument.type || 'other').toLowerCase();
                    return type === this.selectedType;
                });
            }

            return filtered;
        },

        // This is the array-based getter that fixes the Alpine rendering bug!
        get groupedInstrumentsArray() {
            const grouped = {};
            this.filteredInstruments.forEach(instrument => {
                const type = (instrument.type || 'other').toLowerCase();
                if (!grouped[type]) {
                    grouped[type] = [];
                }
                grouped[type].push(instrument);
            });

            // Transforms the object into an array: [{ type: 'crypto', items: [...] }]
            return Object.keys(grouped).map(type => {
                return {
                    type: type,
                    items: grouped[type].sort((a, b) => parseFloat(b.volume || 0) - parseFloat(a.volume || 0))
                };
            });
        },

        getTypeDisplayName(type) {
            const names = {
                'crypto': 'Cryptocurrency',
                'stock': 'Stocks',
                'forex': 'Foreign Exchange',
                'commodity': 'Commodities',
                'bond': 'Bonds',
                'other': 'Other Assets'
            };
            return names[type] || type;
        },

        formatPrice(price) {
            if (!price) return 'N/A';
            const num = parseFloat(price);
            if (num >= 1) {
                return '$' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                return '$' + num.toFixed(6);
            }
        },

        formatPercentage(percent) {
            if (!percent) return '0.00%';
            const num = parseFloat(percent);
            return (num >= 0 ? '+' : '') + num.toFixed(2) + '%';
        },

        formatChange(change) {
            if (!change) return '$0.00';
            const num = parseFloat(change);
            return (num >= 0 ? '+$' : '-$') + Math.abs(num).toFixed(2);
        },

        formatVolume(volume) {
            if (!volume) return 'N/A';
            const num = parseFloat(volume);
            if (num >= 1e9) {
                return '$' + (num / 1e9).toFixed(1) + 'B';
            } else if (num >= 1e6) {
                return '$' + (num / 1e6).toFixed(1) + 'M';
            } else if (num >= 1e3) {
                return '$' + (num / 1e3).toFixed(1) + 'K';
            }
            return '$' + num.toLocaleString();
        },

        init() {
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        }
    }
}

document.addEventListener('alpine:updated', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endsection