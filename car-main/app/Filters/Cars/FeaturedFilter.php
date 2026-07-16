<?php

namespace App\Filters\Cars;

use App\DTOs\Cars\CarSearchDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FeaturedFilter
{
    public function __construct(private readonly CarSearchDTO $dto) {}

    public function handle(Builder $query, Closure $next)
    {
        if ($this->dto->featured !== null) {
            $query->where('featured', $this->dto->featured);
        }

        return $next($query);
    }
}
