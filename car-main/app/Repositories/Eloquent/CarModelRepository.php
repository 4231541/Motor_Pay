<?php

namespace App\Repositories\Eloquent;

use App\Models\CarModel;
use App\Repositories\Contracts\CarModelRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CarModelRepository implements CarModelRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return CarModel::with('brand')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedByBrand(int $brandId, int $perPage = 15): LengthAwarePaginator
    {
        return CarModel::with('brand')
            ->where('brand_id', $brandId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveByBrand(int $brandId): Collection
    {
        return CarModel::where('brand_id', $brandId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?CarModel
    {
        return CarModel::with('brand')->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdOrFail(int $id): CarModel
    {
        return CarModel::with('brand')->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): CarModel
    {
        return CarModel::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(CarModel $carModel, array $data): bool
    {
        return $carModel->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CarModel $carModel): ?bool
    {
        return $carModel->delete();
    }
}
