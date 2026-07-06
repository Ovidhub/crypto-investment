@extends('layouts.guest')

@section('title', 'Reset your password')

@section('content')
<!-- Card Container -->
<div class="dark:bg-dark-50 bg-white rounded-xl shadow-lg overflow-hidden border dark:border-dark-200/30 border-light-300/30">
    <!-- Card Header -->
    <div class="dark:bg-dark-100/50 bg-light-100/50 p-6 border-b dark:border-dark-200/50 border-light-200/50">
        <h2 class="text-xl font-bold dark:text-white text-dark">Create New Password</h2>
        <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Set a new password for your account</p>
    </div>
    
    <!-- Card Body -->
    <div class="p-6 md:p-8">
        <!-- Alert Messages -->
        @if(Session::has('message'))
        <div class="mb-6 dark:bg-danger/10 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle h-5 w-5 text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm dark:text-red-400 text-red-700">{{ Session::get('message') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if (session('status'))
        <div class="mb-6 dark:bg-secondary/10 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle h-5 w-5 text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm dark:text-green-400 text-green-700">{{ session('status') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Reset Form -->
        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            
            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    Email Address
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required
                           class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                           placeholder="you@example.com">
                </div>
                @if ($errors->has('email'))
                <span class="text-sm text-red-600 dark:text-red-400 mt-1 block">
                    {{ $errors->first('email') }}
                </span>
                @endif
            </div>
            
            <!-- Password Input -->
            <div>
                <label for="password" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    New Password
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                           class="block w-full pl-10 pr-10 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                           placeholder="••••••••">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button type="button" id="togglePassword" class="dark:text-gray-400 text-gray-500 hover:text-primary dark:hover:text-primary focus:outline-none transition-colors">
                            <i class="fas fa-eye h-5 w-5" id="eyeIcon"></i>
                            <i class="fas fa-eye-slash h-5 w-5 hidden" id="eyeOffIcon"></i>
                        </button>
                    </div>
                </div>
                @if ($errors->has('password'))
                <span class="text-sm text-red-600 dark:text-red-400 mt-1 block">
                    {{ $errors->first('password') }}
                </span>
                @endif
            </div>
            
            <!-- Password Confirmation Input -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    Confirm New Password
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="block w-full pl-10 pr-10 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                           placeholder="••••••••">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button type="button" id="toggleConfirmPassword" class="dark:text-gray-400 text-gray-500 hover:text-primary dark:hover:text-primary focus:outline-none transition-colors">
                            <i class="fas fa-eye h-5 w-5" id="confirmEyeIcon"></i>
                            <i class="fas fa-eye-slash h-5 w-5 hidden" id="confirmEyeOffIcon"></i>
                        </button>
                    </div>
                </div>
                @if ($errors->has('password_confirmation'))
                <span class="text-sm text-red-600 dark:text-red-400 mt-1 block">
                    {{ $errors->first('password_confirmation') }}
                </span>
                @endif
            </div>
            
            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-md text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-key h-5 w-5 mr-2"></i>
                    <span class="font-medium">Reset Password</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Security Notice -->
<div class="mt-8 text-center">
    <div class="inline-flex items-center text-xs dark:text-gray-500 text-gray-500">
        <i class="fas fa-shield-alt h-3 w-3 mr-1"></i>
        <span>Secure password reset - Your data is protected</span>
    </div>
    <p class="mt-2 text-xs dark:text-gray-500 text-gray-500">
        &copy; Copyright {{date('Y')}} &nbsp; {{$settings->site_name}} &nbsp; All Rights Reserved.
    </p>
</div>
@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                // Toggle password visibility
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle eye icon
                eyeIcon.classList.toggle('hidden');
                eyeOffIcon.classList.toggle('hidden');
            });
        }
        
        // Confirm Password visibility toggle
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPassword = document.getElementById('password_confirmation');
        const confirmEyeIcon = document.getElementById('confirmEyeIcon');
        const confirmEyeOffIcon = document.getElementById('confirmEyeOffIcon');
        
        if (toggleConfirmPassword && confirmPassword) {
            toggleConfirmPassword.addEventListener('click', function() {
                // Toggle password visibility
                const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPassword.setAttribute('type', type);
                
                // Toggle eye icon
                confirmEyeIcon.classList.toggle('hidden');
                confirmEyeOffIcon.classList.toggle('hidden');
            });
        }
    });
    
    // Auto-refresh CSRF token every 10 minutes to prevent 419 errors
    setInterval(function() {
        fetch('{{ route('password.reset', ['token' => $request->route('token')]) }}', {
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