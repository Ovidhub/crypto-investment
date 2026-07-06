@extends('layouts.guest')

@section('title', 'Two factor login')

@section('styles')
@parent

@endsection

@section('content')
<!-- Card Container -->
<div class="dark:bg-dark-50 bg-white rounded-xl shadow-lg overflow-hidden border dark:border-dark-200/30 border-light-300/30">
    <!-- Card Header -->
    <div class="dark:bg-dark-100/50 bg-light-100/50 p-6 border-b dark:border-dark-200/50 border-light-200/50">
        <h2 class="text-xl font-bold dark:text-white text-dark">Two-Factor Authentication</h2>
        <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Enter the verification code sent to your email</p>
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
        
        <div class="mb-6 dark:bg-primary/10 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle h-5 w-5 text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm dark:text-blue-400 text-blue-700">A 2FA authentication code has been sent to your email. Please check your inbox and enter the code below to continue.</p>
                </div>
            </div>
        </div>
        
        <!-- Two-Factor Form -->
        <form method="POST" action="{{ route('twofalogin') }}" class="space-y-6">
            @csrf
            
            <!-- Two-Factor Code Input -->
            <div>
                <label for="twofa" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    Authentication Code
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-key h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                    </div>
                    <input type="text" id="twofa" name="twofa" required
                           class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                           placeholder="Enter the 6-digit code">
                </div>
                @if ($errors->has('twofa'))
                <span class="text-sm text-red-600 dark:text-red-400 mt-1 block">
                    {{ $errors->first('twofa') }}
                </span>
                @endif
            </div>
            
            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-md text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-shield-alt h-5 w-5 mr-2"></i>
                    <span class="font-medium">Verify Code</span>
                </button>
            </div>
            
            <!-- Retry Login Link -->
            <div class="text-center mt-4">
                <a href="{{ route('adminlogout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                   class="text-sm font-medium text-primary hover:text-primary-600 transition-colors">
                    <i class="fas fa-redo h-3 w-3 mr-1"></i> Try again with different credentials
                </a>
                <form id="logout-form" action="{{ route('adminlogout') }}" method="POST" class="hidden">
                    {{ csrf_field() }}
                </form>
            </div>
        </form>
    </div>
</div>

<!-- Security Notice -->
<div class="mt-8 text-center">
    <div class="inline-flex items-center text-xs dark:text-gray-500 text-gray-500">
        <i class="fas fa-shield-alt h-3 w-3 mr-1"></i>
        <span>Additional security layer - Your account is protected</span>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    // Auto-refresh CSRF token every 10 minutes to prevent 419 errors
    setInterval(function() {
        fetch('{{ route('twofalogin') }}', {
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