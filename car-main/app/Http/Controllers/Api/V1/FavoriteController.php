<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CarListResource;
use App\Services\FavoriteService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly FavoriteService $favoriteService) {}

    /**
     * Get a paginated list of the user's favorite cars.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $favorites = $this->favoriteService->getFavoritesForUser($request->user(), $perPage);

        return $this->success(CarListResource::collection($favorites)->response()->getData(true));
    }

    /**
     * Add a car to the user's favorites.
     */
    public function store(Request $request, string $carUuid): JsonResponse
    {
        $this->favoriteService->addFavorite($request->user(), $carUuid);

        return $this->success(null, 'Car added to favorites successfully.', 201);
    }

    /**
     * Remove a car from the user's favorites.
     */
    public function destroy(Request $request, string $carUuid): JsonResponse
    {
        $this->favoriteService->removeFavorite($request->user(), $carUuid);

        return $this->success(null, 'Car removed from favorites successfully.');
    }
}
