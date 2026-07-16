<?php

namespace Database\Factories;

use App\Enums\RequestStatus;
use App\Models\PurchaseRequest;
use App\Models\RequestStatusLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestStatusLog>
 */
class RequestStatusLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'request_id' => PurchaseRequest::factory(),
            'changed_by' => User::factory(),
            'old_status' => null,
            'new_status' => RequestStatus::Pending,
            'notes' => null,
        ];
    }
}
