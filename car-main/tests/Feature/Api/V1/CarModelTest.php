<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleName;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_models_for_brand(): void
    {
        $brand = Brand::factory()->create();
        CarModel::factory()->create(['brand_id' => $brand->id, 'name' => 'Camry', 'is_active' => true]);
        
        // Another brand's model
        $otherBrand = Brand::factory()->create();
        CarModel::factory()->create(['brand_id' => $otherBrand->id, 'name' => 'Corolla']);

        $response = $this->getJson("/api/v1/brands/{$brand->id}/models");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Camry');
    }

    public function test_admin_can_create_car_model(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::SuperAdmin);

        $brand = Brand::factory()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/models', [
            'brand_id' => $brand->id,
            'name' => 'Land Cruiser',
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Land Cruiser');

        $this->assertDatabaseHas('car_models', ['name' => 'Land Cruiser', 'brand_id' => $brand->id]);
    }
}
