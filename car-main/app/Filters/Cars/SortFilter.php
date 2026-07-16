<?php

namespace App\Filters\Cars;

use App\DTOs\Cars\CarSearchDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class SortFilter
{
    public function __construct(private readonly CarSearchDTO $dto) {}

    public function handle(Builder $query, Closure $next)
    {
        if ($this->dto->sort) {
            $descending = str_starts_with($this->dto->sort, '-');
            $column = ltrim($this->dto->sort, '-');
            
            $allowedColumns = ['created_at', 'price', 'year', 'mileage', 'view_count'];
            
            if (in_array($column, $allowedColumns)) {
                $query->orderBy($column, $descending ? 'desc' : 'asc');
            } else {
                // Default fallback
                $query->latest();
            }
        }

        return $next($query);
    }
}
