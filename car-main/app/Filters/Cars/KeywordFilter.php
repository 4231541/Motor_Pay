<?php

namespace App\Filters\Cars;

use App\DTOs\Cars\CarSearchDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class KeywordFilter
{
    public function __construct(private readonly CarSearchDTO $dto) {}

    public function handle(Builder $query, Closure $next)
    {
        if ($this->dto->q) {
            $query->where(function (Builder $q) {
                $q->where('title', 'like', '%' . $this->dto->q . '%')
                  ->orWhere('description', 'like', '%' . $this->dto->q . '%')
                  ->orWhere('meta_title', 'like', '%' . $this->dto->q . '%');
            });
        }

        return $next($query);
    }
}
