<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CarModels\StoreCarModelRequest;
use App\Http\Requests\Api\V1\CarModels\UpdateCarModelRequest;
use App\Http\Resources\Api\V1\CarModelResource;
use App\Services\BrandService;
use App\Services\CarModelService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarModelController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CarModelService $carModelService,
        private readonly BrandService $brandService
    ) {}

    /**
     * Display a listing of car models.
     */
    public function index(Request $request): JsonResponse
    {
        $models = $this->carModelService->getPaginated($request->integer('per_page', 15));
        return $this->success(CarModelResource::collection($models)->response()->getData(true));
    }

    /**
     * Display models for a specific brand.
     */
    public function getByBrand(Request $request, int $brandId): JsonResponse
    {
        // Verify brand exists
        $this->brandService->findByIdOrFail($brandId);

        if ($request->boolean('paginate')) {
            $models = $this->carModelService->getPaginatedByBrand($brandId, $request->integer('per_page', 15));
            return $this->success(CarModelResource::collection($models)->response()->getData(true));
        }

        $models = $this->carModelService->getActiveByBrand($brandId);
        return $this->success(CarModelResource::collection($models));
    }

    /**
     * Store a newly created car model.
     */
    public function store(StoreCarModelRequest $request): JsonResponse
    {
        $model = $this->carModelService->createModel($request->validated());
        return $this->success(new CarModelResource($model), 'Car model created successfully.', 201);
    }

    /**
     * Display the specified car model.
     */
    public function show(int $id): JsonResponse
    {
        $model = $this->carModelService->findByIdOrFail($id);
        return $this->success(new CarModelResource($model));
    }

    /**
     * Update the specified car model.
     */
    public function update(UpdateCarModelRequest $request, int $id): JsonResponse
    {
        $model = $this->carModelService->findByIdOrFail($id);
        $model = $this->carModelService->updateModel($model, $request->validated());

        return $this->success(new CarModelResource($model), 'Car model updated successfully.');
    }

    /**
     * Remove the specified car model.
     */
    public function destroy(int $id): JsonResponse
    {
        $model = $this->carModelService->findByIdOrFail($id);
        $this->carModelService->deleteModel($model);

        return $this->success(null, 'Car model deleted successfully.');
    }
}
