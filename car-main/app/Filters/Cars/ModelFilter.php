<?php

namespace App\Filters\Cars;

use App\DTOs\Cars\CarSearchDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class ModelFilter
{
    public function __construct(private readonly CarSearchDTO $dto) {}

    public function handle(Builder $query, Closure $next)
    {
        if ($this->dto->model) {
            $query->whereHas('carModel', function (Builder $q) {
                $q->where('slug', $this->dto->model);
            });
        }

        return $next($query);
    }
}
