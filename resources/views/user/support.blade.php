@extends('layouts.dash')
@section('title', $title)
@section('content')

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>

<!-- Support page header with title and icon -->
<div class="mb-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-dark dark:text-white flex items-center">
            <svg class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.1505 10.4901C21.1505 14.5901 17.9205 18.0001 12.0005 21.0001C6.08047 18.0001 2.85046 14.5901 2.85046 10.4901C2.85046 6.39015 6.08047 2.9801 12.0005 5.9801C17.9205 2.9801 21.1505 6.39015 21.1505 10.4901Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 12V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12.0098 8H11.9998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>24/7 Customer Support</span>
        </h1>
    </div>
</div>

<!-- Alert Messages -->
<x-danger-alert/>
<x-success-alert/>

<!-- Support Form Card -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Support Info -->
    <div class="md:col-span-1">
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
            <div class="p-4 border-b border-light-200 dark:border-dark-200/50">
                <h2 class="text-base font-bold text-dark dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" viewBox="0 0 24 24" fill="none">
                        <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
                        <path d="M12 16V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Contact Information</span>
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-6">
                    <!-- Email Contact -->
                    <div>
                        <h3 class="text-dark-400 dark:text-light-400 text-sm font-medium mb-2">Direct Email</h3>
                        <a href="mailto:{{$settings->contact_email}}" class="flex items-center p-4 rounded-lg bg-light-50 dark:bg-dark-200 border border-light-200 dark:border-dark-300/50 text-primary hover:bg-light-100 dark:hover:bg-dark-300/80 transition-colors">
                            <svg class="w-5 h-5 mr-3 text-primary-600 dark:text-primary-400" viewBox="0 0 24 24" fill="none">
                                <path d="M17 20.5H7C4 20.5 2 19 2 15.5V8.5C2 5 4 3.5 7 3.5H17C20 3.5 22 5 22 8.5V15.5C22 19 20 20.5 17 20.5Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17 9L13.87 11.5C12.84 12.32 11.15 12.32 10.12 11.5L7 9" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="font-medium">{{$settings->contact_email}}</span>
                        </a>
                    </div>
                    
                    <!-- Support Hours -->
                    <div class="p-4 rounded-lg bg-light-50 dark:bg-dark-200 border border-light-200 dark:border-dark-300/50">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-3 mt-0.5 text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none">
                                <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.71 15.18L12.61 13.33C12.07 13.01 11.63 12.24 11.63 11.61V7.51" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-dark dark:text-white mb-1">Support Hours</h3>
                                <p class="text-xs text-dark-400 dark:text-light-400">Our team is available 24/7 to assist you with any inquiries or issues you may have.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Response Time -->
                    <div class="p-4 rounded-lg bg-light-50 dark:bg-dark-200 border border-light-200 dark:border-dark-300/50">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-3 mt-0.5 text-green-500 dark:text-green-400" viewBox="0 0 24 24" fill="none">
                                <path d="M15 10L11 14L9 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-dark dark:text-white mb-1">Response Time</h3>
                                <p class="text-xs text-dark-400 dark:text-light-400">We typically respond to all inquiries within 24 hours during business days.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Support Form -->
    <div class="md:col-span-2">
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
            <div class="p-4 border-b border-light-200 dark:border-dark-200/50">
                <h2 class="text-base font-bold text-dark dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" viewBox="0 0 24 24" fill="none">
                        <path d="M8.5 19H8C4 19 2 18 2 13V8C2 4 4 2 8 2H16C20 2 22 4 22 8V13C22 17 20 19 16 19H15.5C15.19 19 14.89 19.15 14.7 19.4L13.2 21.4C12.54 22.28 11.46 22.28 10.8 21.4L9.3 19.4C9.14 19.18 8.77 19 8.5 19Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 8H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 13H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Send us a Message</span>
                </h2>
            </div>
            <div class="p-5">
                <form method="post" action="{{route('enquiry')}}" class="space-y-4">
                    <input type="hidden" name="name" value="{{Auth::user()->name}}" />
                    <input type="hidden" name="email" value="{{Auth::user()->email}}">
                    
                    <!-- User Info Summary -->
                    <div class="flex items-center p-4 rounded-lg bg-light-50 dark:bg-dark-200 border border-light-200 dark:border-dark-300/50">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none">
                                <path d="M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.51C8.71997 7.7 10.18 6.23 12 6.23C13.81 6.23 15.28 7.7 15.28 9.51C15.27 11.28 13.88 12.72 12.12 12.78Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.74 19.38C16.96 21.01 14.6 22 12 22C9.40001 22 7.04001 21.01 5.26001 19.38C5.36001 18.44 5.96001 17.52 7.03001 16.8C9.77001 14.98 14.25 14.98 16.97 16.8C18.04 17.52 18.64 18.44 18.74 19.38Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-dark dark:text-white font-medium">{{Auth::user()->name}}</p>
                            <p class="text-xs text-dark-400 dark:text-light-400">{{Auth::user()->email}}</p>
                        </div>
                    </div>
                    
                    <!-- Message Field -->
                    <div class="space-y-2">
                        <label for="message" class="text-sm font-medium text-dark dark:text-white flex items-center">
                            Your Message
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <textarea 
                            name="message" 
                            id="message" 
                            rows="6" 
                            class="block w-full px-4 py-3 text-sm rounded-lg bg-light-100 dark:bg-dark-100 border border-light-200 dark:border-dark-200 focus:ring-2 focus:ring-primary focus:border-transparent text-dark dark:text-white transition-all"
                            placeholder="How can we help you today?"
                            required
                        ></textarea>
                    </div>
                    
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 px-4 rounded-lg bg-primary hover:bg-primary-600 text-white font-medium flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path d="M7.39999 6.32003L15.89 3.49003C19.7 2.22003 21.77 4.30003 20.51 8.11003L17.68 16.6C15.78 22.31 12.66 22.31 10.76 16.6L9.91999 14.08L7.39999 13.24C1.68999 11.34 1.68999 8.23003 7.39999 6.32003Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10.11 13.6501L13.69 10.0601" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="mt-6">
    <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
        <div class="p-4 border-b border-light-200 dark:border-dark-200/50">
            <h2 class="text-base font-bold text-dark dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M17 18.43H13L8.54999 21.39C7.88999 21.83 7 21.36 7 20.56V18.43C4 18.43 2 16.43 2 13.43V7.42993C2 4.42993 4 2.42993 7 2.42993H17C20 2.42993 22 4.42993 22 7.42993V13.43C22 16.43 20 18.43 17 18.43Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 11.36V11.15C12 10.47 12.42 10.11 12.84 9.82001C13.25 9.54001 13.66 9.18002 13.66 8.52002C13.66 7.60002 12.92 6.85999 12 6.85999C11.08 6.85999 10.34 7.60002 10.34 8.52002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M11.9955 13.75H12.0045" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Frequently Asked Questions</span>
            </h2>
        </div>
        <div class="p-5">
            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="border border-light-200 dark:border-dark-300/50 rounded-lg overflow-hidden">
                    <div class="p-4 bg-light-50 dark:bg-dark-200/50">
                        <h3 class="font-medium text-dark dark:text-white">How long does it take to process a withdrawal?</h3>
                    </div>
                    <div class="p-4 bg-white dark:bg-dark-50">
                        <p class="text-sm text-dark-400 dark:text-light-400">Most withdrawals are processed within 24-48 hours. However, processing times may vary depending on the payment method and amount.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="border border-light-200 dark:border-dark-300/50 rounded-lg overflow-hidden">
                    <div class="p-4 bg-light-50 dark:bg-dark-200/50">
                        <h3 class="font-medium text-dark dark:text-white">What are the fees for crypto swapping?</h3>
                    </div>
                    <div class="p-4 bg-white dark:bg-dark-50">
                        <p class="text-sm text-dark-400 dark:text-light-400">Our platform charges a fee of {{ $moresettings->fee }}% for all crypto swapping operations. This fee is automatically calculated and displayed during the swap process.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="border border-light-200 dark:border-dark-300/50 rounded-lg overflow-hidden">
                    <div class="p-4 bg-light-50 dark:bg-dark-200/50">
                        <h3 class="font-medium text-dark dark:text-white">How can I secure my account?</h3>
                    </div>
                    <div class="p-4 bg-white dark:bg-dark-50">
                        <p class="text-sm text-dark-400 dark:text-light-400">We recommend enabling two-factor authentication (2FA), using a strong unique password, and regularly monitoring your account for any suspicious activity.</p>
                    </div>
                </div>
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
