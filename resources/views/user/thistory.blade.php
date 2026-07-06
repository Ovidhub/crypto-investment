@extends('layouts.dash')
@section('title', $title)
@section('content')

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>

<!-- ROI History page header with title and icon -->
<div class="mb-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-dark dark:text-white flex items-center">
            <svg class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.75 2.44995C11.44 1.85995 12.57 1.85995 13.27 2.44995L14.85 3.81005C15.15 4.07005 15.71 4.28005 16.11 4.28005H17.81C18.87 4.28005 19.74 5.14996 19.74 6.20996V7.91003C19.74 8.30003 19.95 8.87003 20.21 9.17003L21.57 10.75C22.16 11.44 22.16 12.57 21.57 13.27L20.21 14.85C19.95 15.15 19.74 15.71 19.74 16.11V17.8101C19.74 18.8701 18.87 19.7401 17.81 19.7401H16.11C15.72 19.7401 15.15 19.95 14.85 20.21L13.27 21.5701C12.58 22.1601 11.45 22.1601 10.75 21.5701L9.17003 20.21C8.87003 19.95 8.31001 19.7401 7.91001 19.7401H6.18994C5.12994 19.7401 4.25998 18.8701 4.25998 17.8101V16.11C4.25998 15.72 4.04998 15.15 3.78998 14.85L2.42998 13.26C1.83998 12.57 1.83998 11.45 2.42998 10.75L3.78998 9.17003C4.04998 8.87003 4.25998 8.31003 4.25998 7.92003V6.20996C4.25998 5.14996 5.12994 4.28005 6.18994 4.28005H7.91001C8.30001 4.28005 8.87003 4.07005 9.17003 3.81005L10.75 2.44995Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8.5 12H15.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 15L17 12L14 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Your ROI History</span>
        </h1>
    </div>
</div>

<!-- Alert Messages -->
<x-danger-alert/>
<x-success-alert/>

<!-- ROI History Table Card -->
<div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
    <div class="p-4 border-b border-light-200 dark:border-dark-200/50">
        <h2 class="text-base font-bold text-dark dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary" viewBox="0 0 24 24" fill="none">
                <path d="M3.16992 7.43994L11.9999 12.5499L20.7699 7.46994" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 21.61V12.54" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2.39014 9.17006V14.8201C2.39014 16.0001 3.14014 17.0401 4.19014 17.5601L10.7401 21.0701C11.7101 21.5401 12.8501 21.5401 13.8201 21.0701L20.3701 17.5601C21.4201 17.0401 22.1701 16.0001 22.1701 14.8201V9.17006C22.1701 7.99006 21.4201 6.95006 20.3701 6.43006L13.8201 2.92006C12.8501 2.45006 11.7101 2.45006 10.7401 2.92006L4.19014 6.43006C3.14014 6.95006 2.39014 7.99006 2.39014 9.17006Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.9998 13.2401V9.58014L7.50977 4.1001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Return on Investment History</span>
        </h2>
    </div>
    
    <!-- Table Container -->
    <div class="p-5">
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-light-200 dark:divide-dark-200/50">
                <thead>
                    <tr class="bg-light-50 dark:bg-dark-200/50">
                        <th class="px-4 py-3.5 text-left text-xs font-medium text-dark-500 dark:text-light-400 uppercase tracking-wider">Plan</th>
                        <th class="px-4 py-3.5 text-left text-xs font-medium text-dark-500 dark:text-light-400 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3.5 text-left text-xs font-medium text-dark-500 dark:text-light-400 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3.5 text-left text-xs font-medium text-dark-500 dark:text-light-400 uppercase tracking-wider">Date Created</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-dark-50 divide-y divide-light-200 dark:divide-dark-200/30">
                    @foreach ($t_history as $history)
                    <tr class="hover:bg-light-50 dark:hover:bg-dark-200/30 transition-colors">
                        <td class="px-4 py-4 text-sm text-dark dark:text-white">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary" viewBox="0 0 24 24" fill="none">
                                    <path d="M10.75 2.44995C11.44 1.85995 12.57 1.85995 13.27 2.44995L14.85 3.81005C15.15 4.07005 15.71 4.28005 16.11 4.28005H17.81C18.87 4.28005 19.74 5.14996 19.74 6.20996V7.91003C19.74 8.30003 19.95 8.87003 20.21 9.17003L21.57 10.75C22.16 11.44 22.16 12.57 21.57 13.27L20.21 14.85C19.95 15.15 19.74 15.71 19.74 16.11V17.8101C19.74 18.8701 18.87 19.7401 17.81 19.7401H16.11C15.72 19.7401 15.15 19.95 14.85 20.21L13.27 21.5701C12.58 22.1601 11.45 22.1601 10.75 21.5701L9.17003 20.21C8.87003 19.95 8.31001 19.7401 7.91001 19.7401H6.18994C5.12994 19.7401 4.25998 18.8701 4.25998 17.8101V16.11C4.25998 15.72 4.04998 15.15 3.78998 14.85L2.42998 13.26C1.83998 12.57 1.83998 11.45 2.42998 10.75L3.78998 9.17003C4.04998 8.87003 4.25998 8.31003 4.25998 7.92003V6.20996C4.25998 5.14996 5.12994 4.28005 6.18994 4.28005H7.91001C8.30001 4.28005 8.87003 4.07005 9.17003 3.81005L10.75 2.44995Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>{{ $history->plan }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-dark dark:text-white">
                            <span class="font-medium">{{ $settings->currency }}{{ number_format($history->amount, 2, '.', ',') }}</span>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $history->type == 'ROI' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' }}">
                                {{ $history->type }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-dark-400 dark:text-light-500">
                            {{ \Carbon\Carbon::parse($history->created_at)->toDayDateTimeString() }}
                        </td>
                    </tr>
                    @endforeach
                    
                    @if(count($t_history) == 0)
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-sm text-center text-dark-400 dark:text-light-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 mb-3 text-dark-300 dark:text-light-600" viewBox="0 0 24 24" fill="none">
                                    <path d="M10.75 2.44995C11.44 1.85995 12.57 1.85995 13.27 2.44995L14.85 3.81005C15.15 4.07005 15.71 4.28005 16.11 4.28005H17.81C18.87 4.28005 19.74 5.14996 19.74 6.20996V7.91003C19.74 8.30003 19.95 8.87003 20.21 9.17003L21.57 10.75C22.16 11.44 22.16 12.57 21.57 13.27L20.21 14.85C19.95 15.15 19.74 15.71 19.74 16.11V17.8101C19.74 18.8701 18.87 19.7401 17.81 19.7401H16.11C15.72 19.7401 15.15 19.95 14.85 20.21L13.27 21.5701C12.58 22.1601 11.45 22.1601 10.75 21.5701L9.17003 20.21C8.87003 19.95 8.31001 19.7401 7.91001 19.7401H6.18994C5.12994 19.7401 4.25998 18.8701 4.25998 17.8101V16.11C4.25998 15.72 4.04998 15.15 3.78998 14.85L2.42998 13.26C1.83998 12.57 1.83998 11.45 2.42998 10.75L3.78998 9.17003C4.04998 8.87003 4.25998 8.31003 4.25998 7.92003V6.20996C4.25998 5.14996 5.12994 4.28005 6.18994 4.28005H7.91001C8.30001 4.28005 8.87003 4.07005 9.17003 3.81005L10.75 2.44995Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 12H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p>No ROI history found</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $t_history->onEachSide(1)->links('components.transaction-pagination') }}
        </div>
    </div>
</div>

<!-- ROI Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <!-- Total ROI Card -->
   
    <!-- Last ROI Card -->
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-green-500 dark:text-green-400" viewBox="0 0 24 24" fill="none">
                    <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.46997 10.64L12 14.16L15.53 10.64" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-dark-300 dark:text-light-300 uppercase font-medium">Last Return</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">
                    @if($t_history->count() > 0)
                        {{ $settings->currency }}{{ number_format($t_history->first()->amount, 2, '.', ',') }}
                    @else
                        {{ $settings->currency }}0.00
                    @endif
                </p>
                <p class="text-xs text-dark-400 dark:text-light-500 mt-1">
                    @if($t_history->count() > 0)
                        {{ \Carbon\Carbon::parse($t_history->first()->created_at)->diffForHumans() }}
                    @else
                        No returns yet
                    @endif
                </p>
            </div>
        </div>
    </div>
    
    <!-- ROI Count Card -->
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="none">
                    <path d="M3.16992 7.43994L11.9999 12.5499L20.7699 7.46994" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 21.61V12.54" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9.92988 2.48004L4.58988 5.44004C3.37988 6.11004 2.38989 7.79004 2.38989 9.17004V14.82C2.38989 16.2 3.37988 17.88 4.58988 18.55L9.92988 21.52C11.0699 22.15 12.9399 22.15 14.0799 21.52L19.4199 18.55C20.6299 17.88 21.6199 16.2 21.6199 14.82V9.17004C21.6199 7.79004 20.6299 6.11004 19.4199 5.44004L14.0799 2.47004C12.9299 1.84004 11.0699 1.84004 9.92988 2.48004Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-dark-300 dark:text-light-300 uppercase font-medium">Total Transactions</p>
                <p class="text-xl font-bold text-dark dark:text-white mt-1">
                    {{ $t_history->count() }}
                </p>
                <p class="text-xs text-dark-400 dark:text-light-500 mt-1">
                    ROI payments received
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modern Toast Notification System -->
<script>
    function showToast(type, title, message) {
        const toastContainer = document.getElementById('toast-container');
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
        
        // Allow click to dismiss
        toast.addEventListener('click', function() {
            this.style.animation = 'fadeOut 0.3s forwards';
            setTimeout(() => {
                this.remove();
            }, 300);
        });
    }

    // Make notify function available globally
    window.notify = function(options, settings) {
        const type = (settings && settings.type) || 'info';
        const title = (options && options.title) || '';
        const message = (options && options.message) || '';
        showToast(type, title, message);
    };
</script>

<!-- Toast Notification Styles -->
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
        z-index: 10000;
    }
    
    .toast.success {
        background-color: #10b981;
        color: white;
    }
    
    .toast.danger {
        background-color: #ef4444;
        color: white;
    }
    
    .toast.dark-success {
        background-color: #065f46;
        color: white;
    }
    
    .toast.dark-danger {
        background-color: #b91c1c;
        color: white;
    }
    
    .toast .icon {
        margin-right: 12px;
        font-size: 1.5rem;
    }
    
    .toast .content {
        flex: 1;
    }
    
    .toast .title {
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .toast .message {
        font-size: 0.75rem;
        opacity: 0.9;
    }
    
    .toast .close {
        cursor: pointer;
        font-size: 0.75rem;
        padding: 4px;
    }
    
    .toast .progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
    }
    
    .toast .progress-bar {
        height: 100%;
        background-color: rgba(255, 255, 255, 0.3);
        width: 100%;
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
