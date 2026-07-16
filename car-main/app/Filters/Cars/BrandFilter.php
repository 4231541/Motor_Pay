<?php

namespace App\Filters\Cars;

use App\DTOs\Cars\CarSearchDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class BrandFilter
{
    public function __construct(private readonly CarSearchDTO $dto) {}

    public function handle(Builder $query, Closure $next)
    {
        if ($this->dto->brand) {
            $query->whereHas('brand', function (Builder $q) {
                $q->where('slug', $this->dto->brand);
            });
        }

        return $next($query);
    }
}
