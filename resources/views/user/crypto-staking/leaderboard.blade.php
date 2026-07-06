@extends('layouts.dash')
@section('content')
<div class="content">
    <div class="page-inner">
        <div class="mb-5">
            <h1 class="text-3xl font-bold dark:text-white text-dark">Staking Leaderboard</h1>
            <p class="dark:text-gray-400 text-gray-600 mt-2">Top stakers earning the most rewards</p>
        </div>

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('crypto-staking.index') }}" class="inline-flex items-center text-primary hover:text-primary-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Staking Plans
            </a>
        </div>

        <!-- Filter Options -->
        <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('crypto-staking.leaderboard') }}" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm dark:text-gray-400 text-gray-600 mb-2">Filter by Cryptocurrency</label>
                    <select name="crypto" class="w-full dark:bg-dark-100 bg-light-200 dark:border-dark-100 border-light-200 border dark:text-white text-dark rounded-lg px-4 py-2 focus:outline-none focus:border-primary" onchange="this.form.submit()">
                        <option value="">All Cryptocurrencies</option>
                        @foreach($cryptos as $crypto)
                        <option value="{{ $crypto->coin_symbol }}" {{ request('crypto') == $crypto->coin_symbol ? 'selected' : '' }}>
                            {{ $crypto->coin_symbol }} - {{ $crypto->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @if(request('crypto'))
                <a href="{{ route('crypto-staking.leaderboard') }}" class="px-4 py-2 dark:bg-dark-100 bg-light-200 hover:border-primary dark:text-white text-dark rounded-lg transition">
                    Clear Filter
                </a>
                @endif
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4 text-center">
                <h3 class="text-2xl font-bold dark:text-white text-dark">{{ $topStakers->count() ?? 0 }}</h3>
                <p class="text-sm dark:text-gray-400 text-gray-600">Total Stakers</p>
            </div>
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4 text-center">
                <h3 class="text-2xl font-bold text-secondary">{{ $settings->currency }}{{ number_format($totalRewards ?? 0, 2) }}</h3>
                <p class="text-sm dark:text-gray-400 text-gray-600">Total Rewards Paid</p>
            </div>
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4 text-center">
                <h3 class="text-2xl font-bold text-primary">{{ $settings->currency }}{{ number_format($totalStaked ?? 0, 2) }}</h3>
                <p class="text-sm dark:text-gray-400 text-gray-600">Total Staked</p>
            </div>
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-4 text-center">
                <h3 class="text-2xl font-bold text-accent">{{ $activeStakes ?? 0 }}</h3>
                <p class="text-sm dark:text-gray-400 text-gray-600">Active Stakes</p>
            </div>
        </div>

        <!-- Top 3 Podium -->
        @if($topStakers && $topStakers->count() >= 3)
        <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-8 mb-6">
            <h2 class="text-2xl font-bold dark:text-white text-dark mb-6 text-center flex items-center justify-center">
                <svg class="w-8 h-8 text-accent mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                Top 3 Stakers
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- 2nd Place -->
                <div class="order-2 md:order-1">
                    <div class="dark:bg-dark-100 bg-light-200 border-2 border-gray-400 rounded-lg p-6 text-center transform hover:-translate-y-2 transition duration-300">
                        <div class="flex justify-center mb-4">
                            <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-400 mb-2">#2</div>
                        <div class="text-lg font-bold dark:text-white text-dark mb-2">{{ $topStakers[1]->user->name }}</div>
                        <div class="text-2xl font-bold text-secondary mb-3">{{ $settings->currency }}{{ number_format($topStakers[1]->total_rewards, 2) }}</div>
                        <div class="text-sm dark:text-gray-400 text-gray-600 mb-2">Total Rewards</div>
                        <div class="flex justify-center gap-2">
                            <span class="px-3 py-1 bg-primary/20 text-primary text-xs rounded-full border border-primary">
                                {{ $topStakers[1]->stake_count }} stakes
                            </span>
                            <span class="px-3 py-1 bg-purple-900/50 text-purple-400 text-xs rounded-full border border-purple-500">
                                {{ number_format($topStakers[1]->avg_apr, 1) }}% APR
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 1st Place -->
                <div class="order-1 md:order-2">
                    <div class="bg-accent/10 border-2 border-accent rounded-lg p-8 text-center transform md:-translate-y-4 hover:-translate-y-6 transition duration-300">
                        <div class="flex justify-center mb-4">
                            <svg class="w-20 h-20 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <div class="text-4xl font-bold text-accent mb-3">#1</div>
                        <div class="text-xl font-bold dark:text-white text-dark mb-3">{{ $topStakers[0]->user->name }}</div>
                        <div class="text-3xl font-bold text-secondary mb-4">{{ $settings->currency }}{{ number_format($topStakers[0]->total_rewards, 2) }}</div>
                        <div class="text-sm dark:text-gray-300 text-gray-700 mb-3">Total Rewards</div>
                        <div class="flex justify-center gap-2">
                            <span class="px-3 py-1 bg-primary/20 text-primary text-xs rounded-full border border-primary">
                                {{ $topStakers[0]->stake_count }} stakes
                            </span>
                            <span class="px-3 py-1 bg-purple-900/50 text-purple-400 text-xs rounded-full border border-purple-500">
                                {{ number_format($topStakers[0]->avg_apr, 1) }}% APR
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 3rd Place -->
                <div class="order-3">
                    <div class="dark:bg-dark-100 bg-light-200 border-2 border-orange-700 rounded-lg p-6 text-center transform hover:-translate-y-2 transition duration-300">
                        <div class="flex justify-center mb-4">
                            <svg class="w-16 h-16 text-orange-700" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-orange-700 mb-2">#3</div>
                        <div class="text-lg font-bold dark:text-white text-dark mb-2">{{ $topStakers[2]->user->name }}</div>
                        <div class="text-2xl font-bold text-secondary mb-3">{{ $settings->currency }}{{ number_format($topStakers[2]->total_rewards, 2) }}</div>
                        <div class="text-sm dark:text-gray-400 text-gray-600 mb-2">Total Rewards</div>
                        <div class="flex justify-center gap-2">
                            <span class="px-3 py-1 bg-primary/20 text-primary text-xs rounded-full border border-primary">
                                {{ $topStakers[2]->stake_count }} stakes
                            </span>
                            <span class="px-3 py-1 bg-purple-900/50 text-purple-400 text-xs rounded-full border border-purple-500">
                                {{ number_format($topStakers[2]->avg_apr, 1) }}% APR
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Full Rankings Table -->
        <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg overflow-hidden">
            <div class="p-6 border-b dark:border-dark-100 border-light-200">
                <h2 class="text-xl font-bold dark:text-white text-dark">Full Rankings (Top 50)</h2>
            </div>
            <div class="overflow-x-auto">
                @if($topStakers && $topStakers->count() > 0)
                <table class="w-full">
                    <thead class="dark:bg-dark-100 bg-light-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-600 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-600 uppercase tracking-wider">Total Staked</th>
                            <th class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-600 uppercase tracking-wider">Total Rewards</th>
                            <th class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-600 uppercase tracking-wider">Stakes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium dark:text-gray-400 text-gray-600 uppercase tracking-wider">Avg APR</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ROI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-dark-100 divide-light-200">
                        @foreach($topStakers as $index => $staker)
                        <tr class="hover:bg-primary/10 transition {{ $staker->user_id == auth()->id() ? 'bg-primary/20 border-l-4 border-primary' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-lg font-bold {{ $index < 3 ? 'text-accent' : 'dark:text-gray-400 text-gray-600' }}">
                                        #{{ $index + 1 }}
                                    </span>
                                    @if($index == 0)
                                    <svg class="w-5 h-5 ml-2 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @elseif($index == 1)
                                    <svg class="w-5 h-5 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @elseif($index == 2)
                                    <svg class="w-5 h-5 ml-2 text-orange-700" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium dark:text-white text-dark">
                                    {{ $staker->user->name }}
                                    @if($staker->user_id == auth()->id())
                                    <span class="ml-2 px-2 py-1 bg-primary/20 text-primary text-xs rounded-full border border-primary">You</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm dark:text-white text-dark">{{ $settings->currency }}{{ number_format($staker->total_staked, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-secondary">{{ $settings->currency }}{{ number_format($staker->total_rewards, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-primary/20 text-primary text-xs rounded-full border border-primary">
                                    {{ $staker->stake_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-accent">{{ number_format($staker->avg_apr, 2) }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                $roi = $staker->total_staked > 0 ? ($staker->total_rewards / $staker->total_staked * 100) : 0;
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-24 dark:bg-gray-700 bg-gray-300 rounded-full h-2 mr-2">
                                        <div class="bg-secondary h-2 rounded-full" style="width: {{ min($roi, 100) }}%"></div>
                                    </div>
                                    <span class="text-sm dark:text-white text-dark">{{ number_format($roi, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto dark:text-gray-600 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="dark:text-gray-400 text-gray-600">No stakers yet. Be the first!</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Your Rank (if not in top 50) -->
        @if($userRank && $userRank > 50)
        <div class="bg-primary/10 border border-primary rounded-lg p-6 mt-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold dark:text-white text-dark mb-1">Your Ranking</h3>
                    <p class="text-sm dark:text-gray-400 text-gray-600">You're not in the top 50 yet, but keep staking!</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-primary">#{{ $userRank }}</div>
                    <p class="text-sm dark:text-gray-400 text-gray-600">out of {{ $totalStakers }} stakers</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
