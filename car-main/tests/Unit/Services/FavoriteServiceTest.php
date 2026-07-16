<?php

namespace Tests\Unit\Services;

use App\Models\Car;
use App\Models\User;
use App\Services\FavoriteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FavoriteServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_service_caches_favorite_ids(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();
        
        $service = app(FavoriteService::class);
        $service->addFavorite($user, $car->id);

        // Call method which should cache the array of ids
        $favorites = $service->getUserFavoriteCarIds($user);
        
        $this->assertContains($car->id, $favorites);
        
        // Assert cache key exists
        $this->assertTrue(Cache::has("users:{$user->id}:favorites"));
    }

    public function test_favorite_service_clears_cache_on_toggle(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();
        
        $service = app(FavoriteService::class);
        $service->addFavorite($user, $car->id);

        // Seed the cache
        $service->getUserFavoriteCarIds($user);
        $this->assertTrue(Cache::has("users:{$user->id}:favorites"));

        // Removing should clear the cache
        $service->removeFavorite($user, $car->id);
        
        $this->assertFalse(Cache::has("users:{$user->id}:favorites"));
    }
}
