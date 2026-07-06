@extends('layouts.dash')
@section('title', 'Confirm your password')
@section('styles')
@parent
<style>
    /* Animation for security badge */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .security-badge {
        animation: pulse 2.5s infinite ease-in-out;
    }
    
    /* Password field animation */
    @keyframes border-pulse {
        0% { box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(var(--primary-rgb), 0); }
        100% { box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0); }
    }
    
    .pulse-focus:focus {
        animation: border-pulse 2s infinite;
    }
    
    /* Card background animation */
    .animated-bg {
        background: linear-gradient(-45deg, var(--card-bg-1), var(--card-bg-2), var(--card-bg-3), var(--card-bg-4));
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
    }
    
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    /* Input focus effect */
    .security-input:focus {
        border-color: var(--primary-color);
    }
    
    .security-input:not(:focus):hover {
        border-color: var(--primary-color-light);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <!-- Card Header with Badge -->
        <div class="text-center mb-8">
            <div class="security-badge inline-flex items-center justify-center h-20 w-20 rounded-full dark:bg-dark-100 bg-light-200 mb-4 mx-auto relative">
                <div class="absolute inset-0 rounded-full bg-gradient-to-r from-primary/30 to-secondary/30 blur-md"></div>
                <div class="absolute inset-0 rounded-full animate-spin-slow opacity-40" style="border: 1px dashed; border-color: var(--primary-color)"></div>
                <div class="relative">
                    <i data-lucide="shield" class="h-10 w-10 text-primary"></i>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold dark:text-white text-dark">Secure Area</h2>
            <p class="mt-2 text-sm dark:text-gray-300 text-gray-700">Please confirm your password before continuing</p>
        </div>
        
        <!-- Main Card with animated background -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl transition-all">
            <!-- Animated Background -->
            <div class="absolute inset-0 animated-bg -z-10 opacity-25" style="--card-bg-1: var(--primary-light); --card-bg-2: var(--secondary-light); --card-bg-3: var(--tertiary-light); --card-bg-4: var(--dark-100);"></div>
            
            <!-- Security Pattern Overlay -->
            <div class="absolute inset-0 -z-10 opacity-5">
                <svg width="100%" height="100%">
                    <pattern id="securityPattern" width="32" height="32" patternUnits="userSpaceOnUse">
                        <path d="M0 16 L32 16 M16 0 L16 32" stroke="currentColor" stroke-width="0.5"></path>
                        <circle cx="16" cy="16" r="1.5" fill="currentColor"></circle>
                    </pattern>
                    <rect width="100%" height="100%" fill="url(#securityPattern)"></rect>
                </svg>
            </div>
            
            <!-- Glass Morphism Card -->
            <div class="dark:bg-dark-50/80 bg-light-100/80 backdrop-blur-sm border dark:border-dark-100 border-light-200 rounded-2xl p-8 relative z-10">
                <!-- Form -->
                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Password Field -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium dark:text-gray-300 text-gray-700">
                            Enter Password <span class="text-primary">*</span>
                        </label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i data-lucide="lock" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                            </div>
                            <input 
                                id="password"
                                name="password" 
                                type="password" 
                                required 
                                autocomplete="current-password" 
                                autofocus
                                class="block w-full pl-10 py-3 dark:bg-dark-100 bg-light-200 border-0 dark:border-dark-100 border-light-200 dark:text-white text-dark rounded-lg focus:ring-2 focus:ring-primary dark:focus:border-primary focus:border-primary security-input pulse-focus transition-all"
                                placeholder="••••••••••"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <button type="button" id="togglePassword" class="pr-3 dark:text-gray-400 text-gray-600 hover:text-primary dark:hover:text-primary focus:outline-none">
                                    <i data-lucide="eye" class="h-5 w-5 toggle-eye-icon"></i>
                                    <i data-lucide="eye-off" class="h-5 w-5 toggle-eye-icon hidden"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Error Messages -->
                    @if ($errors->any())
                    <div class="rounded-md bg-danger/10 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-circle" class="h-5 w-5 text-danger"></i>
                            </div>
                            <div class="ml-3">
                                <ul class="list-disc space-y-1 pl-5 text-sm dark:text-danger text-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="group relative flex w-full justify-center py-3 px-4 border border-transparent rounded-lg dark:text-white text-dark font-medium shadow-sm transition-all overflow-hidden">
                            <!-- Button Background Effects -->
                            <span class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-90 group-hover:opacity-100 transition-opacity"></span>
                            <!-- Button Content -->
                            <span class="relative flex items-center justify-center text-white">
                                <i data-lucide="shield-check" class="h-5 w-5 mr-2 animate-pulse"></i>
                                Confirm Identity
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Bottom Security Info -->
        <div class="mt-8 text-center">
            <p class="text-xs dark:text-gray-400 text-gray-600 flex items-center justify-center">
                <i data-lucide="info" class="h-3 w-3 mr-1"></i>
                This extra security step helps protect your account
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const toggleIcons = document.querySelectorAll('.toggle-eye-icon');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                // Toggle password visibility
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle eye icon
                toggleIcons.forEach(icon => {
                    icon.classList.toggle('hidden');
                });
            });
        }
        
        // Set CSS variables for animations
        document.documentElement.style.setProperty('--primary-color', getComputedStyle(document.documentElement).getPropertyValue('--tw-text-opacity-primary'));
        document.documentElement.style.setProperty('--primary-color-light', getComputedStyle(document.documentElement).getPropertyValue('--tw-text-opacity-primary-light'));
        document.documentElement.style.setProperty('--primary-rgb', '200, 139, 101'); // Adjust to match your primary color
        
        // Auto-refresh CSRF token every 10 minutes to prevent 419 errors
        setInterval(function() {
            fetch('{{ route('password.confirm') }}', {
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
    });
</script>
@endsection

@section('scripts')
@parent
@endsection