<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\AutoTaskController;

class ProcessCopyTradingProfit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $controller = new AutoTaskController();
        
        try {
            // Process automatic copy trading profit for all active traders
            $controller->automaticCopyTradingProfit();
            
            \Log::info('Automatic copy trading profit processing completed successfully');
        } catch (\Exception $e) {
            \Log::error('Automatic copy trading profit processing failed: ' . $e->getMessage());
            throw $e; // Re-throw to allow retry
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        \Log::error('Automatic copy trading profit job failed after all retries: ' . $exception->getMessage());
    }
}
