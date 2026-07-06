<?php

namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Settings;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
   protected function schedule(Schedule $schedule): void
{
    // Fetch crypto prices every 5 minutes
    $schedule->command('crypto:fetch-prices')->everyFiveMinutes();
    
    // Process automatic ROI every 5 minutes for plans with short intervals
    $schedule->job(new \App\Jobs\ProcessAutomaticRoi())->everyFiveMinutes();
    
    // Process automatic copy trading profit distribution every 5 minutes
    $schedule->job(new \App\Jobs\ProcessCopyTradingProfit())->everyFiveMinutes();
    
    // Process automated bot trading every 5 minutes
    $schedule->job(new \App\Jobs\ProcessBotTrading())->everyFiveMinutes();
    
    // Process crypto staking rewards every 5 minutes
    $schedule->job(new \App\Jobs\ProcessCryptoStakingRewards())->everyFiveMinutes();
    
    // Fetch stock prices every 15 minutes (checks market hours internally)
    $schedule->command('stock:fetch-prices')->everyFifteenMinutes();
    
    // Process pending stock orders (limit/stop) every minute
    $schedule->command('stock-orders:process')
        ->everyMinute()
        ->withoutOverlapping()
        ->runInBackground();
    
    // Check subscription expiration and send reminders daily at 1 AM
    $schedule->job(new \App\Jobs\ProcessSubscriptionCheck())->dailyAt('01:00');
}


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
