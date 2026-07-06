@extends('layouts.dash')
@section('title', $title)
@section('content')

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col items-end space-y-4"></div>

<!-- Meta Tag for CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Page Container -->
<div class="container-fluid" style="max-width: 1400px;">
    <!-- Breadcrumb Navigation -->
    <div class="flex items-center text-sm text-dark-400 dark:text-light-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <svg class="w-4 h-4 mx-2" viewBox="0 0 16 16" fill="none">
            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <a href="#" class="hover:text-primary transition-colors">Verification</a>
        <svg class="w-4 h-4 mx-2" viewBox="0 0 16 16" fill="none">
            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="text-dark dark:text-white">KYC Form</span>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-5">
        <div class="flex items-center mb-4 sm:mb-0">
            <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M14.44 19.0489L14.52 19.0009C15.0055 18.6196 15.6799 18.5378 16.2439 18.7909C17.3599 19.2659 18.6699 19.2659 19.7789 18.7909C20.3589 18.5249 21.0049 18.6149 21.5049 19.0149C22.3309 19.6809 23.4099 19.0309 23.4099 17.9909V9.05093C23.4099 8.43993 22.9599 7.87994 22.3909 7.67094C21.3499 7.27094 20.1499 7.27094 19.1099 7.67094L19.0799 7.67993C18.9599 7.72993 18.9099 7.84993 18.9599 7.96993C18.9799 8.01993 19.0299 8.06094 19.0799 8.07094L19.1499 8.09094C19.6399 8.25094 20.1199 8.46995 20.5699 8.73995C20.7699 8.86995 20.5699 9.17993 20.3399 9.09993C20.1399 9.02993 19.9199 8.97094 19.7099 8.92094C18.7799 8.69094 17.7599 8.69094 16.8299 8.92094C15.9099 9.15094 14.9499 9.15093 14.0299 8.92093C13.0999 8.69093 12.0799 8.69093 11.1499 8.92093C10.2299 9.15093 9.26994 9.15094 8.34994 8.92094C7.41994 8.69094 6.39994 8.69094 5.46994 8.92094C5.25994 8.97094 5.05994 9.02993 4.85994 9.09993C4.62994 9.17993 4.42994 8.86995 4.62994 8.73995C5.07994 8.46995 5.55994 8.25094 6.04994 8.09094C6.14994 8.05094 6.23994 7.97993 6.22994 7.86993C6.21994 7.71993 6.07994 7.62094 5.93994 7.66094L5.88994 7.67993C4.84994 8.07993 3.64994 8.07993 2.60994 7.67993C2.03994 7.47993 1.58994 8.03993 1.58994 8.64993V17.9909C1.58994 19.0309 2.66994 19.6809 3.49994 19.0149C3.99994 18.6149 4.64994 18.5249 5.21994 18.7909C6.32994 19.2659 7.63994 19.2659 8.74994 18.7909C9.31994 18.5379 9.99436 18.6196 10.48 19.0009L10.56 19.0489C11.77 19.9169 13.23 19.9169 14.44 19.0489Z" fill="currentColor" fill-opacity="0.15"/>
                    <path d="M22.5 10.96V17.91C22.5 18.36 22.12 18.97 21.71 19.22C21.11 19.59 20.3 19.6 19.68 19.38C18.92 19.12 18.02 19.12 17.26 19.38C16.63 19.61 15.81 19.59 15.21 19.21C14.83 18.96 14.17 18.96 13.79 19.21C13.19 19.59 12.37 19.61 11.74 19.38C10.98 19.12 10.08 19.12 9.32 19.38C8.69 19.61 7.87 19.59 7.27 19.21C6.89 18.96 6.23 18.96 5.85 19.21C5.25 19.59 4.43 19.61 3.8 19.38C3.04 19.12 2.14 19.11 1.38 19.38C0.94 19.55 0.5 19.22 0.5 18.76V10.96C0.5 10.51 0.88 9.9 1.29 9.65C1.89 9.28 2.7 9.27001 3.32 9.49001C4.08 9.75001 4.98 9.75001 5.74 9.49001C6.37 9.26001 7.19 9.28 7.79 9.66C8.17 9.91 8.83 9.91 9.21 9.66C9.81 9.28 10.63 9.26001 11.26 9.49001C12.02 9.75001 12.92 9.75001 13.68 9.49001C14.31 9.26001 15.13 9.28 15.73 9.66C16.11 9.91 16.77 9.91 17.15 9.66C17.75 9.28 18.57 9.26001 19.2 9.49001C19.96 9.75001 20.86 9.76 21.62 9.49C22.06 9.32 22.5 9.65 22.5 10.96Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 15.5H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 12.5H11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-dark dark:text-white">KYC Verification Form</h1>
        </div>
    </div>

    <!-- Alert Messages -->
    <x-danger-alert/>
    <x-success-alert/>
    <x-error-alert/>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto">
        <!-- KYC Form Card -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden mb-6">
            <div class="p-6 text-center border-b border-light-200 dark:border-dark-200/50">
                <h2 class="text-lg font-bold text-dark dark:text-white mb-2">Begin Your ID-Verification</h2>
                <p class="text-dark-500 dark:text-light-400">To comply with regulations, each participant will have to go through identity verification (KYC/AML) to prevent fraud.</p>
            </div>

            <div class="p-6">
                <form action="{{ route('kycsubmit') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- Personal Details Section -->
                    <div class="space-y-4">
                        <div class="flex items-center pb-4 border-b border-light-200 dark:border-dark-200/50">
                            <svg class="w-5 h-5 text-primary mr-2" viewBox="0 0 24 24" fill="none">
                                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20.5899 22C20.5899 18.13 16.7399 15 11.9999 15C7.25991 15 3.40991 18.13 3.40991 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3 class="text-base font-bold text-dark dark:text-white">Personal Details</h3>
                        </div>

                        <p class="text-sm text-dark-500 dark:text-light-400">Your simple personal information required for identification</p>

                        <div class="bg-light-50 dark:bg-dark-200/30 rounded-lg p-4">
                            <p class="text-sm text-amber-600 dark:text-amber-400 flex items-start">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M11.9945 16H12.0035" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Please type carefully and fill out the form with your personal details. You can't edit these details once you've submitted the form.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="first_name" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="first_name" id="first_name" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>

                            <div class="space-y-2">
                                <label for="last_name" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="last_name" id="last_name" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>





                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>

                            <div class="space-y-2">
                                <label for="phone_number" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="phone_number" id="phone_number" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>

                            <div class="space-y-2">
                                <label for="dob" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    Date of Birth <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="dob" id="dob" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>



                            <div class="space-y-2">
                                <label for="social_media" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    Twitter or Facebook Username <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="social_media" id="social_media" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center pb-4 border-b border-light-200 dark:border-dark-200/50">
                            <svg class="w-5 h-5 text-primary mr-2" viewBox="0 0 24 24" fill="none">
                                <path d="M12 13.4299C13.7231 13.4299 15.12 12.0331 15.12 10.3099C15.12 8.58681 13.7231 7.18994 12 7.18994C10.2769 7.18994 8.88 8.58681 8.88 10.3099C8.88 12.0331 10.2769 13.4299 12 13.4299Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.62001 8.49C5.59001 -0.169998 18.42 -0.159997 20.38 8.5C21.53 13.58 18.37 17.88 15.6 20.54C13.59 22.48 10.41 22.48 8.39001 20.54C5.63001 17.88 2.47001 13.57 3.62001 8.49Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3 class="text-base font-bold text-dark dark:text-white">Your Address</h3>
                        </div>

                        <p class="text-sm text-dark-500 dark:text-light-400">Your simple location information required for identification</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="address" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    Address Line <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="address" id="address" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>

                            <div class="space-y-2">
                                <label for="city" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="city" id="city" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>

                            <div class="space-y-2">
                                <label for="state" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    State <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="state" id="state" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>

                            <div class="space-y-2">
                                <label for="country" class="block text-sm font-medium text-dark-600 dark:text-light-200">
                                    Nationality <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="country" id="country" class="w-full px-4 py-2.5 rounded-lg border border-light-200 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:border-primary dark:focus:border-primary focus:ring-0 transition-colors" required>
                            </div>
                        </div>
                    </div>

                    <!-- Document Upload Section -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center pb-4 border-b border-light-200 dark:border-dark-200/50">
                            <svg class="w-5 h-5 text-primary mr-2" viewBox="0 0 24 24" fill="none">
                                <path d="M9 17V11L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 11L11 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22 10V15C22 20 20 22 15 22H9C4 22 2 20 2 15V9C2 4 4 2 9 2H14" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22 10H18C15 10 14 9 14 6V2L22 10Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3 class="text-base font-bold text-dark dark:text-white">Document Upload</h3>
                        </div>

                        <p class="text-sm text-dark-500 dark:text-light-400">Your simple personal document required for identification</p>

                        <!-- Document Type Selection -->
                        <div class="space-y-4">
                            <div class="flex flex-wrap justify-center gap-3">
                                <label class="relative flex flex-col items-center p-4 rounded-xl bg-white dark:bg-dark-100 border-2 border-light-200 dark:border-dark-200 hover:border-primary dark:hover:border-primary group cursor-pointer transition-colors">
                                    <input type="radio" name="document_type" value="Int'l Passport" class="absolute opacity-0" checked>
                                    <div class="w-12 h-12 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-primary" viewBox="0 0 24 24" fill="none">
                                            <path d="M13.5 20H6.5C5.11929 20 4 18.8807 4 17.5C4 16.1193 5.11929 15 6.5 15H13.5" fill="currentColor" fill-opacity="0.15"/>
                                            <path d="M13.5 20H6.5C5.11929 20 4 18.8807 4 17.5C4 16.1193 5.11929 15 6.5 15H13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M19 15H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M19 18H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <rect x="8" y="4" width="12" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                            <circle cx="14" cy="10" r="2" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-dark dark:text-white">Int'l Passport</span>
                                    <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center opacity-0 group-has-[input:checked]:opacity-100 transition-opacity">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </label>

                                <label class="relative flex flex-col items-center p-4 rounded-xl bg-white dark:bg-dark-100 border-2 border-light-200 dark:border-dark-200 hover:border-primary dark:hover:border-primary group cursor-pointer transition-colors">
                                    <input type="radio" name="document_type" value="National ID" class="absolute opacity-0">
                                    <div class="w-12 h-12 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-primary" viewBox="0 0 24 24" fill="none">
                                            <rect x="2" y="4" width="20" height="16" rx="4" fill="currentColor" fill-opacity="0.15"/>
                                            <rect x="2" y="4" width="20" height="16" rx="4" stroke="currentColor" stroke-width="1.5"/>
                                            <circle cx="9" cy="10" r="2" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M13 8H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M13 12H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M5 16C5 16 6.5 14 9 14C11.5 14 13 16 13 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M15 16H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-dark dark:text-white">National ID</span>
                                    <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center opacity-0 group-has-[input:checked]:opacity-100 transition-opacity">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </label>

                                <label class="relative flex flex-col items-center p-4 rounded-xl bg-white dark:bg-dark-100 border-2 border-light-200 dark:border-dark-200 hover:border-primary dark:hover:border-primary group cursor-pointer transition-colors">
                                    <input type="radio" name="document_type" value="Drivers License" class="absolute opacity-0">
                                    <div class="w-12 h-12 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-primary" viewBox="0 0 24 24" fill="none">
                                            <rect x="2" y="3" width="20" height="18" rx="3" fill="currentColor" fill-opacity="0.15"/>
                                            <rect x="2" y="3" width="20" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M16 14.5C16 12.0147 13.9853 10 11.5 10C9.01472 10 7 12.0147 7 14.5" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M7 7.5H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M11.5 14.5V14.51" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-dark dark:text-white">Driver's License</span>
                                    <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center opacity-0 group-has-[input:checked]:opacity-100 transition-opacity">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </label>
                            </div>

                            <!-- Document Guidelines -->
                            <div class="bg-light-50 dark:bg-dark-200/30 rounded-lg p-4">
                                <h4 class="text-sm font-bold text-dark dark:text-white mb-3">To avoid delays when verifying your account, please make sure your document meets the criteria below:</h4>
                                <ul class="space-y-2">
                                    <li class="flex items-start text-sm text-dark-600 dark:text-light-300">
                                        <svg class="w-5 h-5 text-primary mr-2 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                            <path d="M9 11L12 14L20 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12C4 7.58172 7.58172 4 12 4C12.9473 4 13.8561 4.16464 14.6993 4.46686" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Chosen credential must not have expired.
                                    </li>
                                    <li class="flex items-start text-sm text-dark-600 dark:text-light-300">
                                        <svg class="w-5 h-5 text-primary mr-2 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                            <path d="M9 11L12 14L20 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12C4 7.58172 7.58172 4 12 4C12.9473 4 13.8561 4.16464 14.6993 4.46686" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Document should be in good condition and clearly visible.
                                    </li>
                                    <li class="flex items-start text-sm text-dark-600 dark:text-light-300">
                                        <svg class="w-5 h-5 text-primary mr-2 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                            <path d="M9 11L12 14L20 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12C4 7.58172 7.58172 4 12 4C12.9473 4 13.8561 4.16464 14.6993 4.46686" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Make sure that there is no light glare on the card.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Front Side Upload -->
                    <div class="space-y-3">
                        <h4 class="text-base font-medium text-dark dark:text-white">Upload Front Side</h4>
                        <div class="flex flex-col md:flex-row items-center gap-6 p-4 border border-dashed border-light-300 dark:border-dark-200 rounded-lg">
                            <div class="w-full md:w-1/2">
                                <label for="frontimg" class="flex flex-col items-center justify-center w-full h-40 bg-light-50 dark:bg-dark-200/50 rounded-lg cursor-pointer hover:bg-light-100 dark:hover:bg-dark-300/50 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-10 h-10 mb-3 text-dark-400 dark:text-light-500" viewBox="0 0 24 24" fill="none">
                                            <path d="M9 17V11L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9 11L11 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M22 10V15C22 20 20 22 15 22H9C4 22 2 20 2 15V9C2 4 4 2 9 2H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M22 10H18C15 10 14 9 14 6V2L22 10Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-dark-500 dark:text-light-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-dark-400 dark:text-light-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                    </div>
                                    <input id="frontimg" name="frontimg" type="file" class="hidden" required />
                                </label>
                            </div>
                            <div class="flex-shrink-0 flex items-center justify-center">
                                <div class="w-24 h-24 bg-light-100 dark:bg-dark-300/50 rounded-lg flex items-center justify-center">
                                    <svg class="w-16 h-16 text-dark-300 dark:text-light-600" viewBox="0 0 24 24" fill="none">
                                        <rect x="2" y="4" width="20" height="16" rx="4" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="9" cy="10" r="2" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M13 8H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M13 12H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M5 16C5 16 6.5 14 9 14C11.5 14 13 16 13 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M15 16H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Back Side Upload -->
                    <div class="space-y-3">
                        <h4 class="text-base font-medium text-dark dark:text-white">Upload Back Side</h4>
                        <div class="flex flex-col md:flex-row items-center gap-6 p-4 border border-dashed border-light-300 dark:border-dark-200 rounded-lg">
                            <div class="w-full md:w-1/2">
                                <label for="backimg" class="flex flex-col items-center justify-center w-full h-40 bg-light-50 dark:bg-dark-200/50 rounded-lg cursor-pointer hover:bg-light-100 dark:hover:bg-dark-300/50 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-10 h-10 mb-3 text-dark-400 dark:text-light-500" viewBox="0 0 24 24" fill="none">
                                            <path d="M9 17V11L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9 11L11 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M22 10V15C22 20 20 22 15 22H9C4 22 2 20 2 15V9C2 4 4 2 9 2H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M22 10H18C15 10 14 9 14 6V2L22 10Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-dark-500 dark:text-light-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-dark-400 dark:text-light-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                    </div>
                                    <input id="backimg" name="backimg" type="file" class="hidden" required />
                                </label>
                            </div>
                            <div class="flex-shrink-0 flex items-center justify-center">
                                <div class="w-24 h-24 bg-light-100 dark:bg-dark-300/50 rounded-lg flex items-center justify-center">
                                    <svg class="w-16 h-16 text-dark-300 dark:text-light-600" viewBox="0 0 24 24" fill="none">
                                        <rect x="2" y="3" width="20" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M7 9H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M7 13H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M7 17H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="pt-6 pb-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" required class="w-5 h-5 rounded border-light-300 dark:border-dark-300 text-primary focus:ring-primary dark:focus:ring-offset-dark-100 dark:focus:ring-offset-2 dark:checked:bg-primary focus:ring-2">
                            <span class="text-dark-600 dark:text-light-300">All The Information I Have Entered Is Correct.</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        @if (Auth::user()->account_verify == 'Under review')
                        <div class="space-y-3">
                            <button type="submit" class="w-full md:w-auto px-6 py-3 rounded-lg bg-primary/50 text-white cursor-not-allowed opacity-70" disabled>
                                Submit Application
                            </button>
                            <p class="text-sm text-green-500 dark:text-green-400">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Your previous application is under review, please wait
                            </p>
                        </div>
                        @else
                        <button type="submit" class="w-full md:w-auto px-6 py-3 rounded-lg bg-primary text-white hover:bg-primary-600 transition-colors inline-flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none">
                                <path d="M8.90002 16.8L10.8 18.7C11.2 19.1 11.9 19.1 12.3 18.7L15.3 15.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Submit Application
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts for Toast Notifications -->
<script>
    // Toast notification system
    function showToast(message, type = 'success', duration = 5000) {
        const toastContainer = document.getElementById('toast-container');

        // Create toast element
        const toast = document.createElement('div');

        // Set classes based on type
        let bgClass, iconColor, icon;
        if (type === 'success') {
            bgClass = 'bg-green-500 dark:bg-green-600';
            iconColor = 'text-green-500';
            icon = `<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>`;
        } else if (type === 'error') {
            bgClass = 'bg-red-500 dark:bg-red-600';
            iconColor = 'text-red-500';
            icon = `<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>`;
        } else {
            bgClass = 'bg-blue-500 dark:bg-blue-600';
            iconColor = 'text-blue-500';
            icon = `<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>`;
        }

        toast.className = `flex items-center p-4 mb-3 w-full max-w-xs text-white ${bgClass} rounded-lg shadow-md transform transition-all duration-300 translate-x-0 opacity-100`;
        toast.innerHTML = `
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-white ${iconColor}">
                ${icon}
            </div>
            <div class="ml-3 text-sm font-normal">${message}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-white hover:text-gray-100 rounded-lg p-1.5 inline-flex h-8 w-8 focus:outline-none">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        `;

        // Add toast to container
        toastContainer.appendChild(toast);

        // Add event listener for close button
        toast.querySelector('button').addEventListener('click', () => {
            toast.classList.replace('opacity-100', 'opacity-0');
            toast.classList.replace('translate-x-0', 'translate-x-full');
            setTimeout(() => {
                toastContainer.removeChild(toast);
            }, 300);
        });

        // Auto remove toast after duration
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.replace('opacity-100', 'opacity-0');
                toast.classList.replace('translate-x-0', 'translate-x-full');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toastContainer.removeChild(toast);
                    }
                }, 300);
            }
        }, duration);
    }

    // Global notify function for compatibility
    window.notify = function(options) {
        const type = options.type || 'info';
        const message = options.message || '';

        showToast(message, type);
    };

    // Check for URL parameters to show success/error messages
    document.addEventListener('DOMContentLoaded', function() {
        // Show any flash messages from session
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            showToast(urlParams.get('success'), 'success');
        }
        if (urlParams.has('error')) {
            showToast(urlParams.get('error'), 'error');
        }

        // File upload preview
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                const fileName = this.files[0]?.name;
                if (fileName) {
                    const fileNameDisplay = document.createElement('p');
                    fileNameDisplay.classList.add('text-xs', 'text-primary', 'font-medium', 'mt-2');
                    fileNameDisplay.textContent = `Selected: ${fileName}`;

                    // Remove any existing file name display
                    const parent = this.closest('label');
                    const existingFileNameDisplay = parent.querySelector('p.text-primary');
                    if (existingFileNameDisplay) {
                        existingFileNameDisplay.remove();
                    }

                    // Add the new file name display
                    parent.appendChild(fileNameDisplay);
                }
            });
        });
    });
</script>
@endsection
