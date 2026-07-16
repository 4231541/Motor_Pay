<?php

namespace Tests\Feature\Api\V1;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\Brand;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Since we test uploads, fake the local disk
        Storage::fake('local');
    }

    public function test_guest_can_list_active_brands(): void
    {
        Brand::factory()->create(['name' => 'Toyota', 'is_active' => true]);
        Brand::factory()->create(['name' => 'InactiveBrand', 'is_active' => false]);

        $response = $this->getJson('/api/v1/brands');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Toyota');
    }

    public function test_guest_can_view_single_brand(): void
    {
        $brand = Brand::factory()->create(['name' => 'Toyota']);

        $response = $this->getJson("/api/v1/brands/{$brand->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Toyota');
    }

    public function test_admin_can_create_brand_with_logo(): void
    {
        if (!function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD extension is not installed.');
        }

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::SuperAdmin);

        $file = UploadedFile::fake()->image('logo.jpg');

        $response = $this->actingAs($admin)->postJson('/api/v1/brands', [
            'name' => 'Hyundai',
            'is_active' => true,
            'logo' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Hyundai');

        $this->assertDatabaseHas('brands', ['name' => 'Hyundai']);
        
        // Assert media was attached
        $brand = Brand::where('name', 'Hyundai')->first();
        $this->assertNotEmpty($brand->getMedia('brand_logo'));
    }

    public function test_customer_cannot_create_brand(): void
    {
        $customer = User::factory()->create();

        $response = $this->actingAs($customer)->postJson('/api/v1/brands', [
            'name' => 'Hyundai',
            'is_active' => true,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_brand(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::SuperAdmin);

        $brand = Brand::factory()->create(['name' => 'OldName']);

        $response = $this->actingAs($admin)->putJson("/api/v1/brands/{$brand->id}", [
            'name' => 'NewName',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'NewName');

        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'name' => 'NewName']);
    }

    public function test_admin_can_soft_delete_brand(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::SuperAdmin);

        $brand = Brand::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/v1/brands/{$brand->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('brands', ['id' => $brand->id]);
    }

    public function test_customer_cannot_delete_brand(): void
    {
        $customer = User::factory()->create();
        $brand = Brand::factory()->create();

        $response = $this->actingAs($customer)->deleteJson("/api/v1/brands/{$brand->id}");

        $response->assertStatus(403);
    }
}
