<?php

namespace App\Services;

use App\Models\Car;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class CarService
{
    /**
     * Find a car by its UUID or fail.
     */
    public function findByIdOrFail(string $uuid): Car
    {
        return Car::with(['brand', 'carModel', 'media'])->findOrFail($uuid);
    }

    /**
     * Find a car by its slug or fail. caches for 24 hours.
     */
    public function findBySlugOrFail(string $slug): Car
    {
        $cacheKey = "cars:slug:{$slug}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($slug) {
            return Car::with(['brand', 'carModel', 'media'])->where('slug', $slug)->firstOrFail();
        });
    }

    /**
     * Get featured cars. Cached for 1 hour.
     */
    public function getFeatured(): Collection
    {
        return Cache::remember('cars:featured', now()->addHour(), function () {
            return Car::with(['brand', 'carModel', 'media'])
                ->published()
                ->available()
                ->featured()
                ->latest()
                ->limit(10)
                ->get();
        });
    }

    /**
     * Create a new car.
     */
    public function createCar(array $data, string $ownerId): Car
    {
        $data['owner_id'] = $ownerId;
        $car = Car::create($data);
        
        $this->clearCaches($car);
        
        return $car->refresh();
    }

    /**
     * Update an existing car.
     */
    public function updateCar(Car $car, array $data): Car
    {
        $car->update($data);
        
        $this->clearCaches($car);
        
        return $car->refresh();
    }

    /**
     * Delete a car.
     */
    public function deleteCar(Car $car): void
    {
        $car->delete();
        $this->clearCaches($car);
    }

    /**
     * Set publish status.
     */
    public function setPublishStatus(Car $car, bool $publish): Car
    {
        $car->update([
            'published_at' => $publish ? now() : null,
            'is_active' => $publish,
        ]);
        
        $this->clearCaches($car);
        
        return $car;
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Car $car): Car
    {
        $car->update([
            'featured' => !$car->featured,
        ]);
        
        $this->clearCaches($car);
        
        return $car;
    }

    /**
     * Clear caches related to cars.
     */
    private function clearCaches(Car $car): void
    {
        // Clear specific car slug cache
        if ($car->slug) {
            Cache::forget("cars:slug:{$car->slug}");
        }
        
        // Clear featured list
        Cache::forget('cars:featured');
        
        // Let search caches expire naturally, unless required by business rules
    }
}
