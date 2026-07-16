<?php

namespace App\Filters\Cars;

use App\DTOs\Cars\CarSearchDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class EnumFilter
{
    public function __construct(private readonly CarSearchDTO $dto) {}

    public function handle(Builder $query, Closure $next)
    {
        if ($this->dto->transmission) {
            $query->where('transmission', $this->dto->transmission);
        }

        if ($this->dto->fuelType) {
            $query->where('fuel_type', $this->dto->fuelType);
        }

        if ($this->dto->status) {
            $query->where('status', $this->dto->status);
        }

        return $next($query);
    }
}
