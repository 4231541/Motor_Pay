<?php

namespace App\Services;

use App\Models\CarModel;
use App\Repositories\Contracts\CarModelRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CarModelService
{
    private const CACHE_TTL = 86400; // 24 hours
    private const CACHE_TAG = 'models';

    public function __construct(
        private readonly CarModelRepositoryInterface $repository
    ) {}

    /**
     * Get paginated car models.
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($perPage);
    }

    /**
     * Get paginated car models for a specific brand.
     */
    public function getPaginatedByBrand(int $brandId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginatedByBrand($brandId, $perPage);
    }

    /**
     * Get all active models for a specific brand, fully cached.
     */
    public function getActiveByBrand(int $brandId): Collection
    {
        $cacheKey = "models:list:{$brandId}";

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => $this->repository->getActiveByBrand($brandId)
        );
    }

    /**
     * Find car model by ID.
     */
    public function findByIdOrFail(int $id): CarModel
    {
        return $this->repository->findByIdOrFail($id);
    }

    /**
     * Create a new car model.
     */
    public function createModel(array $data): CarModel
    {
        $model = $this->repository->create($data);
        $this->clearCache();
        return $model->refresh();
    }

    /**
     * Update an existing car model.
     */
    public function updateModel(CarModel $carModel, array $data): CarModel
    {
        $this->repository->update($carModel, $data);
        $this->clearCache();
        return $carModel->refresh();
    }

    /**
     * Soft delete a car model.
     */
    public function deleteModel(CarModel $carModel): void
    {
        $this->repository->delete($carModel);
        $this->clearCache();
    }

    /**
     * Clear all model-related caches.
     */
    private function clearCache(): void
    {
        // Flush all models list caches
        foreach (range(1, 200) as $id) {
            Cache::forget("models:list:{$id}");
        }
    }
}
