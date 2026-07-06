@extends('layouts.guest')

@section('title', 'Account Blocked')

@section('content')
<!-- Card Container -->
<div class="dark:bg-dark-50 bg-white rounded-xl shadow-lg overflow-hidden border dark:border-dark-200/30 border-light-300/30">
    <!-- Card Header -->
    <div class="dark:bg-red-900/20 bg-red-50 p-6 border-b dark:border-red-800/50 border-red-200/50">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30">
                    <i data-lucide="shield-alert" class="h-6 w-6 text-red-600 dark:text-red-400"></i>
                </div>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-bold dark:text-white text-dark">Account Blocked</h2>
                <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Your account access has been restricted</p>
            </div>
        </div>
    </div>
    
    <!-- Card Body -->
    <div class="p-6 md:p-8">
        <!-- Block Reason -->
        <div class="mb-6 dark:bg-red-900/10 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium dark:text-red-400 text-red-800 mb-1">Reason for Blocking</h3>
                    <p class="text-sm dark:text-red-300 text-red-700">
                        @if(session('user_block_reason'))
                            {{ session('user_block_reason') }}
                        @else
                            Your account has been blocked due to a violation of our terms of service or suspicious activity. Please contact support for more information.
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <!-- What This Means -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold dark:text-white text-dark mb-3">What this means:</h3>
            <ul class="space-y-2 text-sm dark:text-gray-400 text-gray-600">
                <li class="flex items-start">
                    <i data-lucide="x-circle" class="h-5 w-5 text-red-500 mr-2 flex-shrink-0 mt-0.5"></i>
                    <span>You cannot access your account dashboard</span>
                </li>
                <li class="flex items-start">
                    <i data-lucide="x-circle" class="h-5 w-5 text-red-500 mr-2 flex-shrink-0 mt-0.5"></i>
                    <span>You cannot perform any trading or transactions</span>
                </li>
                <li class="flex items-start">
                    <i data-lucide="x-circle" class="h-5 w-5 text-red-500 mr-2 flex-shrink-0 mt-0.5"></i>
                    <span>All active operations have been suspended</span>
                </li>
            </ul>
        </div>
        
        <!-- Support Contact Card -->
        <div class="dark:bg-primary-900/10 bg-primary-50 border dark:border-primary-800/30 border-primary-200 rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30">
                        <i data-lucide="headphones" class="h-5 w-5 text-primary-600 dark:text-primary-400"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold dark:text-white text-dark">Need Help?</h3>
                    <p class="text-xs dark:text-gray-400 text-gray-600">Contact our support team</p>
                </div>
            </div>
            
            <p class="text-sm dark:text-gray-300 text-gray-700 mb-4">
                If you believe this is a mistake or would like to appeal this decision, please contact our support team:
            </p>
            
            <div class="space-y-3">
                @if($settings->contact_email)
                <a href="mailto:{{ $settings->contact_email }}" class="flex items-center p-3 dark:bg-dark-100 bg-white rounded-lg hover:shadow-md transition-all group">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 group-hover:bg-primary-200 dark:group-hover:bg-primary-800/50 transition-colors">
                            <i data-lucide="mail" class="h-5 w-5 text-primary-600 dark:text-primary-400"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-xs dark:text-gray-400 text-gray-500">Email Support</p>
                        <p class="text-sm font-medium dark:text-white text-dark">{{ $settings->contact_email }}</p>
                    </div>
                    <i data-lucide="external-link" class="h-4 w-4 dark:text-gray-400 text-gray-500"></i>
                </a>
                @endif
                
                @if($settings->whatsapp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp) }}" target="_blank" class="flex items-center p-3 dark:bg-dark-100 bg-white rounded-lg hover:shadow-md transition-all group">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                            <i data-lucide="message-circle" class="h-5 w-5 text-green-600 dark:text-green-400"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-xs dark:text-gray-400 text-gray-500">WhatsApp Support</p>
                        <p class="text-sm font-medium dark:text-white text-dark">{{ $settings->whatsapp }}</p>
                    </div>
                    <i data-lucide="external-link" class="h-4 w-4 dark:text-gray-400 text-gray-500"></i>
                </a>
                @endif
                
                @if($settings->site_address)
                <div class="flex items-center p-3 dark:bg-dark-100 bg-white rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30">
                            <i data-lucide="globe" class="h-5 w-5 text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-xs dark:text-gray-400 text-gray-500">Website</p>
                        <p class="text-sm font-medium dark:text-white text-dark">{{ $settings->site_name }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Back to Login Button -->
        <div class="mt-6">
            <a href="{{ route('login') }}" class="w-full flex justify-center items-center px-4 py-3 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm text-sm font-medium dark:text-white text-dark dark:bg-dark-100 bg-white hover:bg-light-100 dark:hover:bg-dark-200 transition-colors">
                <i data-lucide="arrow-left" class="h-5 w-5 mr-2"></i>
                <span>Back to Login</span>
            </a>
        </div>
    </div>
</div>

<!-- Security Notice -->
<div class="mt-8 text-center">
    <div class="inline-flex items-center text-xs dark:text-gray-500 text-gray-500">
        <i data-lucide="info" class="h-3 w-3 mr-1"></i>
        <span>Blocked on {{ now()->format('M d, Y') }}</span>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
