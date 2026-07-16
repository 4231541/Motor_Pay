<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->admin->assignRole('admin');

        $this->customer = User::factory()->create();

        $this->customer->assignRole('customer');
    }

    public function test_admin_can_view_audit_logs(): void
    {
        AuditLog::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/v1/admin/audit-logs');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data',
                         'meta',
                         'links'
                     ]
                 ]);
    }

    public function test_customer_cannot_view_audit_logs(): void
    {
        $response = $this->actingAs($this->customer, 'sanctum')->getJson('/api/v1/admin/audit-logs');

        $response->assertStatus(403);
    }
}
