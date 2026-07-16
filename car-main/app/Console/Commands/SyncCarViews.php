<?php

namespace App\Console\Commands;

use App\Models\Car;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class SyncCarViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cars:sync-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync car views from Redis to MySQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting view synchronization...');

        // In a shared hosting environment, Redis might not be configured.
        // We gracefully exit if Redis is inaccessible.
        try {
            Redis::ping();
        } catch (\Exception $e) {
            $this->warn('Redis is not available. Skipping view synchronization.');
            return;
        }

        $keys = Redis::keys('car:views:*');

        if (empty($keys)) {
            $this->info('No views to synchronize.');
            return;
        }

        $updates = [];

        foreach ($keys as $key) {
            // Depending on redis prefixing, we strip the prefix if necessary
            $keyName = str_replace(config('database.redis.options.prefix', ''), '', $key);
            
            // Extract the UUID from 'car:views:uuid'
            $parts = explode(':', $keyName);
            $carId = end($parts);

            // Atomically get and delete the key
            $views = Redis::getdel($keyName);

            if ($views > 0) {
                $updates[$carId] = (int) $views;
            }
        }

        if (empty($updates)) {
            $this->info('No valid view counts found.');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($updates as $carId => $views) {
                // Increment safely in MySQL
                Car::where('id', $carId)->increment('view_count', $views);
            }
            DB::commit();
            $this->info(sprintf('Successfully synchronized views for %d cars.', count($updates)));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to sync views: ' . $e->getMessage());
        }
    }
}
