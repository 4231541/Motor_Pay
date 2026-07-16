<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Enums\RoleName;
use App\Models\Car;
use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_purchase_request(): void
    {
        $customer = User::factory()->create();
        $car = Car::factory()->create();

        $response = $this->actingAs($customer)->postJson('/api/v1/requests', [
            'car_id' => $car->id,
            'type' => RequestType::Booking->value,
            'customer_message' => 'I would like to book this car.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', RequestStatus::Received->value);
            
        $this->assertDatabaseHas('requests', [
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => RequestStatus::Received->value,
        ]);
    }

    public function test_customer_can_only_view_their_own_requests(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        
        $request = PurchaseRequest::factory()->create(['user_id' => $customer->id]);

        // Customer can view own request
        $this->actingAs($customer)->getJson("/api/v1/requests/{$request->id}")
            ->assertStatus(200);

        // Other customer cannot view
        $this->actingAs($otherCustomer)->getJson("/api/v1/requests/{$request->id}")
            ->assertStatus(403);
    }

    public function test_agent_can_only_view_assigned_requests(): void
    {
        $agent = User::factory()->create();
        $agent->assignRole(RoleName::SalesAgent);
        
        $assignedRequest = PurchaseRequest::factory()->create(['assigned_agent_id' => $agent->id]);
        $unassignedRequest = PurchaseRequest::factory()->create();

        // Agent can view assigned
        $this->actingAs($agent)->getJson("/api/v1/requests/{$assignedRequest->id}")
            ->assertStatus(200);

        // Agent cannot view unassigned
        $this->actingAs($agent)->getJson("/api/v1/requests/{$unassignedRequest->id}")
            ->assertStatus(403);
    }

    public function test_admin_can_assign_agent(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::SuperAdmin);
        
        $agent = User::factory()->create(['is_active' => true]);
        $request = PurchaseRequest::factory()->create();

        $response = $this->actingAs($admin)->patchJson("/api/v1/requests/{$request->id}/assign", [
            'agent_id' => $agent->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.assigned_agent.id', $agent->id);
    }
}
