<?php

namespace Tests\Unit\Services;

use App\Enums\RequestStatus;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Notifications\PurchaseRequestStatusUpdated;
use App\Services\PurchaseRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PurchaseRequestServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_status_creates_log_and_sends_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $customer = User::factory()->create();
        $request = PurchaseRequest::factory()->create([
            'user_id' => $customer->id,
            'status' => RequestStatus::Received->value,
        ]);

        $service = app(PurchaseRequestService::class);
        
        $updatedRequest = $service->updateStatus(
            $request, 
            RequestStatus::Approved, 
            $admin, 
            'Your credit was approved.'
        );

        $this->assertEquals(RequestStatus::Approved, $updatedRequest->status);

        // Check if log was created
        $this->assertDatabaseHas('request_status_logs', [
            'request_id' => $request->id,
            'changed_by' => $admin->id,
            'old_status' => RequestStatus::Received->value,
            'new_status' => RequestStatus::Approved->value,
            'notes' => 'Your credit was approved.',
        ]);

        // Check if notification was sent to customer
        Notification::assertSentTo(
            [$customer],
            PurchaseRequestStatusUpdated::class
        );
    }
}
