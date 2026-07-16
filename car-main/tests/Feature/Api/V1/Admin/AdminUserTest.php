<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superAdmin = User::factory()->create();

        $this->superAdmin->assignRole('super_admin');

        $this->regularUser = User::factory()->create();

        $this->regularUser->assignRole('customer');
    }

    public function test_super_admin_can_list_users()
    {
        $response = $this->actingAs($this->superAdmin, 'sanctum')->getJson('/api/v1/admin/users');

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data' => ['data', 'meta', 'links']]);
    }

    public function test_regular_user_cannot_list_users()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')->getJson('/api/v1/admin/users');

        $response->assertStatus(403); // Forbidden due to Policy
    }

    public function test_super_admin_can_create_user()
    {
        $payload = [
            'name' => 'New Agent',
            'email' => 'agent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->superAdmin, 'sanctum')->postJson('/api/v1/admin/users', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.user.email', 'agent@example.com');
                 
        $this->assertDatabaseHas('users', ['email' => 'agent@example.com']);
    }

    public function test_super_admin_can_update_user()
    {
        $payload = ['name' => 'Updated Name'];

        $response = $this->actingAs($this->superAdmin, 'sanctum')->putJson('/api/v1/admin/users/' . $this->regularUser->id, $payload);

        $response->assertStatus(200)
                 ->assertJsonPath('data.user.name', 'Updated Name');
                 
        $this->assertDatabaseHas('users', ['id' => $this->regularUser->id, 'name' => 'Updated Name']);
    }

    public function test_super_admin_can_delete_user()
    {
        $response = $this->actingAs($this->superAdmin, 'sanctum')->deleteJson('/api/v1/admin/users/' . $this->regularUser->id);

        $response->assertStatus(200);
        
        $this->assertSoftDeleted('users', ['id' => $this->regularUser->id]);
    }

    public function test_super_admin_cannot_delete_self()
    {
        $response = $this->actingAs($this->superAdmin, 'sanctum')->deleteJson('/api/v1/admin/users/' . $this->superAdmin->id);

        $response->assertStatus(403);
    }

    public function test_super_admin_can_assign_roles()
    {

        
        $payload = ['roles' => ['admin']];

        $response = $this->actingAs($this->superAdmin, 'sanctum')->patchJson('/api/v1/admin/users/' . $this->regularUser->id . '/roles', $payload);

        $response->assertStatus(200);
        $this->assertTrue($this->regularUser->fresh()->hasRole('admin'));
    }
}
