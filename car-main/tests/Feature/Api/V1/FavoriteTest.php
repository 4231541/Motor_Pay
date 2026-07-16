<?php

namespace Tests\Feature\Api\V1;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_favorites(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $user->favorites()->attach($car->id);

        $response = $this->actingAs($user)->getJson('/api/v1/favorites');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.uuid', $car->id);
    }

    public function test_user_can_add_car_to_favorites(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/v1/favorites/{$car->id}");

        $response->assertStatus(201);
        $this->assertCount(1, $user->favorites);
        $this->assertEquals($car->id, $user->favorites->first()->id);
    }

    public function test_user_can_remove_car_from_favorites(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();
        
        $user->favorites()->attach($car->id);
        $this->assertCount(1, $user->favorites);

        $response = $this->actingAs($user)->deleteJson("/api/v1/favorites/{$car->id}");

        $response->assertStatus(200);
        $this->assertCount(0, $user->fresh()->favorites);
    }

    public function test_adding_duplicate_favorite_does_not_crash(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $user->favorites()->attach($car->id);

        // Try adding again
        $response = $this->actingAs($user)->postJson("/api/v1/favorites/{$car->id}");

        $response->assertStatus(201);
        $this->assertCount(1, $user->favorites); // Should still only be 1
    }
}
