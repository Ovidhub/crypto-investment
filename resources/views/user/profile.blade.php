@extends('layouts.dash')
@section('title', $title)
@section('content')

<!-- Modern Exchange-Style Header -->
<div class="relative mb-8 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-primary/20 via-secondary/20 to-tertiary/20 rounded-3xl -z-10 blur-xl opacity-50"></div>
    <div class="px-6 py-8 rounded-3xl bg-gradient-to-r from-white/80 to-white/60 dark:from-dark-50/80 dark:to-dark-100/60 backdrop-blur-md border border-white/20 dark:border-dark-200/30 shadow-lg">
        <!-- Animated Elements -->
        <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
            <div class="floating-element elem-1 bg-primary/20 dark:bg-primary/30"></div>
            <div class="floating-element elem-2 bg-secondary/20 dark:bg-secondary/30"></div>
            <div class="floating-element elem-3 bg-tertiary/20 dark:bg-tertiary/30"></div>
            <div class="floating-element elem-4 bg-accent/20 dark:bg-accent/30"></div>
        </div>

        <!-- Header Content -->
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-dark dark:text-white">Account Settings</h1>
                    <p class="mt-2 text-base text-dark-300 dark:text-light-300">Manage your personal information and preferences</p>
                </div>
                <div>
                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-white/80 dark:bg-dark-50/80 backdrop-blur-sm border border-white/20 dark:border-dark-200/30 shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-secondary text-white">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 2C14.5 2 16.5 4 16.5 6.5C16.5 9 14.5 11 12 11C9.5 11 7.5 9 7.5 6.5C7.5 4 9.5 2 12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4.99004 20C4.99004 16.5 8.00004 13.5 12 13.5C16 13.5 19.01 16.5 19.01 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-dark-300 dark:text-light-300">Account</p>
                            <p class="text-base font-bold text-dark dark:text-white">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>

<!-- Alert Messages -->
<x-danger-alert />
<x-success-alert />
<x-error-alert />

<!-- Main Content -->
<div class="bg-white dark:bg-dark-50 rounded-xl shadow-lg border border-light-200 dark:border-dark-200/50 overflow-hidden mb-8">
    <!-- Tab Navigation -->
    <div class="border-b border-light-200 dark:border-dark-200/50">
        <div class="flex overflow-x-auto scrollbar-hide">
            <button type="button" class="tab-btn active px-6 py-4 text-sm font-medium border-b-2 border-primary text-primary dark:text-primary-400 focus:outline-none" data-tab="personal">
                <svg class="w-5 h-5 mb-1 mx-auto" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="hidden md:block">Personal Info</span>
            </button>
            <button type="button" class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-dark-300 dark:text-light-300 hover:text-dark dark:hover:text-white focus:outline-none" data-tab="withdrawal">
                <svg class="w-5 h-5 mb-1 mx-auto" viewBox="0 0 24 24" fill="none">
                    <path d="M9.5 13.7502C9.5 14.7202 10.25 15.5002 11.17 15.5002H13.05C13.85 15.5002 14.5 14.8202 14.5 13.9702C14.5 13.0602 14.1 12.7302 13.51 12.5202L10.5 11.4702C9.91 11.2602 9.51 10.9402 9.51 10.0202C9.51 9.18023 10.16 8.49023 10.96 8.49023H12.84C13.76 8.49023 14.51 9.27023 14.51 10.2402" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 3V7H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 2L17 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="hidden md:block">Withdrawal</span>
            </button>
            <button type="button" class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-dark-300 dark:text-light-300 hover:text-dark dark:hover:text-white focus:outline-none" data-tab="security">
                <svg class="w-5 h-5 mb-1 mx-auto" viewBox="0 0 24 24" fill="none">
                    <path d="M10.49 2.23006L5.50003 4.10004C4.35003 4.53004 3.41003 5.89004 3.41003 7.12004V14.55C3.41003 15.73 4.19005 17.28 5.14005 17.99L9.44003 21.2C10.85 22.26 13.17 22.26 14.58 21.2L18.88 17.99C19.83 17.28 20.61 15.73 20.61 14.55V7.12004C20.61 5.89004 19.67 4.53004 18.52 4.10004L13.53 2.23006C12.68 1.92006 11.32 1.92006 10.49 2.23006Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M10.49 2.23006L5.50003 4.10004C4.35003 4.53004 3.41003 5.89004 3.41003 7.12004V14.55C3.41003 15.73 4.19005 17.28 5.14005 17.99L9.44003 21.2C10.85 22.26 13.17 22.26 14.58 21.2L18.88 17.99C19.83 17.28 20.61 15.73 20.61 14.55V7.12004C20.61 5.89004 19.67 4.53004 18.52 4.10004L13.53 2.23006C12.68 1.92006 11.32 1.92006 10.49 2.23006Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15.5C13.3807 15.5 14.5 14.3807 14.5 13C14.5 11.6193 13.3807 10.5 12 10.5C10.6193 10.5 9.5 11.6193 9.5 13C9.5 14.3807 10.6193 15.5 12 15.5Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 10.5V8" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="hidden md:block">Password</span>
            </button>
            <button type="button" class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-dark-300 dark:text-light-300 hover:text-dark dark:hover:text-white focus:outline-none" data-tab="preferences">
                <svg class="w-5 h-5 mb-1 mx-auto" viewBox="0 0 24 24" fill="none">
                    <path d="M12 16.5C14.4853 16.5 16.5 14.4853 16.5 12C16.5 9.51472 14.4853 7.5 12 7.5C9.51472 7.5 7.5 9.51472 7.5 12C7.5 14.4853 9.51472 16.5 12 16.5Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M3.74416 18.33C4.70416 19.85 6.91416 20.23 8.45416 19.27L8.89416 19.01C9.30416 18.77 9.32416 18.18 8.94416 17.9C8.02856 17.21 7.26526 16.33 6.70665 15.32C6.18416 14.39 5.83825 13.37 5.68416 12.3C5.61416 11.85 5.13416 11.55 4.70416 11.71L4.22416 11.9C2.68416 12.55 2.00416 14.44 2.65416 15.98C2.98416 16.82 3.34416 17.59 3.74416 18.33Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M20.2459 15.98C20.8999 14.44 20.2159 12.55 18.6759 11.9L18.1959 11.71C17.7659 11.55 17.2859 11.85 17.2159 12.3C17.0657 13.38 16.7199 14.4 16.1932 15.32C15.6346 16.33 14.8714 17.21 13.9559 17.9C13.5759 18.18 13.5959 18.77 14.0059 19.01L14.4459 19.27C15.9859 20.23 18.1959 19.85 19.1559 18.33C19.5595 17.59 19.9199 16.82 20.2459 15.98Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M20.2456 8.01001C19.9196 7.17001 19.5596 6.40001 19.1556 5.67001C18.1956 4.15001 15.9856 3.77001 14.4456 4.73001L14.0056 4.99001C13.5956 5.23001 13.5756 5.82001 13.9556 6.10001C14.8692 6.79066 15.6324 7.67219 16.1929 8.68001C16.7154 9.60001 17.0616 10.62 17.2156 11.7C17.2856 12.15 17.7656 12.45 18.1956 12.29L18.6756 12.1C20.2156 11.45 20.8956 9.56001 20.2456 8.02001" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M8.94431 6.1C9.32431 5.82 9.30431 5.23 8.89431 4.99L8.45431 4.73C6.91431 3.77 4.70431 4.15 3.74431 5.67C3.34431 6.4 2.98856 7.17 2.65431 8.02C2.00431 9.56 2.68431 11.45 4.22431 12.1L4.70431 12.29C5.13431 12.45 5.61431 12.15 5.68431 11.7C5.83827 10.62 6.18431 9.59 6.70681 8.68C7.26541 7.67 8.02871 6.79 8.94431 6.1Z" fill="currentColor" fill-opacity="0.2"/>
                    <path d="M12 16.5C14.4853 16.5 16.5 14.4853 16.5 12C16.5 9.51472 14.4853 7.5 12 7.5C9.51472 7.5 7.5 9.51472 7.5 12C7.5 14.4853 9.51472 16.5 12 16.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.17188 3.6001L8.56188 4.2301C9.72188 6.2001 8.99188 8.5801 6.98188 9.7201C4.97188 10.8601 2.59188 10.0201 1.44188 8.0301L1.05188 7.4001C0.35188 6.1701 0.84187 4.5801 2.12187 3.9601C2.90187 3.5601 3.64188 3.2301 4.38188 2.9601C6.10188 2.4401 8.05188 2.0701 8.71188 3.3201" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.8278 3.6001L15.4378 4.2301C14.2778 6.2001 15.0078 8.5801 17.0178 9.7201C19.0278 10.8601 21.4078 10.0201 22.5578 8.0301L22.9478 7.4001C23.6478 6.1701 23.1578 4.5801 21.8778 3.9601C21.0978 3.5601 20.3579 3.2301 19.6179 2.9601C17.8979 2.4401 15.9478 2.0701 15.2878 3.3201" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.17188 20.4001L8.56188 19.7701C9.72188 17.8001 8.99188 15.4201 6.98188 14.2801C4.97188 13.1401 2.59188 13.9801 1.44188 15.9701L1.05188 16.6001C0.35188 17.8301 0.84187 19.4201 2.12187 20.0401C2.90187 20.4401 3.64188 20.7701 4.38188 21.0401C6.10188 21.5601 8.05188 21.9301 8.71188 20.6801" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.8278 20.4001L15.4378 19.7701C14.2778 17.8001 15.0078 15.4201 17.0178 14.2801C19.0278 13.1401 21.4078 13.9801 22.5578 15.9701L22.9478 16.6001C23.6478 17.8301 23.1578 19.4201 21.8778 20.0401C21.0978 20.4401 20.3579 20.7701 19.6179 21.0401C17.8979 21.5601 15.9478 21.9301 15.2878 20.6801" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="hidden md:block">Preferences</span>
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="p-6">
        <!-- Personal Information Tab -->
        <div class="tab-content active" id="personal-tab">
            <form method="POST" action="javascript:void(0)" id="updateprofileform" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-dark dark:text-white">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all">
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-dark dark:text-white">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-3 rounded-xl bg-light-50 dark:bg-dark-200 border border-light-200 dark:border-dark-200 text-dark-300 dark:text-light-300 cursor-not-allowed" readonly>
                    </div>

                    <div class="space-y-2">
                        <label for="phone" class="block text-sm font-medium text-dark dark:text-white">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ Auth::user()->phone }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all">
                    </div>

                    <div class="space-y-2">
                        <label for="dob" class="block text-sm font-medium text-dark dark:text-white">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="{{ Auth::user()->dob }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all">
                    </div>

                    <div class="space-y-2">
                        <label for="country" class="block text-sm font-medium text-dark dark:text-white">Country</label>
                        <input type="text" id="country" name="country" value="{{ Auth::user()->country }}" class="w-full px-4 py-3 rounded-xl bg-light-50 dark:bg-dark-200 border border-light-200 dark:border-dark-200 text-dark-300 dark:text-light-300 cursor-not-allowed" readonly>
                    </div>

                    <div class="space-y-2">
                        <label for="address" class="block text-sm font-medium text-dark dark:text-white">Address</label>
                        <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all">{{ Auth::user()->address }}</textarea>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium flex items-center gap-2 transform transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-primary/20">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M11 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22H15C20 22 22 20 22 15V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.04 3.02001L8.16 10.9C7.86 11.2 7.56 11.79 7.5 12.22L7.07 15.23C6.91 16.32 7.68 17.08 8.77 16.93L11.78 16.5C12.2 16.44 12.79 16.14 13.1 15.84L20.98 7.96001C22.34 6.60001 22.98 5.02001 20.98 3.02001C18.98 1.02001 17.4 1.66001 16.04 3.02001Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M14.91 4.15002C15.58 6.54002 17.45 8.41002 19.85 9.09002" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Update Profile</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Withdrawal Settings Tab -->
        <div class="tab-content hidden" id="withdrawal-tab">
            <form method="post" action="javascript:void(0)" id="updatewithdrawalinfo" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Bank Details Section -->
                <div>
                    <h3 class="text-lg font-bold text-dark dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary" viewBox="0 0 24 24" fill="none">
                            <path d="M18.04 13.55C17.62 13.96 17.38 14.55 17.44 15.18C17.53 16.26 18.52 17.05 19.6 17.05H21.5V18.24C21.5 20.31 19.81 22 17.74 22H6.26C4.19 22 2.5 20.31 2.5 18.24V11.51C2.5 9.44 4.19 7.75 6.26 7.75H17.74C19.81 7.75 21.5 9.44 21.5 11.51V12.95H19.48C18.92 12.95 18.41 13.17 18.04 13.55Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M14.85 4.97V7.75H6.26C4.19 7.75 2.5 9.44 2.5 11.51V6.44C2.5 5.08 3.6 3.97 4.97 3.97H13.88C14.43 3.97 14.85 4.39 14.85 4.97Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M22.5588 13.9702V16.0302C22.5588 16.5802 22.1188 17.0302 21.5588 17.0502H19.5988C18.5188 17.0502 17.5288 16.2602 17.4388 15.1802C17.3788 14.5502 17.6188 13.9602 18.0388 13.5502C18.4088 13.1702 18.9188 12.9502 19.4788 12.9502H21.5588C22.1188 12.9702 22.5588 13.4202 22.5588 13.9702Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 12H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Bank Transfer Details</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="bank_name" class="block text-sm font-medium text-dark dark:text-white">Bank Name</label>
                            <input type="text" id="bank_name" name="bank_name" value="{{ Auth::user()->bank_name }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all" placeholder="Enter bank name">
                        </div>

                        <div class="space-y-2">
                            <label for="account_name" class="block text-sm font-medium text-dark dark:text-white">Account Name</label>
                            <input type="text" id="account_name" name="account_name" value="{{ Auth::user()->account_name }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all" placeholder="Enter account name">
                        </div>

                        <div class="space-y-2">
                            <label for="account_no" class="block text-sm font-medium text-dark dark:text-white">Account Number</label>
                            <input type="text" id="account_no" name="account_no" value="{{ Auth::user()->account_number }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all" placeholder="Enter account number">
                        </div>

                        <div class="space-y-2">
                            <label for="swiftcode" class="block text-sm font-medium text-dark dark:text-white">Swift Code</label>
                            <input type="text" id="swiftcode" name="swiftcode" value="{{ Auth::user()->swift_code }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all" placeholder="Enter swift code">
                        </div>
                    </div>
                </div>

                <!-- Crypto Wallet Addresses Section -->
                <div class="pt-4 border-t border-light-200 dark:border-dark-200/50">
                    <h3 class="text-lg font-bold text-dark dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary" viewBox="0 0 24 24" fill="none">
                            <path d="M15.5789 17.0884C16.0432 18.0666 17.2913 18.4146 18.2695 17.9503C19.2477 17.486 19.5957 16.2379 19.1314 15.2598L16.3586 9.54352C15.8943 8.56534 14.6461 8.21736 13.668 8.68164C12.6898 9.14591 12.3418 10.3941 12.8061 11.3723L15.5789 17.0884Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M8.60851 15.0884C9.07279 16.0666 10.321 16.4146 11.2992 15.9503C12.2774 15.486 12.6254 14.2379 12.1611 13.2598L9.38825 7.54352C8.92397 6.56534 7.67589 6.21736 6.69771 6.68164C5.71953 7.14591 5.37156 8.39399 5.83584 9.37217L8.60851 15.0884Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.60851 15.0884C9.07279 16.0666 10.321 16.4146 11.2992 15.9503C12.2774 15.486 12.6254 14.2379 12.1611 13.2598L9.38825 7.54352C8.92397 6.56534 7.67589 6.21736 6.69771 6.68164C5.71953 7.14591 5.37156 8.39399 5.83584 9.37217L8.60851 15.0884Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.5789 17.0884C16.0432 18.0666 17.2913 18.4146 18.2695 17.9503C19.2477 17.486 19.5957 16.2379 19.1314 15.2598L16.3586 9.54352C15.8943 8.56534 14.6461 8.21736 13.668 8.68164C12.6898 9.14591 12.3418 10.3941 12.8061 11.3723L15.5789 17.0884Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Cryptocurrency Wallets</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="btc_address" class="block text-sm font-medium text-dark dark:text-white flex items-center">
                                <span class="w-6 h-6 mr-2 rounded-full bg-accent-50 dark:bg-accent-900/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-accent" viewBox="0 0 24 24" fill="none">
                                        <path d="M9 10.5H13C14.1 10.5 15 9.6 15 8.5C15 7.4 14.1 6.5 13 6.5H9V10.5Z" fill="currentColor" fill-opacity="0.2"/>
                                        <path d="M9 17.5H14C15.1 17.5 16 16.6 16 15.5C16 14.4 15.1 13.5 14 13.5H9V17.5Z" fill="currentColor" fill-opacity="0.2"/>
                                        <path d="M23 17V15C23 11 21 9 17 9C17 5 15 3 11 3H7C3 3 1 5 1 9V15C1 19 3 21 7 21H17C21 21 23 19 23 15Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M9 10.5H13C14.1 10.5 15 9.6 15 8.5C15 7.4 14.1 6.5 13 6.5H9V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M9 17.5H14C15.1 17.5 16 16.6 16 15.5C16 14.4 15.1 13.5 14 13.5H9V17.5Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                Bitcoin (BTC)
                            </label>
                            <input type="text" id="btc_address" name="btc_address" value="{{ Auth::user()->btc_address }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all font-mono text-sm" placeholder="Enter Bitcoin address">
                            <p class="text-xs text-dark-300 dark:text-light-300">Enter your Bitcoin Address that will be used to withdraw your funds</p>
                        </div>

                        <div class="space-y-2">
                            <label for="eth_address" class="block text-sm font-medium text-dark dark:text-white flex items-center">
                                <span class="w-6 h-6 mr-2 rounded-full bg-tertiary-50 dark:bg-tertiary-900/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-tertiary" viewBox="0 0 24 24" fill="none">
                                        <path d="M5 5V9C5 11.2091 6.79086 13 9 13H15C17.2091 13 19 11.2091 19 9V5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5 5V9C5 11.2091 6.79086 13 9 13H15C17.2091 13 19 11.2091 19 9V5" fill="currentColor" fill-opacity="0.2"/>
                                        <path d="M12 13L12 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5 5H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                Ethereum (ETH)
                            </label>
                            <input type="text" id="eth_address" name="eth_address" value="{{ Auth::user()->eth_address }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all font-mono text-sm" placeholder="Enter Ethereum address">
                            <p class="text-xs text-dark-300 dark:text-light-300">Enter your Ethereum Address that will be used to withdraw your funds</p>
                        </div>

                        <div class="space-y-2">
                            <label for="ltc_address" class="block text-sm font-medium text-dark dark:text-white flex items-center">
                                <span class="w-6 h-6 mr-2 rounded-full bg-secondary-50 dark:bg-secondary-900/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-secondary" viewBox="0 0 24 24" fill="none">
                                        <path d="M9.5 13.7502C9.5 14.7202 10.25 15.5002 11.17 15.5002H13.05C13.85 15.5002 14.5 14.8202 14.5 13.9702C14.5 13.0602 14.1 12.7302 13.51 12.5202L10.5 11.4702C9.91 11.2602 9.51 10.9402 9.51 10.0202C9.51 9.18023 10.16 8.49023 10.96 8.49023H12.84C13.76 8.49023 14.51 9.27023 14.51 10.2402" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 7.5V16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17 3V7H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M22 2L17 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                Litecoin (LTC)
                            </label>
                            <input type="text" id="ltc_address" name="ltc_address" value="{{ Auth::user()->ltc_address }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all font-mono text-sm" placeholder="Enter Litecoin address">
                            <p class="text-xs text-dark-300 dark:text-light-300">Enter your Litecoin Address that will be used to withdraw your funds</p>
                        </div>

                        <div class="space-y-2">
                            <label for="usdt_address" class="block text-sm font-medium text-dark dark:text-white flex items-center">
                                <span class="w-6 h-6 mr-2 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none">
                                        <path d="M8.67188 14.3298C8.67188 15.6198 9.66188 16.6598 10.8919 16.6598H13.4019C14.4719 16.6598 15.3419 15.7498 15.3419 14.6298C15.3419 13.4098 14.8119 12.9798 14.0219 12.6998L9.99187 11.2998C9.20187 11.0198 8.67188 10.5898 8.67188 9.36984C8.67188 8.24984 9.54187 7.33984 10.6119 7.33984H13.1219C14.3519 7.33984 15.3419 8.37984 15.3419 9.66984" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 6V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                USDT (TRC20)
                            </label>
                            <input type="text" id="usdt_address" name="usdt_address" value="{{ Auth::user()->usdt_address }}" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all font-mono text-sm" placeholder="Enter USDT.TRC20 address">
                            <p class="text-xs text-dark-300 dark:text-light-300">Enter your USDT.TRC20 wallet Address that will be used to withdraw your funds</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium flex items-center gap-2 transform transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-primary/20">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M11 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22H15C20 22 22 20 22 15V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.04 3.02001L8.16 10.9C7.86 11.2 7.56 11.79 7.5 12.22L7.07 15.23C6.91 16.32 7.68 17.08 8.77 16.93L11.78 16.5C12.2 16.44 12.79 16.14 13.1 15.84L20.98 7.96001C22.34 6.60001 22.98 5.02001 20.98 3.02001C18.98 1.02001 17.4 1.66001 16.04 3.02001Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M14.91 4.15002C15.58 6.54002 17.45 8.41002 19.85 9.09002" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Password/Security Tab -->
        <div class="tab-content hidden" id="security-tab">
            <form method="POST" action="{{route('updateuserpass')}}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="current_password" class="block text-sm font-medium text-dark dark:text-white">Current Password</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all pr-10" required>
                            <button type="button" class="password-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-dark-300 dark:text-light-300 hover:text-dark dark:hover:text-white focus:outline-none" data-target="current_password">
                                <svg class="w-5 h-5 password-eye-closed" viewBox="0 0 24 24" fill="none">
                                    <path d="M14.53 9.47004L9.47004 14.53C8.82004 13.88 8.42004 12.99 8.42004 12C8.42004 10.02 10.02 8.42004 12 8.42004C12.99 8.42004 13.88 8.82004 14.53 9.47004Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17.82 5.76998C16.07 4.44998 14.07 3.72998 12 3.72998C8.47 3.72998 5.18 5.80998 2.89 9.40998C1.99 10.82 1.99 13.19 2.89 14.6C3.68 15.84 4.6 16.91 5.6 17.77" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.42004 19.5301C9.56004 20.0101 10.77 20.2701 12 20.2701C15.53 20.2701 18.82 18.1901 21.11 14.5901C22.01 13.1801 22.01 10.8101 21.11 9.40005C20.78 8.88005 20.42 8.39005 20.05 7.93005" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15.51 12.7C15.25 14.11 14.1 15.26 12.69 15.52" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.47 14.53L2 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M22 2L14.53 9.47" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <svg class="w-5 h-5 password-eye-open hidden" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 16.01C9.24 16.01 7 13.77 7 11.01C7 8.25 9.24 6.01 12 6.01C14.76 6.01 17 8.25 17 11.01C17 13.77 14.76 16.01 12 16.01Z" fill="currentColor" fill-opacity="0.2"/>
                                    <path d="M12 16.01C9.24 16.01 7 13.77 7 11.01C7 8.25 9.24 6.01 12 6.01C14.76 6.01 17 8.25 17 11.01C17 13.77 14.76 16.01 12 16.01Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 11.01C2 14.87 6.4 19.01 12 19.01C17.6 19.01 22 14.87 22 11.01C22 7.15 17.6 3.01 12 3.01C6.4 3.01 2 7.15 2 11.01Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-dark dark:text-white">New Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all pr-10" required>
                            <button type="button" class="password-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-dark-300 dark:text-light-300 hover:text-dark dark:hover:text-white focus:outline-none" data-target="password">
                                <svg class="w-5 h-5 password-eye-closed" viewBox="0 0 24 24" fill="none">
                                    <path d="M14.53 9.47004L9.47004 14.53C8.82004 13.88 8.42004 12.99 8.42004 12C8.42004 10.02 10.02 8.42004 12 8.42004C12.99 8.42004 13.88 8.82004 14.53 9.47004Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17.82 5.76998C16.07 4.44998 14.07 3.72998 12 3.72998C8.47 3.72998 5.18 5.80998 2.89 9.40998C1.99 10.82 1.99 13.19 2.89 14.6C3.68 15.84 4.6 16.91 5.6 17.77" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.42004 19.5301C9.56004 20.0101 10.77 20.2701 12 20.2701C15.53 20.2701 18.82 18.1901 21.11 14.5901C22.01 13.1801 22.01 10.8101 21.11 9.40005C20.78 8.88005 20.42 8.39005 20.05 7.93005" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15.51 12.7C15.25 14.11 14.1 15.26 12.69 15.52" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.47 14.53L2 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M22 2L14.53 9.47" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <svg class="w-5 h-5 password-eye-open hidden" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 16.01C9.24 16.01 7 13.77 7 11.01C7 8.25 9.24 6.01 12 6.01C14.76 6.01 17 8.25 17 11.01C17 13.77 14.76 16.01 12 16.01Z" fill="currentColor" fill-opacity="0.2"/>
                                    <path d="M12 16.01C9.24 16.01 7 13.77 7 11.01C7 8.25 9.24 6.01 12 6.01C14.76 6.01 17 8.25 17 11.01C17 13.77 14.76 16.01 12 16.01Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 11.01C2 14.87 6.4 19.01 12 19.01C17.6 19.01 22 14.87 22 11.01C22 7.15 17.6 3.01 12 3.01C6.4 3.01 2 7.15 2 11.01Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="password-strength mt-1 hidden">
                            <div class="h-1 w-full bg-light-200 dark:bg-dark-200 rounded-full overflow-hidden">
                                <div class="password-strength-bar h-full bg-danger w-0 transition-all duration-300"></div>
                            </div>
                            <p class="text-xs mt-1 text-dark-300 dark:text-light-300 password-strength-text"></p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-dark dark:text-white">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 rounded-xl bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all pr-10" required>
                            <button type="button" class="password-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-dark-300 dark:text-light-300 hover:text-dark dark:hover:text-white focus:outline-none" data-target="password_confirmation">
                                <svg class="w-5 h-5 password-eye-closed" viewBox="0 0 24 24" fill="none">
                                    <path d="M14.53 9.47004L9.47004 14.53C8.82004 13.88 8.42004 12.99 8.42004 12C8.42004 10.02 10.02 8.42004 12 8.42004C12.99 8.42004 13.88 8.82004 14.53 9.47004Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17.82 5.76998C16.07 4.44998 14.07 3.72998 12 3.72998C8.47 3.72998 5.18 5.80998 2.89 9.40998C1.99 10.82 1.99 13.19 2.89 14.6C3.68 15.84 4.6 16.91 5.6 17.77" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.42004 19.5301C9.56004 20.0101 10.77 20.2701 12 20.2701C15.53 20.2701 18.82 18.1901 21.11 14.5901C22.01 13.1801 22.01 10.8101 21.11 9.40005C20.78 8.88005 20.42 8.39005 20.05 7.93005" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15.51 12.7C15.25 14.11 14.1 15.26 12.69 15.52" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.47 14.53L2 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M22 2L14.53 9.47" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <svg class="w-5 h-5 password-eye-open hidden" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 16.01C9.24 16.01 7 13.77 7 11.01C7 8.25 9.24 6.01 12 6.01C14.76 6.01 17 8.25 17 11.01C17 13.77 14.76 16.01 12 16.01Z" fill="currentColor" fill-opacity="0.2"/>
                                    <path d="M12 16.01C9.24 16.01 7 13.77 7 11.01C7 8.25 9.24 6.01 12 6.01C14.76 6.01 17 8.25 17 11.01C17 13.77 14.76 16.01 12 16.01Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 11.01C2 14.87 6.4 19.01 12 19.01C17.6 19.01 22 14.87 22 11.01C22 7.15 17.6 3.01 12 3.01C6.4 3.01 2 7.15 2 11.01Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-light-100 dark:bg-dark-100/50 border border-light-200 dark:border-dark-200/50">
                    <h4 class="text-sm font-medium text-dark dark:text-white mb-2">Password Requirements</h4>
                    <ul class="space-y-1">
                        <li class="flex items-center text-xs text-dark-300 dark:text-light-300">
                            <svg class="w-4 h-4 mr-2 text-secondary" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M7.75 12L10.58 14.83L16.25 9.17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Must be at least 8 characters long</span>
                        </li>
                        <li class="flex items-center text-xs text-dark-300 dark:text-light-300">
                            <svg class="w-4 h-4 mr-2 text-secondary" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M7.75 12L10.58 14.83L16.25 9.17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Should contain at least one uppercase letter</span>
                        </li>
                        <li class="flex items-center text-xs text-dark-300 dark:text-light-300">
                            <svg class="w-4 h-4 mr-2 text-secondary" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M7.75 12L10.58 14.83L16.25 9.17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Should contain at least one number</span>
                        </li>
                        <li class="flex items-center text-xs text-dark-300 dark:text-light-300">
                            <svg class="w-4 h-4 mr-2 text-secondary" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M7.75 12L10.58 14.83L16.25 9.17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Should contain at least one special character</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium flex items-center gap-2 transform transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-primary/20">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M10.49 2.23006L5.50003 4.10004C4.35003 4.53004 3.41003 5.89004 3.41003 7.12004V14.55C3.41003 15.73 4.19005 17.28 5.14005 17.99L9.44003 21.2C10.85 22.26 13.17 22.26 14.58 21.2L18.88 17.99C19.83 17.28 20.61 15.73 20.61 14.55V7.12004C20.61 5.89004 19.67 4.53004 18.52 4.10004L13.53 2.23006C12.68 1.92006 11.32 1.92006 10.49 2.23006Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M12 15.5C13.3807 15.5 14.5 14.3807 14.5 13C14.5 11.6193 13.3807 10.5 12 10.5C10.6193 10.5 9.5 11.6193 9.5 13C9.5 14.3807 10.6193 15.5 12 15.5Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 10.5V8" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Update Password</span>
                    </button>
                </div>

                <div class="pt-4 mt-6 border-t border-light-200 dark:border-dark-200/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-dark dark:text-white">Advanced Security</h3>
                        <a href="{{ route('twofa') }}" class="px-4 py-2 rounded-lg bg-tertiary text-white text-sm font-medium hover:bg-tertiary-600 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                <path d="M10.49 2.23006L5.50003 4.10004C4.35003 4.53004 3.41003 5.89004 3.41003 7.12004V14.55C3.41003 15.73 4.19005 17.28 5.14005 17.99L9.44003 21.2C10.85 22.26 13.17 22.26 14.58 21.2L18.88 17.99C19.83 17.28 20.61 15.73 20.61 14.55V7.12004C20.61 5.89004 19.67 4.53004 18.52 4.10004L13.53 2.23006C12.68 1.92006 11.32 1.92006 10.49 2.23006Z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M10.49 2.23006L5.50003 4.10004C4.35003 4.53004 3.41003 5.89004 3.41003 7.12004V14.55C3.41003 15.73 4.19005 17.28 5.14005 17.99L9.44003 21.2C10.85 22.26 13.17 22.26 14.58 21.2L18.88 17.99C19.83 17.28 20.61 15.73 20.61 14.55V7.12004C20.61 5.89004 19.67 4.53004 18.52 4.10004L13.53 2.23006C12.68 1.92006 11.32 1.92006 10.49 2.23006Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Configure 2FA
                        </a>
                    </div>
                    <p class="mt-2 text-sm text-dark-300 dark:text-light-300">
                        Enhance your account security with two-factor authentication, device management, and login history.
                    </p>
                </div>
            </form>
        </div>

        <!-- Preferences Tab -->
        <div class="tab-content hidden" id="preferences-tab">
            <form method="POST" action="javascript:void(0)" id="updateemailpref" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-lg font-bold text-dark dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary" viewBox="0 0 24 24" fill="none">
                            <path d="M17 20.5H7C4 20.5 2 19 2 15.5V8.5C2 5 4 3.5 7 3.5H17C20 3.5 22 5 22 8.5V15.5C22 19 20 20.5 17 20.5Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M17 9L13.87 11.5C12.84 12.32 11.15 12.32 10.12 11.5L7 9" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 8.5V15.5C2 19 4 20.5 7 20.5H17C20 20.5 22 19 22 15.5V8.5C22 5 20 3.5 17 3.5H7C4 3.5 2 5 2 8.5Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Email Notifications</span>
                    </h3>

                    <div class="space-y-4">
                        {{-- <div class="p-4 rounded-xl bg-light-50 dark:bg-dark-100 border border-light-200 dark:border-dark-200/50">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <h4 class="text-base font-medium text-dark dark:text-white">Withdrawal Confirmation</h4>
                                    <p class="text-sm text-dark-300 dark:text-light-300">Send confirmation OTP to my email when withdrawing funds</p>
                                </div>
                                <div class="flex items-center">
                                    <label class="inline-flex items-center mr-4">
                                        <input type="radio" name="otpsend" id="otpsendYes" value="Yes" class="hidden peer" {{ Auth::user()->sendotpemail == 'Yes' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center w-16 h-8 rounded-full bg-light-200 dark:bg-dark-200 text-dark-300 dark:text-light-300 text-sm font-medium cursor-pointer peer-checked:bg-secondary peer-checked:text-white transition-colors">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="otpsend" id="otpsendNo" value="No" class="hidden peer" {{ Auth::user()->sendotpemail != 'Yes' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center w-16 h-8 rounded-full bg-light-200 dark:bg-dark-200 text-dark-300 dark:text-light-300 text-sm font-medium cursor-pointer peer-checked:bg-danger peer-checked:text-white transition-colors">No</span>
                                    </label>
                                </div>
                            </div>
                        </div> --}}

                        <div class="p-4 rounded-xl bg-light-50 dark:bg-dark-100 border border-light-200 dark:border-dark-200/50">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <h4 class="text-base font-medium text-dark dark:text-white">Profit Notifications</h4>
                                    <p class="text-sm text-dark-300 dark:text-light-300">Send me email when I get profit from investments</p>
                                </div>
                                <div class="flex items-center">
                                    <label class="inline-flex items-center mr-4">
                                        <input type="radio" name="roiemail" id="roiemailYes" value="Yes" class="hidden peer" {{ Auth::user()->sendroiemail == 'Yes' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center w-16 h-8 rounded-full bg-light-200 dark:bg-dark-200 text-dark-300 dark:text-light-300 text-sm font-medium cursor-pointer peer-checked:bg-secondary peer-checked:text-white transition-colors">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="roiemail" id="roiemailNo" value="No" class="hidden peer" {{ Auth::user()->sendroiemail != 'Yes' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center w-16 h-8 rounded-full bg-light-200 dark:bg-dark-200 text-dark-300 dark:text-light-300 text-sm font-medium cursor-pointer peer-checked:bg-danger peer-checked:text-white transition-colors">No</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-light-50 dark:bg-dark-100 border border-light-200 dark:border-dark-200/50">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <h4 class="text-base font-medium text-dark dark:text-white">Plan Expiration</h4>
                                    <p class="text-sm text-dark-300 dark:text-light-300">Send me email when my investment plan expires</p>
                                </div>
                                <div class="flex items-center">
                                    <label class="inline-flex items-center mr-4">
                                        <input type="radio" name="invplanemail" id="invplanemailYes" value="Yes" class="hidden peer" {{ Auth::user()->sendinvplanemail == 'Yes' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center w-16 h-8 rounded-full bg-light-200 dark:bg-dark-200 text-dark-300 dark:text-light-300 text-sm font-medium cursor-pointer peer-checked:bg-secondary peer-checked:text-white transition-colors">Yes</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="invplanemail" id="invplanemailNo" value="No" class="hidden peer" {{ Auth::user()->sendinvplanemail != 'Yes' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center w-16 h-8 rounded-full bg-light-200 dark:bg-dark-200 text-dark-300 dark:text-light-300 text-sm font-medium cursor-pointer peer-checked:bg-danger peer-checked:text-white transition-colors">No</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-secondary hover:from-primary-600 hover:to-secondary-600 text-white font-medium flex items-center gap-2 transform transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-primary/20">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M11 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22H15C20 22 22 20 22 15V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.04 3.02001L8.16 10.9C7.86 11.2 7.56 11.79 7.5 12.22L7.07 15.23C6.91 16.32 7.68 17.08 8.77 16.93L11.78 16.5C12.2 16.44 12.79 16.14 13.1 15.84L20.98 7.96001C22.34 6.60001 22.98 5.02001 20.98 3.02001C18.98 1.02001 17.4 1.66001 16.04 3.02001Z" fill="currentColor" fill-opacity="0.2"/>
                            <path d="M14.91 4.15002C15.58 6.54002 17.45 8.41002 19.85 9.09002" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Save Preferences</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Animated floating elements */
    .floating-element {
        position: absolute;
        border-radius: 50%;
        opacity: 0.7;
        animation-duration: 15s;
        animation-iteration-count: infinite;
        animation-timing-function: ease-in-out;
    }

    .elem-1 {
        width: 100px;
        height: 100px;
        top: 10%;
        left: 5%;
        animation-name: float1;
    }

    .elem-2 {
        width: 70px;
        height: 70px;
        top: 20%;
        right: 10%;
        animation-name: float2;
    }

    .elem-3 {
        width: 50px;
        height: 50px;
        bottom: 20%;
        left: 15%;
        animation-name: float3;
    }

    .elem-4 {
        width: 80px;
        height: 80px;
        bottom: 10%;
        right: 5%;
        animation-name: float4;
    }

    @keyframes float1 {
        0% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(15px, 15px) rotate(90deg); }
        50% { transform: translate(0, 30px) rotate(180deg); }
        75% { transform: translate(-15px, 15px) rotate(270deg); }
        100% { transform: translate(0, 0) rotate(360deg); }
    }

    @keyframes float2 {
        0% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(-20px, 10px) rotate(-90deg); }
        50% { transform: translate(0, 20px) rotate(-180deg); }
        75% { transform: translate(20px, 10px) rotate(-270deg); }
        100% { transform: translate(0, 0) rotate(-360deg); }
    }

    @keyframes float3 {
        0% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(15px, -15px) rotate(120deg); }
        66% { transform: translate(-15px, -15px) rotate(240deg); }
        100% { transform: translate(0, 0) rotate(360deg); }
    }

    @keyframes float4 {
        0% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(-20px, -10px) rotate(-120deg); }
        66% { transform: translate(20px, -20px) rotate(-240deg); }
        100% { transform: translate(0, 0) rotate(-360deg); }
    }

    /* Hide scrollbar for tab navigation on mobile */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<!-- JavaScript for tabs and other interactions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Switching
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');

                // Reset all tabs
                tabButtons.forEach(btn => btn.classList.remove('active', 'border-primary', 'text-primary', 'dark:text-primary-400'));
                tabButtons.forEach(btn => btn.classList.add('border-transparent', 'text-dark-300', 'dark:text-light-300'));
                tabContents.forEach(content => content.classList.add('hidden'));

                // Set active tab
                button.classList.add('active', 'border-primary', 'text-primary', 'dark:text-primary-400');
                button.classList.remove('border-transparent', 'text-dark-300', 'dark:text-light-300');
                document.getElementById(`${tabId}-tab`).classList.remove('hidden');
            });
        });

        // Password Visibility Toggle
        const passwordToggles = document.querySelectorAll('.password-toggle');
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const eyeOpen = this.querySelector('.password-eye-open');
                const eyeClosed = this.querySelector('.password-eye-closed');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                } else {
                    passwordInput.type = 'password';
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                }
            });
        });

        // Password Strength Meter
        const passwordInput = document.getElementById('password');
        const strengthBar = document.querySelector('.password-strength-bar');
        const strengthText = document.querySelector('.password-strength-text');
        const strengthContainer = document.querySelector('.password-strength');

        if (passwordInput && strengthBar && strengthText && strengthContainer) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let message = '';

                strengthContainer.classList.remove('hidden');

                // Calculate password strength
                if (password.length >= 8) strength += 25;
                if (password.match(/[A-Z]/)) strength += 25;
                if (password.match(/[0-9]/)) strength += 25;
                if (password.match(/[^A-Za-z0-9]/)) strength += 25;

                // Update strength bar
                strengthBar.style.width = `${strength}%`;

                // Change color based on strength
                if (strength <= 25) {
                    strengthBar.className = 'password-strength-bar h-full bg-danger w-0 transition-all duration-300';
                    message = 'Weak password';
                } else if (strength <= 50) {
                    strengthBar.className = 'password-strength-bar h-full bg-accent w-0 transition-all duration-300';
                    message = 'Moderate password';
                } else if (strength <= 75) {
                    strengthBar.className = 'password-strength-bar h-full bg-tertiary w-0 transition-all duration-300';
                    message = 'Good password';
                } else {
                    strengthBar.className = 'password-strength-bar h-full bg-secondary w-0 transition-all duration-300';
                    message = 'Strong password';
                }

                // Update strength text
                strengthText.textContent = message;
            });
        }

        // Ajax for profile update
        const profileForm = document.getElementById('updateprofileform');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                      </svg> Updating...`;
                submitBtn.disabled = true;

                fetch("{{ route('profile.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new URLSearchParams(new FormData(this))
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    if (data.status === 200) {
                        showToast('success', 'Success', data.success);
                    } else {
                        showToast('danger', 'Error', 'Something went wrong. Please try again.');
                    }
                })
                .catch(error => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    showToast('danger', 'Error', 'An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            });
        }

        // Ajax for withdrawal method update
        const withdrawalForm = document.getElementById('updatewithdrawalinfo');
        if (withdrawalForm) {
            withdrawalForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                      </svg> Saving...`;
                submitBtn.disabled = true;

                fetch("{{ route('updateacount') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new URLSearchParams(new FormData(this))
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    if (data.status === 200) {
                        showToast('success', 'Success', data.success);
                    } else {
                        showToast('danger', 'Error', 'Something went wrong. Please try again.');
                    }
                })
                .catch(error => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    showToast('danger', 'Error', 'An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            });
        }

        // Ajax for email preferences update
        const emailPrefForm = document.getElementById('updateemailpref');
        if (emailPrefForm) {
            emailPrefForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                      </svg> Saving...`;
                submitBtn.disabled = true;

                fetch("{{ route('updateemail') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new URLSearchParams(new FormData(this))
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    if (data.status === 200) {
                        showToast('success', 'Success', data.success);
                    } else {
                        showToast('danger', 'Error', 'Something went wrong. Please try again.');
                    }
                })
                .catch(error => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    showToast('danger', 'Error', 'An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            });
        }
    });

    // Toast notification system
    function showToast(type, title, message) {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-3';
            document.body.appendChild(toastContainer);
        }

        const isDarkMode = document.documentElement.classList.contains('dark') ||
                          window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Determine toast class based on type and dark mode
        let toastClass = type;
        if (isDarkMode) {
            toastClass = 'dark-' + type;
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${toastClass}`;

        // Set icon based on type
        let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        // Build toast HTML
        toast.innerHTML = `
            <div class="icon">
                <i class="fas ${icon}"></i>
            </div>
            <div class="content">
                <div class="title">${title}</div>
                <div class="message">${message}</div>
            </div>
            <div class="close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </div>
            <div class="progress">
                <div class="progress-bar"></div>
            </div>
        `;

        // Add to container
        toastContainer.appendChild(toast);

        // Remove after animation completes
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
</script>

<!-- Toast CSS -->
<style>
    /* Modern Toast Notifications */
    .toast {
        min-width: 300px;
        max-width: 400px;
        border-radius: 10px;
        margin-bottom: 1rem;
        padding: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateX(100%);
        animation: slideIn 0.3s forwards, fadeOut 0.5s 4.5s forwards;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .toast.success {
        background: linear-gradient(to right, #E8F5F0, #D1EBE1);
        border-left: 5px solid #4A9D7F;
        color: #2E614D;
    }

    .toast.success .icon {
        background-color: #A3D7C3;
        color: #3C7F65;
    }

    .toast.danger {
        background: linear-gradient(to right, #FFF0F0, #FFE0E0);
        border-left: 5px solid #FF6B6B;
        color: #D10000;
    }

    .toast.danger .icon {
        background-color: #FFC2C2;
        color: #FF3838;
    }

    .toast.dark-success {
        background: linear-gradient(to right, rgba(46, 97, 77, 0.8), rgba(33, 68, 53, 0.6));
        border-left: 5px solid #4A9D7F;
        color: #ffffff;
    }

    .toast.dark-success .icon {
        background-color: rgba(74, 157, 127, 0.3);
        color: #76C3A5;
    }

    .toast.dark-danger {
        background: linear-gradient(to right, rgba(209, 0, 0, 0.8), rgba(158, 0, 0, 0.6));
        border-left: 5px solid #FF6B6B;
        color: #ffffff;
    }

    .toast.dark-danger .icon {
        background-color: rgba(255, 56, 56, 0.3);
        color: #FFA3A3;
    }

    .toast .icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    .toast .content {
        flex-grow: 1;
    }

    .toast .title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .toast .close {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.15s ease;
    }

    .toast .close:hover {
        opacity: 1;
    }

    .toast .progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
        background: rgba(255, 255, 255, 0.3);
    }

    .toast .progress-bar {
        height: 100%;
        background: currentColor;
        animation: progress 5s linear forwards;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }

    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }

    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>
@endsection
