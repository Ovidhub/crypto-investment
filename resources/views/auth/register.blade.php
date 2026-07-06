@extends('layouts.guest')

@section('title', 'Create an Account')

@section('content')
<!-- Card Container -->
<div class="dark:bg-dark-50 bg-white rounded-xl shadow-lg overflow-hidden border dark:border-dark-200/30 border-light-300/30">
    <!-- Card Header -->
    <div class="dark:bg-dark-100/50 bg-light-100/50 p-6 border-b dark:border-dark-200/50 border-light-200/50">
        <h2 class="text-xl font-bold dark:text-white text-dark">Create an Account</h2>
        <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Fill in your details to get started</p>
    </div>
    
    <!-- Card Body -->
    <div class="p-6 md:p-8">
        <!-- Alert Messages -->
        @if (Session::has('status'))
        <div class="mb-6 dark:bg-danger/10 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm dark:text-red-400 text-red-700">{{ session('status') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            
            <!-- Two Column Layout for Form Fields -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="user" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required
                               class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                               placeholder="Enter unique username">
                        @if ($errors->has('username'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('username') }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="user-check" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                               placeholder="Enter your full name">
                        @if ($errors->has('name'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                               placeholder="name@example.com">
                        @if ($errors->has('email'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('email') }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Phone Number -->
                <div>
                    <label for="phone" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="phone" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                               class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                               placeholder="Enter your phone number">
                        @if ($errors->has('phone'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('phone') }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                               class="block w-full pl-10 pr-10 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                               placeholder="Create password">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" id="togglePassword" class="dark:text-gray-400 text-gray-500 hover:text-primary dark:hover:text-primary focus:outline-none transition-colors">
                                <i data-lucide="eye" class="h-5 w-5" id="eyeIcon"></i>
                                <i data-lucide="eye-off" class="h-5 w-5 hidden" id="eyeOffIcon"></i>
                            </button>
                        </div>
                        @if ($errors->has('password'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('password') }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                               placeholder="Confirm password">
                    </div>
                </div>
                
                <!-- Country -->
                <div>
                    <label for="country" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="map-pin" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <select id="country" name="country" required
                                class="block w-full pl-10 pr-10 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors appearance-none">
                            <option selected disabled>Select your country</option>
                            @include('auth.countries')
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i data-lucide="chevron-down" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        @if ($errors->has('country'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('country') }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Referral ID -->
                @if (Session::has('ref_by'))
                <div>
                    <label for="ref_by" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                        Referral ID <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="users" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="text" id="ref_by" name="ref_by" value="{{ session('ref_by') }}" readonly
                               class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors bg-opacity-50"
                               placeholder="Referral ID">
                    </div>
                </div>
                @else
                <div>
                    <label for="ref_by" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                        Referral ID (Optional)
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="users" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="text" id="ref_by" name="ref_by" value="{{ old('ref_by') }}"
                               class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                               placeholder="Enter referral ID if you have one">
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Math Captcha -->
            @if ($settings->math_captcha == 'true')
            <div>
                <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    Security Question <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="calculator" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="text" readonly value="{{ $math_question ?? 'What is ? + ?' }}"
                               class="block w-full pl-10 pr-3 py-3 dark:bg-dark-200 bg-light-100 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-gray-300 text-gray-600 cursor-not-allowed">
                    </div>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="hash" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                        </div>
                        <input type="number" id="math_answer" name="math_answer" value="{{ old('math_answer') }}" required
                               class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                               placeholder="Enter your answer">
                    </div>
                    @if ($errors->has('math_answer'))
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('math_answer') }}</p>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Google ReCaptcha -->
            @if ($settings->captcha == 'true' && $settings->math_captcha != 'true')
            <div>
                <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    Captcha <span class="text-red-500">*</span>
                </label>
                <div class="{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                    {!! NoCaptcha::display() !!}
                    @if ($errors->has('g-recaptcha-response'))
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('g-recaptcha-response') }}</p>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Terms and Conditions -->
            @if ($terms->useterms == 'yes')
            <div>
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="customCheck1" name="terms" type="checkbox" required
                               class="h-4 w-4 dark:bg-dark-100 bg-light-50 dark:border-dark-200 border-light-300 rounded dark:text-primary text-primary focus:ring-primary">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="customCheck1" class="dark:text-gray-300 text-gray-700">
                            I accept the <a href="{{ route('privacy') }}" class="text-primary hover:underline">Terms and Privacy Policy</a>
                        </label>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Register Button -->
            <div>
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-md text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i data-lucide="user-plus" class="h-5 w-5 mr-2"></i>
                    <span class="font-medium">Create Account</span>
                </button>
            </div>
            
            <!-- Social Login -->
            @if ($settings->enable_social_login == 'yes')
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t dark:border-dark-200 border-light-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 dark:bg-dark-50 bg-white dark:text-gray-400 text-gray-500">Or continue with</span>
                </div>
            </div>
            
            <div>
                <a href="{{ route('social.redirect', ['social' => 'google']) }}" class="w-full flex justify-center items-center py-2.5 px-4 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm text-sm font-medium dark:text-white text-dark dark:bg-dark-100 bg-white hover:bg-light-100 dark:hover:bg-dark-200 transition-colors">
                    <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                        <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                            <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
                            <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
                            <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
                            <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
                        </g>
                    </svg>
                    Sign up with Google
                </a>
            </div>
            @endif
        </form>
        
        <!-- Login Link -->
        <div class="mt-8 text-center">
            <p class="text-sm dark:text-gray-400 text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium text-primary hover:text-primary-600 transition-colors">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Security Notice -->
<div class="mt-8 text-center">
    <div class="inline-flex items-center text-xs dark:text-gray-500 text-gray-500">
        <i data-lucide="shield" class="h-3 w-3 mr-1"></i>
        <span>Your information is secure - We respect your privacy</span>
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
        
        // Prevent space in username
        const username = document.getElementById('username');
        if (username) {
            username.addEventListener('keypress', function(e) {
                return e.which !== 32;
            });
        }
        
        // Auto-refresh CSRF token every 10 minutes to prevent 419 errors
        setInterval(function() {
            fetch('{{ route('register') }}', {
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
<script src="//code.tidio.co/6jsrtbd0baan9h0fzzbbesslvf1wqrnx.js" async></script>
@endsection