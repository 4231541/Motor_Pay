<?php

namespace Database\Factories;

use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Models\Car;
use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseRequest>
 */
class PurchaseRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'car_id' => Car::factory(),
            'type' => RequestType::Booking,
            'status' => RequestStatus::Received,
            'down_payment' => null,
            'financing_months' => null,
            'monthly_installment' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the request applies for financing.
     */
    public function financing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => RequestType::Financing,
            'down_payment' => fake()->randomFloat(2, 1000, 20000),
            'financing_months' => fake()->randomElement([12, 24, 36, 48, 60]),
            'monthly_installment' => fake()->randomFloat(2, 200, 3000),
        ]);
    }

    /**
     * Indicate that the request has the given status.
     */
    public function status(RequestStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }
}
