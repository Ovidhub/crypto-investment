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
        <span class="text-dark dark:text-white">Account Verification</span>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-5">
        <div class="flex items-center mb-4 sm:mb-0">
            <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M9 11V8H15V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 16V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-dark dark:text-white">Account Verification</h1>
        </div>
    </div>
    
    <!-- Alert Messages -->
    <x-danger-alert/>
    <x-success-alert/>
    
    <!-- Main Content -->
    <div class="max-w-4xl mx-auto">
        <!-- KYC Verification Card -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden mb-6">
            <div class="p-6 text-center border-b border-light-200 dark:border-dark-200/50">
                <h2 class="text-lg font-bold text-dark dark:text-white mb-2">KYC Verification</h2>
                <p class="text-dark-500 dark:text-light-400">To comply with regulations, each participant will have to go through identity verification (KYC/AML) to prevent fraud.</p>
            </div>
            
            <div class="p-8 flex flex-col items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-light-100 dark:bg-dark-200/80 flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-primary" viewBox="0 0 24 24" fill="none">
                        <path d="M20.5 10.19H17.61C15.24 10.19 13.31 8.26 13.31 5.89V3C13.31 2.45 12.86 2 12.31 2H8.07C4.99 2 2.5 4 2.5 7.57V16.43C2.5 20 4.99 22 8.07 22H15.93C19.01 22 21.5 20 21.5 16.43V11.19C21.5 10.64 21.05 10.19 20.5 10.19Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.31 2V5.89C13.31 8.26 15.24 10.19 17.61 10.19H20.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.8101 13.8201C14.2601 15.3701 11.7301 15.3701 10.1801 13.8201" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                
                <p class="text-dark-600 dark:text-light-300 text-center mb-6 max-w-md">You have not submitted your necessary documents to verify your identity. In order to enjoy our investment system, please verify your identity.</p>
                
                <div class="mb-8">
                    @if (Auth::user()->account_verify == 'Verified' or Auth::user()->account_verify == 'Under review')
                        <button class="px-6 py-3 rounded-lg bg-primary/50 text-white cursor-not-allowed opacity-70" disabled>
                            Complete Your KYC
                        </button>
                        <p class="mt-3 text-sm text-green-500 dark:text-green-400">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Your application is under review, please wait
                        </p>
                    @else
                        <a href="{{ route('kycform') }}" class="px-6 py-3 rounded-lg bg-primary text-white hover:bg-primary-600 transition-colors inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none">
                                <path d="M11 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22H15C20 22 22 20 22 15V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16.04 3.02001L8.16 10.9C7.86 11.2 7.56 11.79 7.5 12.22L7.07 15.23C6.91 16.32 7.68 17.08 8.77 16.93L11.78 16.5C12.2 16.44 12.79 16.14 13.1 15.84L20.98 7.96001C22.34 6.60001 22.98 5.02001 20.98 3.02001C18.98 1.02001 17.4 1.66001 16.04 3.02001Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.91 4.1499C15.58 6.5399 17.45 8.4099 19.85 9.0899" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Complete Your KYC
                        </a>
                    @endif
                </div>
                
                <!-- Verification Status -->
                <div class="w-full max-w-md bg-light-50 dark:bg-dark-200/50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="relative flex items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center z-10">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 11V8H15V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 16V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="absolute top-0 left-0 w-full h-full rounded-full border-2 border-primary animate-ping opacity-20"></div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-dark dark:text-white">KYC Status</h4>
                            @if (Auth::user()->account_verify == 'Verified')
                                <p class="text-green-500">Verified ✓</p>
                            @elseif (Auth::user()->account_verify == 'Under review')
                                <p class="text-amber-500">Under Review</p>
                            @else
                                <p class="text-red-500">Not Verified</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Support Card -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
    <div class="p-6 flex flex-col md:flex-row md:items-center">
        <div class="flex-shrink-0 flex items-center justify-center mb-4 md:mb-0 md:mr-6">
            <div class="w-16 h-16 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center">
                <svg class="w-8 h-8 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M17 20.5H7C4 20.5 2 19 2 15.5V8.5C2 5 4 3.5 7 3.5H17C20 3.5 22 5 22 8.5V15.5C22 19 20 20.5 17 20.5Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 9L13.87 11.5C12.84 12.32 11.15 12.32 10.12 11.5L7 9" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="flex-grow md:mr-6 text-center md:text-left">
            <h3 class="text-lg font-bold text-dark dark:text-white mb-2">We're here to help you!</h3>
            <p class="text-dark-500 dark:text-light-400 mb-4 md:mb-0">Ask a question, manage request, report an issue. Our support team will get back to you by email.</p>
        </div>
        <div class="flex-shrink-0 flex justify-center md:justify-start">
            <a href="{{ route('support') }}" class="px-5 py-2.5 rounded-lg bg-primary text-white hover:bg-primary-600 transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none">
                    <path d="M8.5 19H8C4 19 2 18 2 13V8C2 4 4 2 8 2H16C20 2 22 4 22 8V13C22 17 20 19 16 19H15.5C15.19 19 14.89 19.15 14.7 19.4L13.2 21.4C12.54 22.28 11.46 22.28 10.8 21.4L9.3 19.4C9.14 19.18 8.77 19 8.5 19Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.9965 11H16.0054" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M11.9955 11H12.0045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7.99451 11H8.00349" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Get Support Now
            </a>
        </div>
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
    });
</script>
@endsection
