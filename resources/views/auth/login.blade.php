@extends('layouts.guest')

@section('title', 'Account Login')

@section('content')
<!-- Card Container -->
<div class="rounded-2xl border border-dark-200 bg-dark-50/80 glass shadow-2xl overflow-hidden">
    <!-- Card Header -->
    <div class="p-6 border-b border-dark-200">
        <h2 class="font-display text-2xl font-bold text-white">Welcome Back</h2>
        <p class="mt-1 text-sm text-[#9aa3b2]">Sign in to your account to continue</p>
    </div>

    <!-- Card Body -->
    <div class="p-6 md:p-8">
        <!-- Alert Messages -->
        @if (Session::has('status'))
        <div class="mb-6 bg-red-500/10 border-l-4 border-red-500 p-4 rounded-md">
            <p class="text-sm text-red-300">{{ session('status') }}</p>
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-6 bg-red-500/10 border-l-4 border-red-500 p-4 rounded-md">
            <ul class="list-disc pl-5 space-y-1 text-sm text-red-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-medium text-[#c7cedb] mb-2">
                   Username or Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="mail" class="h-5 w-5 text-[#9aa3b2]"></i>
                    </div>
                    <input type="text" id="email" name="email" value="{{ old('email') }}" required
                           class="block w-full pl-10 pr-3 py-3 bg-dark-100 border border-dark-200 rounded-lg text-white placeholder-[#6b7280] focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-colors"
                           placeholder="you@example.com or username">
                </div>
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-sm font-medium text-[#c7cedb] mb-2">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="h-5 w-5 text-[#9aa3b2]"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                           class="block w-full pl-10 pr-10 py-3 bg-dark-100 border border-dark-200 rounded-lg text-white placeholder-[#6b7280] focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-colors"
                           placeholder="••••••••">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button type="button" id="togglePassword" class="text-[#9aa3b2] hover:text-brand focus:outline-none transition-colors">
                            <i data-lucide="eye" class="h-5 w-5" id="eyeIcon"></i>
                            <i data-lucide="eye-off" class="h-5 w-5 hidden" id="eyeOffIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember"
                           class="h-4 w-4 bg-dark-100 border-dark-200 rounded text-brand focus:ring-brand">
                    <label for="remember" class="ml-2 block text-sm text-[#c7cedb]">
                        Remember me
                    </label>
                </div>

                <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand hover:text-brand-2 transition-colors">
                    Forgot password?
                </a>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="brand-btn w-full flex justify-center items-center px-4 py-3 rounded-lg text-white">
                    <i data-lucide="log-in" class="h-5 w-5 mr-2"></i>
                    <span class="font-semibold">Sign In</span>
                </button>
            </div>

            <!-- Social Login -->
            @if ($settings->enable_social_login == 'yes')
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-dark-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-dark-50 text-[#9aa3b2]">Or continue with</span>
                </div>
            </div>

            <div>
                <a href="{{ route('social.redirect', ['social' => 'google']) }}" class="w-full flex justify-center items-center py-2.5 px-4 border border-dark-200 rounded-lg text-sm font-medium text-white bg-dark-100 hover:bg-dark-200 transition-colors">
                    <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                        <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                            <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
                            <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
                            <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
                            <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
                        </g>
                    </svg>
                    Sign in with Google
                </a>
            </div>
            @endif
        </form>
        
        <!-- Sign Up Link -->
        <div class="mt-8 text-center">
            <p class="text-sm text-[#9aa3b2]">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-medium text-brand hover:text-brand-2 transition-colors">
                    Sign up now
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Security Notice -->
<div class="mt-8 text-center">
    <div class="inline-flex items-center text-xs text-[#6b7280]">
        <i data-lucide="shield" class="h-3 w-3 mr-1"></i>
        <span>Secure login - Your data is protected</span>
    </div>
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
            fetch('{{ route('login') }}', {
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