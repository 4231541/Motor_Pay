<?php

namespace App\Repositories\Contracts;

use App\Models\CarModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CarModelRepositoryInterface
{
    /**
     * Get paginated car models.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get paginated models for a specific brand.
     *
     * @param int $brandId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedByBrand(int $brandId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all active models for a specific brand (for dropdowns).
     *
     * @param int $brandId
     * @return Collection
     */
    public function getActiveByBrand(int $brandId): Collection;

    /**
     * Find a car model by its ID.
     *
     * @param int $id
     * @return CarModel|null
     */
    public function findById(int $id): ?CarModel;

    /**
     * Find a car model by its ID or throw an exception.
     *
     * @param int $id
     * @return CarModel
     */
    public function findByIdOrFail(int $id): CarModel;

    /**
     * Create a new car model.
     *
     * @param array $data
     * @return CarModel
     */
    public function create(array $data): CarModel;

    /**
     * Update an existing car model.
     *
     * @param CarModel $carModel
     * @param array $data
     * @return bool
     */
    public function update(CarModel $carModel, array $data): bool;

    /**
     * Delete a car model (soft delete).
     *
     * @param CarModel $carModel
     * @return bool|null
     */
    public function delete(CarModel $carModel): ?bool;
}
