<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository implements BrandRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getActiveBrands(): Collection
    {
        return Brand::with('media')
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Brand::with('media')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Brand
    {
        return Brand::with('media')->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdOrFail(int $id): Brand
    {
        return Brand::with('media')->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Brand $brand, array $data): bool
    {
        return $brand->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Brand $brand): ?bool
    {
        return $brand->delete();
    }
}
