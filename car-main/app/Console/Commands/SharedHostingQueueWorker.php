<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SharedHostingQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shared:queue-work {--timeout=60 : Seconds the worker should run before exiting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the queue on a shared hosting environment without Supervisor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting queue worker for shared hosting...');
        
        $timeout = $this->option('timeout');
        
        // This command runs the queue worker and forcefully stops it when it is empty
        // or after the specified timeout (so cron can respawn it safely).
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--max-time' => $timeout,
        ]);
        
        $this->info('Queue processing completed or timed out safely.');
    }
}
