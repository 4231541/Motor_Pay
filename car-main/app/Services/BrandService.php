<?php

namespace App\Services;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class BrandService
{
    private const CACHE_TTL = 86400; // 24 hours
    private const CACHE_TAG = 'brands';
    private const CACHE_KEY_LIST = 'brands:list';

    public function __construct(
        private readonly BrandRepositoryInterface $repository
    ) {}

    /**
     * Get paginated brands with caching.
     * Caching paginated results is tricky, usually we cache the base list
     * and let the DB handle pagination, but if requested, we could cache pages.
     * For now, we will cache the full list of active brands for dropdowns,
     * and let paginated admin views hit the DB (they need fresh data).
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($perPage);
    }

    /**
     * Get all active brands, fully cached.
     */
    public function getActiveBrands(): Collection
    {
        return Cache::remember(
            self::CACHE_KEY_LIST,
            self::CACHE_TTL,
            fn () => $this->repository->getActiveBrands()
        );
    }

    /**
     * Find brand by ID.
     */
    public function findByIdOrFail(int $id): Brand
    {
        return $this->repository->findByIdOrFail($id);
    }

    /**
     * Create a new brand and handle media upload.
     */
    public function createBrand(array $data, ?UploadedFile $logo = null): Brand
    {
        $brand = $this->repository->create($data);

        if ($logo) {
            $brand->addMedia($logo)->toMediaCollection('brand_logo');
        }

        $this->clearCache();

        return $brand->refresh();
    }

    /**
     * Update an existing brand and handle media upload.
     */
    public function updateBrand(Brand $brand, array $data, ?UploadedFile $logo = null): Brand
    {
        $this->repository->update($brand, $data);

        if ($logo) {
            $brand->addMedia($logo)->toMediaCollection('brand_logo');
        }

        $this->clearCache();

        return $brand->refresh();
    }

    /**
     * Soft delete a brand.
     */
    public function deleteBrand(Brand $brand): void
    {
        $this->repository->delete($brand);
        $this->clearCache();
    }

    /**
     * Clear all brand-related caches.
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_LIST);
    }
}
