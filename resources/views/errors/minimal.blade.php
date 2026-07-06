
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title') - {{ $settings->site_name }}</title>

	<!-- Favicon -->
	<link rel="icon" href="{{ asset('storage/app/public/' . $settings->favicon) }}" type="image/png" />

	<!-- Tailwind CSS -->
	<script src="https://cdn.tailwindcss.com"></script>
	
	<script>
		tailwind.config = {
			darkMode: 'class',
			theme: {
				extend: {
					colors: {
  primary: {
    DEFAULT: '#0F9D58',
    '500': '#0F9D58',
    '600': '#0B8043', // darker green (replaced blue)
    '700': '#0A6E3A', // deeper green
  },
  dark: {
    DEFAULT: '#111827',
    '50': '#1E2430',
    '100': '#2A303C',
    '200': '#343D4F',
  },
  light: {
    DEFAULT: '#F8FAFC',
    '50': '#FFFFFF',
    '100': '#F1F5F9',
  }
}

				}
			}
		}
	</script>

	<!-- Lucide Icons -->
	<script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>

	<style>
		@keyframes float {
			0%, 100% { transform: translateY(0px); }
			50% { transform: translateY(-20px); }
		}
		.float-animation {
			animation: float 3s ease-in-out infinite;
		}
	</style>
</head>

<body class="dark:bg-dark bg-light-100 dark:text-white text-gray-900 transition-colors duration-200 min-h-screen flex items-center justify-center p-4">
	<div class="max-w-2xl w-full text-center">
		<!-- Error Icon/Code -->
		<div class="mb-8 float-animation">
			<div class="inline-block p-6 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 dark:from-primary/30 dark:to-primary/10">
				<div class="text-8xl md:text-9xl font-black bg-gradient-to-r from-primary to-primary-600 bg-clip-text text-transparent">
					@yield('code')
				</div>
			</div>
		</div>

		<!-- Error Message -->
		<h1 class="text-3xl md:text-4xl font-bold mb-4 dark:text-white text-gray-900">
			@yield('title')
		</h1>
		
		<p class="text-lg md:text-xl dark:text-gray-400 text-gray-600 mb-8 max-w-md mx-auto">
			@yield('message')
		</p>

		<!-- Action Buttons -->
		<div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
			<a href="{{ url()->previous() }}" 
			   class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 dark:bg-dark-100 hover:bg-gray-300 dark:hover:bg-dark-50 text-gray-900 dark:text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
				<i data-lucide="arrow-left" class="w-5 h-5"></i>
				<span>Go Back</span>
			</a>
			
			<a href="{{ route('dashboard') }}" 
			   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
				<i data-lucide="home" class="w-5 h-5"></i>
				<span>Go to Dashboard</span>
			</a>
		</div>

		<!-- Footer -->
		<div class="mt-12 pt-8 border-t dark:border-dark-100 border-gray-200">
			<p class="text-sm dark:text-gray-500 text-gray-500">
				&copy; {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.
			</p>
		</div>
	</div>

	<!-- Initialize Lucide Icons -->
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			lucide.createIcons();
		});
	</script>
</body>
</html>

