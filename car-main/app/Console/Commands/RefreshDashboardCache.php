<?php

namespace App\Console\Commands;

use App\Services\DashboardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshDashboardCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:refresh-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the admin dashboard statistics cache';

    /**
     * Execute the console command.
     */
    public function handle(DashboardService $dashboardService)
    {
        $this->info('Clearing old dashboard cache...');
        Cache::forget('dashboard:stats');

        $this->info('Warming up new dashboard statistics...');
        // Calling the service method will automatically cache the new data
        $dashboardService->getStatistics();

        $this->info('Dashboard cache refreshed successfully.');
    }
}
