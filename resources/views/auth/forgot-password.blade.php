@extends('layouts.guest')

@section('title', 'Forgot your password')

@section('styles')
@parent

@endsection

@section('content')
<!-- Card Container -->
<div class="dark:bg-dark-50 bg-white rounded-xl shadow-lg overflow-hidden border dark:border-dark-200/30 border-light-300/30">
    <!-- Card Header -->
    <div class="dark:bg-dark-100/50 bg-light-100/50 p-6 border-b dark:border-dark-200/50 border-light-200/50">
        <h2 class="text-xl font-bold dark:text-white text-dark">Forgot Password</h2>
        <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Enter your email to reset your password</p>
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
        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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
                @if ($errors->has('email'))
                <span class="text-sm text-red-600 dark:text-red-400 mt-1 block">
                    {{ $errors->first('email') }}
                </span>
                @endif
            </div>
            
            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-md text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-paper-plane h-5 w-5 mr-2"></i>
                    <span class="font-medium">Email Password Reset Link</span>
                </button>
            </div>
            
            <!-- Back to Login Link -->
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm font-medium text-primary hover:text-primary-600 transition-colors">
                    <i class="fas fa-arrow-left h-3 w-3 mr-1"></i> Back to login
                </a>
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
    // Auto-refresh CSRF token every 10 minutes to prevent 419 errors
    setInterval(function() {
        fetch('{{ route('password.request') }}', {
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