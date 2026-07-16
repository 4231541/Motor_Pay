<?php

namespace Tests\Unit\Services;

use App\DTOs\Cars\CarSearchDTO;
use App\Models\Car;
use App\Services\CarSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CarSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_results_are_cached_and_retrieved(): void
    {
        Car::factory()->create(['title' => 'Test Car', 'is_active' => true, 'published_at' => now()]);

        $dto = new CarSearchDTO(q: 'Test');
        $service = app(CarSearchService::class);

        // First call should hit DB and cache
        $results1 = $service->search($dto);
        $this->assertCount(1, $results1->items());

        // Delete from DB to ensure next call hits cache
        Car::truncate();

        // Second call should return cached results despite empty DB
        $results2 = $service->search($dto);
        $this->assertCount(1, $results2->items());
    }
}
