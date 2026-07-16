<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;
use App\Enums\CarStatus;
use Illuminate\Auth\Access\Response;

class CarPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('car.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Car $car): bool
    {
        if ($user->hasPermission('car.update')) {
            return true;
        }

        // Owner can update if status is still available/pending
        return $user->id === $car->owner_id && $car->status === CarStatus::Available;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Car $car): bool
    {
        if ($user->hasPermission('car.delete')) {
            return true;
        }

        return $user->id === $car->owner_id;
    }

    /**
     * Determine whether the user can publish/unpublish cars.
     */
    public function publish(User $user, Car $car): bool
    {
        return $user->hasPermission('car.publish');
    }

    /**
     * Determine whether the user can feature cars.
     */
    public function feature(User $user, Car $car): bool
    {
        return $user->hasPermission('car.feature');
    }
}
