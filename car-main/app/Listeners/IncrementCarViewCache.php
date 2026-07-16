<?php

namespace App\Listeners;

use App\Events\CarViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class IncrementCarViewCache implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CarViewed $event): void
    {
        try {
            Redis::incr("car:views:{$event->carId}");
        } catch (\Exception $e) {
            // Ignore missing Redis in tests/local
        }
    }
}
