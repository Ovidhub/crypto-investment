@extends('layouts.guest')
@section('title', 'Manager Login')

@section('content')
<!-- Card Container -->
<div class="dark:bg-dark-50 bg-white rounded-xl shadow-lg overflow-hidden border dark:border-dark-200/30 border-light-300/30">
    <!-- Card Header -->
    <div class="dark:bg-dark-100/50 bg-light-100/50 p-6 border-b dark:border-dark-200/50 border-light-200/50">
        <div class="text-center mb-3">
            <a href="/"><img src="{{ asset('storage/app/public/'.$settings->logo)}}" alt="Logo" class="h-10 mx-auto"></a>
        </div>
        <h2 class="text-xl font-bold dark:text-white text-dark text-center">Manager Login</h2>
        <p class="mt-1 text-sm dark:text-gray-400 text-gray-600 text-center">Sign in to your admin account</p>
    </div>
    
    <!-- Card Body -->
    <div class="p-6 md:p-8">
        <!-- Alert Messages -->
        <x-danger-alert/>
        <x-success-alert/>
        
        <!-- Login Form -->
        <form method="POST" action="{{ route('adminlogin') }}" class="space-y-6">
            @csrf
            
            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    Email Address
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                           placeholder="you@example.com">
                </div>
                @error('email')
                <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                @enderror
            </div>
            
            <!-- Password Input -->
            <div>
                <label for="password" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    Password
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
            </div>
            
            <!-- Forgot Password Link -->
            <div class="flex justify-end">
                <a href="{{ route('admin.forgetpassword') }}" class="text-sm font-medium text-primary hover:text-primary-600 transition-colors">
                    Forgot password?
                </a>
            </div>
            
            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-md text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-sign-in-alt h-5 w-5 mr-2"></i>
                    <span class="font-medium">Sign In</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Security Notice -->
<div class="mt-8 text-center">
    <div class="inline-flex items-center text-xs dark:text-gray-500 text-gray-500">
        <i class="fas fa-shield-alt h-3 w-3 mr-1"></i>
        <span>Secure login - Your data is protected</span>
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
        
        // Auto-refresh CSRF token every 10 minutes to prevent 419 errors
        setInterval(function() {
            fetch('{{ route('adminloginform') }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
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
        }, 600000); // Refresh every 10 minutes (600000ms)
    });
</script>
@endsection