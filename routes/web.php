<?php

use App\Http\Controllers\Admin\ClearCacheController;
use Illuminate\Support\Facades\Route;
use App\Models\Settings;
use Laravel\Fortify\Http\Controllers\NewPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/home.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';
require __DIR__ . '/botman.php';

// Blocked user route
Route::get('/account-blocked', function () {
    $settings = App\Models\Settings::where('id', 1)->first();
    return view('auth.blocked', compact('settings'));
})->name('user.blocked');

//activate and deactivate Online Trader
Route::any('/activate', function () {
	return view('activate.index', [
		'settings' => Settings::where('id', '1')->first(),
	]);
});

// SECURITY: Removed public 'register-license' route (was potential backdoor)
// saveLicense() method has been deleted from ClearCacheController
// Route::get('register-license', [ClearCacheController::class, 'saveLicense']); // REMOVED

Route::any('/revoke', function () {
	return view('revoke.index');
});

Route::post('/reset-password', [NewPasswordController::class, 'store'])
	->middleware(['guest:' . config('fortify.guard')])
	->name('password.update');

// ========================================
// Cron Job Route - Secure Scheduler Endpoint
// ========================================
// This route allows running Laravel's scheduler via HTTP request (for cPanel cron jobs)
// Security: Protected by CRON_SECRET token from .env file
//
// Usage in cPanel Cron Jobs:
// Using wget (every 5 minutes): */5 * * * * wget -q -O /dev/null "https://yourdomain.com/cron?token=YOUR_CRON_SECRET"
// Using curl (every 5 minutes): */5 * * * * curl -s "https://yourdomain.com/cron?token=YOUR_CRON_SECRET" > /dev/null 2>&1
//
Route::get('/cron', function () {
    // Get token from query parameter or X-Cron-Token header
    $token = request()->query('token') ?? request()->header('X-Cron-Token');
    $expectedToken = env('CRON_SECRET');
    
    // Security: Verify token matches and is not empty
    if (empty($expectedToken) || $token !== $expectedToken) {
        \Log::warning('Unauthorized cron access attempt', [
            'ip' => request()->ip(),
            'token_provided' => !empty($token)
        ]);
        abort(403, 'Unauthorized - Invalid or missing cron token');
    }
    
    try {
        // Execute Laravel scheduler (runs all scheduled tasks in app/Console/Kernel.php)
        \Illuminate\Support\Facades\Artisan::call('schedule:run');
        
        $output = \Illuminate\Support\Facades\Artisan::output();
        
        \Log::info('Cron job executed successfully via HTTP', [
            'timestamp' => now()->toDateTimeString(),
            'output' => $output
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Scheduler executed successfully',
            'output' => $output,
            'timestamp' => now()->toDateTimeString(),
            'scheduled_tasks' => [
                'crypto_prices' => 'Every 5 minutes',
                'automatic_roi' => 'Every 5 minutes',
                'copy_trading_profit' => 'Every 5 minutes',
                'bot_trading' => 'Every 5 minutes',
                'staking_rewards' => 'Every 5 minutes',
                'stock_prices' => 'Every 15 minutes',
                'stock_orders' => 'Every minute',
                'subscription_check' => 'Daily at 1:00 AM'
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Cron job execution failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Scheduler execution failed',
            'error' => $e->getMessage(),
            'timestamp' => now()->toDateTimeString()
        ], 500);
    }
})->name('cron.run');