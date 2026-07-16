<?php

namespace Database\Factories;

use App\Enums\CarApprovalStatus;
use App\Enums\CarCondition;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'brand_id' => Brand::factory(),
            'car_model_id' => fn (array $attributes) => CarModel::factory()
                ->create(['brand_id' => $attributes['brand_id']])
                ->id,
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'year' => fake()->numberBetween(2010, 2026),
            'price' => fake()->randomFloat(2, 5000, 150000),
            'mileage' => fake()->numberBetween(0, 200000),
            'condition' => fake()->randomElement(CarCondition::cases()),
            'transmission' => fake()->randomElement(TransmissionType::cases()),
            'fuel_type' => fake()->randomElement(FuelType::cases()),
            'color' => fake()->safeColorName(),
            'status' => CarStatus::Available,
            'approval_status' => CarApprovalStatus::Pending,
            'rejection_reason' => null,
        ];
    }

    /**
     * Indicate that the listing has been approved for publishing.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => CarApprovalStatus::Approved,
        ]);
    }

    /**
     * Indicate that the listing has been rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => CarApprovalStatus::Rejected,
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the car has been sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::Sold,
        ]);
    }
}
