<?php

namespace App\Services;

use App\DTOs\Cars\CarSearchDTO;
use App\Filters\Cars\BrandFilter;
use App\Filters\Cars\EnumFilter;
use App\Filters\Cars\FeaturedFilter;
use App\Filters\Cars\KeywordFilter;
use App\Filters\Cars\ModelFilter;
use App\Filters\Cars\PriceRangeFilter;
use App\Filters\Cars\SortFilter;
use App\Filters\Cars\YearRangeFilter;
use App\Models\Car;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Cache;

class CarSearchService
{
    private const CACHE_TTL_MINUTES = 15;

    public function __construct(private readonly Pipeline $pipeline) {}

    /**
     * Search and filter cars based on DTO.
     */
    public function search(CarSearchDTO $dto): LengthAwarePaginator
    {
        // Generate a cache key based on the DTO properties
        $cacheKey = 'cars:search:' . md5(serialize($dto));

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($dto) {
            $query = Car::query()
                ->published()
                ->available()
                // Eager load relations necessary for lists to prevent N+1
                ->with(['brand', 'carModel', 'media']);

            return $this->pipeline
                ->send($query)
                ->through([
                    new KeywordFilter($dto),
                    new BrandFilter($dto),
                    new ModelFilter($dto),
                    new PriceRangeFilter($dto),
                    new YearRangeFilter($dto),
                    new EnumFilter($dto),
                    new FeaturedFilter($dto),
                    new SortFilter($dto),
                ])
                ->thenReturn()
                ->paginate($dto->perPage);
        });
    }
}
