<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Cars\CarSearchDTO;
use App\Events\CarViewed;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Cars\SearchCarRequest;
use App\Http\Requests\Api\V1\Cars\StoreCarRequest;
use App\Http\Requests\Api\V1\Cars\UpdateCarRequest;
use App\Http\Resources\Api\V1\CarListResource;
use App\Http\Resources\Api\V1\CarResource;
use App\Services\CarSearchService;
use App\Services\CarService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CarController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CarService $carService,
        private readonly CarSearchService $carSearchService
    ) {}

    /**
     * Search and list cars.
     */
    public function index(SearchCarRequest $request): JsonResponse
    {
        $dto = CarSearchDTO::fromRequest($request);
        $cars = $this->carSearchService->search($dto);

        return $this->success(CarListResource::collection($cars)->response()->getData(true));
    }

    /**
     * Get featured cars.
     */
    public function featured(): JsonResponse
    {
        $cars = $this->carService->getFeatured();
        return $this->success(CarListResource::collection($cars));
    }

    /**
     * Store a newly created car.
     */
    public function store(StoreCarRequest $request): JsonResponse
    {
        Gate::authorize('create', \App\Models\Car::class);

        $car = $this->carService->createCar($request->validated(), $request->user()->id);

        return $this->success(new CarResource($car), 'Car created successfully.', 201);
    }

    /**
     * Display the specified car by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $car = $this->carService->findBySlugOrFail($slug);

        // Fire event to increment view counter in Redis
        event(new CarViewed($car->id));

        return $this->success(new CarResource($car));
    }

    /**
     * Update the specified car.
     */
    public function update(UpdateCarRequest $request, string $uuid): JsonResponse
    {
        $car = $this->carService->findByIdOrFail($uuid);
        
        Gate::authorize('update', $car);

        $car = $this->carService->updateCar($car, $request->validated());

        return $this->success(new CarResource($car), 'Car updated successfully.');
    }

    /**
     * Remove the specified car.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $car = $this->carService->findByIdOrFail($uuid);
        
        Gate::authorize('delete', $car);

        $this->carService->deleteCar($car);

        return $this->success(null, 'Car deleted successfully.');
    }

    /**
     * Publish or unpublish a car.
     */
    public function publish(string $uuid): JsonResponse
    {
        $car = $this->carService->findByIdOrFail($uuid);
        
        Gate::authorize('publish', $car);

        $this->carService->setPublishStatus($car, true);

        return $this->success(new CarResource($car), 'Car published successfully.');
    }

    /**
     * Toggle featured status of a car.
     */
    public function toggleFeatured(string $uuid): JsonResponse
    {
        $car = $this->carService->findByIdOrFail($uuid);
        
        Gate::authorize('feature', $car);

        $this->carService->toggleFeatured($car);

        return $this->success(new CarResource($car), 'Car featured status updated.');
    }
}
