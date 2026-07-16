<?php

namespace App\Services;

use App\Models\Car;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class FavoriteService
{
    private const CACHE_TTL_HOURS = 24;

    /**
     * Get paginated favorite cars for a user.
     */
    public function getFavoritesForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->favorites()
            ->with(['brand', 'carModel', 'media'])
            ->latest('favorites.created_at')
            ->paginate($perPage);
    }

    /**
     * Add a car to user's favorites.
     */
    public function addFavorite(User $user, string $carUuid): void
    {
        // Verify car exists
        Car::findOrFail($carUuid);

        // Attach without detaching to prevent errors if already favorited
        $user->favorites()->syncWithoutDetaching([$carUuid]);

        $this->clearFavoritesCache($user->id);
    }

    /**
     * Remove a car from user's favorites.
     */
    public function removeFavorite(User $user, string $carUuid): void
    {
        $user->favorites()->detach($carUuid);

        $this->clearFavoritesCache($user->id);
    }

    /**
     * Get an array of car UUIDs the user has favorited (cached).
     */
    public function getUserFavoriteCarIds(User $user): array
    {
        $cacheKey = "users:{$user->id}:favorites";

        return Cache::remember($cacheKey, now()->addHours(self::CACHE_TTL_HOURS), function () use ($user) {
            return $user->favorites()->pluck('cars.id')->toArray();
        });
    }

    /**
     * Clear the user's favorites cache.
     */
    private function clearFavoritesCache(int $userId): void
    {
        Cache::forget("users:{$userId}:favorites");
    }
}
