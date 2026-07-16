<?php

namespace App\Repositories\Contracts;

use App\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BrandRepositoryInterface
{
    /**
     * Get all active brands, usually for dropdowns.
     *
     * @return Collection
     */
    public function getActiveBrands(): Collection;

    /**
     * Get paginated brands with media eager loaded.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a brand by its ID.
     *
     * @param int $id
     * @return Brand|null
     */
    public function findById(int $id): ?Brand;

    /**
     * Find a brand by its ID or throw an exception.
     *
     * @param int $id
     * @return Brand
     */
    public function findByIdOrFail(int $id): Brand;

    /**
     * Create a new brand.
     *
     * @param array $data
     * @return Brand
     */
    public function create(array $data): Brand;

    /**
     * Update an existing brand.
     *
     * @param Brand $brand
     * @param array $data
     * @return bool
     */
    public function update(Brand $brand, array $data): bool;

    /**
     * Delete a brand (soft delete).
     *
     * @param Brand $brand
     * @return bool|null
     */
    public function delete(Brand $brand): ?bool;
}
