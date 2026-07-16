<?php

namespace Tests\Feature\Api\V1;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_search_cars_by_keyword(): void
    {
        Car::factory()->create(['title' => 'Toyota Camry 2024', 'is_active' => true, 'published_at' => now()]);
        Car::factory()->create(['title' => 'Honda Civic 2023', 'is_active' => true, 'published_at' => now()]);

        $response = $this->getJson('/api/v1/cars?q=Camry');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.title', 'Toyota Camry 2024');
    }

    public function test_search_respects_price_ranges(): void
    {
        Car::factory()->create(['price' => 10000, 'is_active' => true, 'published_at' => now()]);
        Car::factory()->create(['price' => 20000, 'is_active' => true, 'published_at' => now()]);
        Car::factory()->create(['price' => 30000, 'is_active' => true, 'published_at' => now()]);

        $response = $this->getJson('/api/v1/cars?min_price=15000&max_price=25000');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.price', '20000.00');
    }

    public function test_unpublished_cars_are_hidden_from_public_search(): void
    {
        Car::factory()->create(['title' => 'Published Car', 'is_active' => true, 'published_at' => now()]);
        Car::factory()->create(['title' => 'Draft Car', 'is_active' => false, 'published_at' => null]);

        $response = $this->getJson('/api/v1/cars');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.title', 'Published Car');
    }
}
