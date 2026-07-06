@extends('layouts.guest')
@section('title', 'Verify Email')
@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto">
        <!-- Card Header with Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-50 dark:bg-primary-900/30 mb-4">
                <svg class="w-8 h-8 text-primary" viewBox="0 0 24 24" fill="none">
                    <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 12H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 16V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-dark dark:text-white">Verify Your Email</h1>
            <p class="text-dark-400 dark:text-white mt-2">Enter the 6-digit verification code sent to your email</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white dark:bg-dark-50 rounded-xl shadow-sm border border-light-200 dark:border-dark-200/50 overflow-hidden">
            <div class="p-6">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="p-4 mb-5 rounded-lg flex items-center bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-500" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M7.75 12L10.58 14.83L16.25 9.17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 mb-5 rounded-lg flex items-center bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M15 9L9 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M9 9L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 dark:text-red-400">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Email Info -->
                <div class="text-center mb-6 bg-light-50 dark:bg-dark-100/50 rounded-lg p-4">
                    <p class="text-sm text-dark-500 dark:text-white">We've sent a verification code to:</p>
                    <p class="text-base font-medium text-dark dark:text-white mt-1">{{ Auth::user()->email }}</p>
                </div>

                <!-- Verification Form -->
                <form method="POST" action="{{ route('verification.verify-code') }}" class="mt-6">
                    @csrf

                    <!-- Code Input -->
                    <div class="mb-6">
                        <label for="code" class="block text-sm font-medium text-dark-500 dark:text-white mb-2">
                            Verification Code
                        </label>
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-dark-400 dark:text-white" viewBox="0 0 24 24" fill="none">
                                    <path d="M16.4232 9.4478V7.3008C16.4232 4.7878 14.3852 2.7498 11.8722 2.7498C9.35925 2.7388 7.31325 4.7668 7.30225 7.2808V7.3008V9.4478" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M11.9 14.6667V17.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.9 8.25C7.5 8.25 5.75 9.75 5.75 13.5C5.75 17.25 7.5 18.75 11.9 18.75C16.3 18.75 18.05 17.25 18.05 13.5C18.05 9.75 16.3 8.25 11.9 8.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <input type="text" name="code" id="code" autocomplete="one-time-code" 
                                class="w-full pl-10 pr-4 py-3 rounded-lg border border-light-300 dark:border-dark-200 bg-white dark:bg-dark-100 text-dark dark:text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent placeholder-dark-400 dark:placeholder-light-500 tracking-widest text-center font-medium"
                                placeholder="Enter 6-digit code" required inputmode="numeric" pattern="[0-9]*" maxlength="6">
                        </div>
                        
                        @error('code')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full px-5 py-3 text-sm font-medium text-white bg-primary hover:bg-primary-600 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800 rounded-lg transition-colors">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none">
                                <path d="M9 11L12 14L15 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 14V2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 4C19.61 5.46 22 9.42 22 14C22 16.6522 20.9464 19.1957 19.0711 21.0711C17.1957 22.9464 14.6522 24 12 24C9.34784 24 6.8043 22.9464 4.92893 21.0711C3.05357 19.1957 2 16.6522 2 14C2 9.42 4.39 5.46 8 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Verify Email
                        </div>
                    </button>
                </form>

                <!-- Resend Code -->
                <div class="mt-8 pt-6 border-t border-light-200 dark:border-dark-200/50 text-center">
                    <p class="text-sm text-dark-400 dark:text-white mb-3">
                        Didn't receive the code?
                    </p>
                    <form method="POST" action="{{ route('verification.send-code') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-primary-200 dark:border-primary-700 text-sm font-medium rounded-lg text-primary dark:text-primary-400 bg-white dark:bg-dark-100 hover:bg-primary-50 dark:hover:bg-primary-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none">
                                <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.9 11.9999C15.9 14.6099 13.79 16.7199 11.18 16.7199C8.57 16.7199 6.46 14.6099 6.46 11.9999C6.46 9.38994 8.57 7.27991 11.18 7.27991C13.79 7.27991 15.9 9.38994 15.9 11.9999Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17.5 7.63V16.37" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Resend Verification Code
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Text -->
        <div class="text-center mt-6">
            <p class="text-sm text-dark-400 dark:text-white">
                If you're having trouble, please contact 
                <a href="{{ route('support') }}" class="text-primary hover:text-primary-600 dark:hover:text-primary-400">customer support</a>
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh CSRF token every 10 minutes to prevent 419 errors
    setInterval(function() {
        fetch('{{ route('verification.verify-code') }}', {
            method: 'GET',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newToken = doc.querySelector('input[name="_token"]');
            const currentToken = document.querySelector('input[name="_token"]');
            if (newToken && currentToken) {
                currentToken.value = newToken.value;
                console.log('CSRF token refreshed');
            }
        })
        .catch(error => console.error('Failed to refresh CSRF token:', error));
    }, 600000); // 10 minutes
</script>
@endsection 