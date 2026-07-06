@extends('layouts.guest')

@section('title', 'Verification')

@section('content')
<!-- Card Container -->
<div class="dark:bg-dark-50 bg-white rounded-xl shadow-lg overflow-hidden border dark:border-dark-200/30 border-light-300/30">
    <!-- Card Header -->
    <div class="dark:bg-dark-100/50 bg-light-100/50 p-6 border-b dark:border-dark-200/50 border-light-200/50">
        <h2 class="text-xl font-bold dark:text-white text-dark">Verification Required</h2>
        <p class="mt-1 text-sm dark:text-gray-400 text-gray-600">Please confirm you are not a robot</p>
    </div>
    
    <!-- Card Body -->
    <div class="p-6 md:p-8">
        <!-- Alert Messages -->
        @if(Session::has('success'))
        <div class="mb-6 dark:bg-danger/10 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm dark:text-red-400 text-red-700">{{ Session::get('success') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Verification Info -->
        <div class="mb-6 text-center">
            <p class="dark:text-gray-300 text-gray-700">
                Please enter the verification code below to continue to registration.
            </p>
        </div>
        
        <!-- Verification Form -->
        <form method="POST" action="{{ route('codeverify') }}" class="space-y-6">
            @csrf
            
            <!-- Auto-Generated Code Display -->
            <div>
                <label class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    Verification Code
                </label>
                <div class="relative">
                    <input type="text" name="email" value="{{$captcha}}" readonly
                           class="block w-full py-3 px-4 text-center font-bold text-lg dark:bg-primary/10 bg-primary/5 border-0 dark:text-primary text-primary dark:border-primary/20 border-primary/20 rounded-lg shadow-sm focus:outline-none">
                </div>
            </div>
            
            <!-- Code Input -->
            <div>
                <label for="code" class="block text-sm font-medium dark:text-gray-300 text-gray-700 mb-2">
                    Enter Code <span class="text-red-500">*</span>
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="key" class="h-5 w-5 dark:text-gray-400 text-gray-500"></i>
                    </div>
                    <input type="number" id="code" name="code" required
                           class="block w-full pl-10 pr-3 py-3 dark:bg-dark-100 bg-light-50 border dark:border-dark-200 border-light-300 rounded-lg shadow-sm dark:text-white text-dark focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                           placeholder="Enter the code shown above" autofocus>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-md text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                    <span class="font-medium">Verify Code</span>
                </button>
            </div>
        </form>
        
        <!-- Additional Information -->
        <div class="mt-6 text-center">
            <p class="text-sm dark:text-gray-400 text-gray-600">
                This verification helps us protect our platform from automated bots.
            </p>
        </div>
    </div>
</div>

<!-- Security Notice -->
<div class="mt-8 text-center">
    <div class="inline-flex items-center text-xs dark:text-gray-500 text-gray-500">
        <i data-lucide="shield" class="h-3 w-3 mr-1"></i>
        <span>Secure verification - User protection is our priority</span>
    </div>
</div>
@endsection