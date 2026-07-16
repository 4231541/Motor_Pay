<?php

use Illuminate\Support\Facades\Schedule;

// Schedule the queue worker for shared hosting (runs every minute, exits on empty/timeout)
Schedule::command('shared:queue-work --timeout=55')->everyMinute()->withoutOverlapping();

// Synchronize Redis views to MySQL every 5 minutes
Schedule::command('cars:sync-views')->everyFiveMinutes()->withoutOverlapping();

// Refresh the expensive admin dashboard cache every hour
Schedule::command('dashboard:refresh-cache')->hourly();
