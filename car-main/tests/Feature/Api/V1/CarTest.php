<?php

namespace Tests\Feature\Api\V1;

use App\Enums\CarCondition;
use App\Enums\FuelType;
use App\Enums\RoleName;
use App\Enums\TransmissionType;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_published_car_details(): void
    {
        $car = Car::factory()->create([
            'is_active' => true,
            'published_at' => now()->subDay(),
            'status' => 'available',
        ]);

        $response = $this->getJson("/api/v1/cars/{$car->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', $car->title);
    }

    public function test_owner_can_update_own_car(): void
    {
        $owner = User::factory()->create();
        $car = Car::factory()->create(['owner_id' => $owner->id, 'title' => 'Old Title']);

        $response = $this->actingAs($owner)->putJson("/api/v1/cars/{$car->id}", [
            'title' => 'New Title',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'New Title');
    }

    public function test_owner_cannot_update_others_car(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        
        $car = Car::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->putJson("/api/v1/cars/{$car->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_only_admin_can_feature_car(): void
    {
        $owner = User::factory()->create();
        $car = Car::factory()->create(['owner_id' => $owner->id, 'featured' => false]);

        // Owner tries to feature their own car (should fail)
        $response = $this->actingAs($owner)->patchJson("/api/v1/cars/{$car->id}/feature");
        $response->assertStatus(403);

        // Admin tries to feature the car (should succeed)
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::SuperAdmin);

        $response = $this->actingAs($admin)->patchJson("/api/v1/cars/{$car->id}/feature");
        $response->assertStatus(200);
        $this->assertTrue($car->refresh()->featured);
    }
}
