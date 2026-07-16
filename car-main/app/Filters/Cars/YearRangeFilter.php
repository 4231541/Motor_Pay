<?php

namespace App\Filters\Cars;

use App\DTOs\Cars\CarSearchDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class YearRangeFilter
{
    public function __construct(private readonly CarSearchDTO $dto) {}

    public function handle(Builder $query, Closure $next)
    {
        if ($this->dto->minYear !== null) {
            $query->where('year', '>=', $this->dto->minYear);
        }

        if ($this->dto->maxYear !== null) {
            $query->where('year', '<=', $this->dto->maxYear);
        }

        return $next($query);
    }
}
