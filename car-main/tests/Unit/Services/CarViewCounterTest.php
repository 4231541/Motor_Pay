<?php

namespace Tests\Unit\Services;

use App\Events\CarViewed;
use App\Listeners\IncrementCarViewCache;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class CarViewCounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_car_viewed_event_increments_redis(): void
    {
        $car = Car::factory()->create(['view_count' => 0]);

        // Mock Redis so we don't need a real server running
        Redis::shouldReceive('get')->with("car:views:{$car->id}")->andReturn(1, 6);
        // We also need to mock incr so it doesn't try to connect
        Redis::shouldReceive('incr')->with("car:views:{$car->id}");

        $listener = new IncrementCarViewCache();
        $listener->handle(new CarViewed($car->id));

        $views = Redis::get("car:views:{$car->id}");
        $this->assertEquals(1, (int) $views);

        // Simulate 5 more views
        for ($i = 0; $i < 5; $i++) {
            $listener->handle(new CarViewed($car->id));
        }

        $views = Redis::get("car:views:{$car->id}");
        $this->assertEquals(6, (int) $views);
    }
}
