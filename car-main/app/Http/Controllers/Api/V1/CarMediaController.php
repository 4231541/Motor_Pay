<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Cars\ReorderCarMediaRequest;
use App\Http\Requests\Api\V1\Cars\UploadCarMediaRequest;
use App\Http\Resources\Api\V1\CarResource;
use App\Services\CarService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CarMediaController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CarService $carService) {}

    /**
     * Upload media to a car gallery.
     */
    public function store(UploadCarMediaRequest $request, string $uuid): JsonResponse
    {
        $car = $this->carService->findByIdOrFail($uuid);
        
        Gate::authorize('update', $car); // Owner or admin can upload

        foreach ($request->file('images') as $image) {
            $car->addMedia($image)->toMediaCollection('car_gallery');
        }

        // Return updated car with new gallery
        return $this->success(new CarResource($car->refresh()), 'Media uploaded successfully.', 201);
    }

    /**
     * Delete a specific media item.
     */
    public function destroy(string $uuid, string $mediaUuid): JsonResponse
    {
        $car = $this->carService->findByIdOrFail($uuid);
        
        Gate::authorize('update', $car);

        $media = $car->getMedia('car_gallery')->where('uuid', $mediaUuid)->first();

        if (!$media) {
            return $this->error('Media not found.', 404);
        }

        $media->delete();

        return $this->success(null, 'Media deleted successfully.');
    }

    /**
     * Reorder media items.
     */
    public function reorder(ReorderCarMediaRequest $request, string $uuid): JsonResponse
    {
        $car = $this->carService->findByIdOrFail($uuid);
        
        Gate::authorize('update', $car);

        // Uses Spatie Media Library's reordering method
        \Spatie\MediaLibrary\MediaCollections\Models\Media::setNewOrder($request->input('media_uuids'));

        return $this->success(new CarResource($car->refresh()), 'Media reordered successfully.');
    }
}
