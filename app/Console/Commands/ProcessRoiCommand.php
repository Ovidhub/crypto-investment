<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AutoTaskController;

class ProcessRoiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roi:process {--force : Force processing even if conditions not met}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automatic ROI for all active investment plans';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting automatic ROI processing...');
        
        $controller = new AutoTaskController();
        
        try {
            $controller->automaticRoi();
            
            $this->info('✓ Automatic ROI processing completed successfully!');
            $this->newLine();
            $this->info('Check storage/logs/laravel.log for any issues.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Automatic ROI processing failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}
