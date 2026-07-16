<?php

namespace App\Filters\Cars;

use App\DTOs\Cars\CarSearchDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class PriceRangeFilter
{
    public function __construct(private readonly CarSearchDTO $dto) {}

    public function handle(Builder $query, Closure $next)
    {
        if ($this->dto->minPrice !== null) {
            $query->where('price', '>=', $this->dto->minPrice);
        }

        if ($this->dto->maxPrice !== null) {
            $query->where('price', '<=', $this->dto->maxPrice);
        }

        return $next($query);
    }
}
