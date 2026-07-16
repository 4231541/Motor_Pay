<?php

namespace App\DTOs\Cars;

use Illuminate\Http\Request;

class CarSearchDTO
{
    public function __construct(
        public readonly ?string $q = null,
        public readonly ?string $brand = null,
        public readonly ?string $model = null,
        public readonly ?float $minPrice = null,
        public readonly ?float $maxPrice = null,
        public readonly ?int $minYear = null,
        public readonly ?int $maxYear = null,
        public readonly ?string $transmission = null,
        public readonly ?string $fuelType = null,
        public readonly ?string $status = 'available',
        public readonly ?bool $featured = null,
        public readonly string $sort = '-created_at',
        public readonly int $perPage = 15,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            q: $request->query('q'),
            brand: $request->query('brand'),
            model: $request->query('model'),
            minPrice: $request->has('min_price') ? (float) $request->query('min_price') : null,
            maxPrice: $request->has('max_price') ? (float) $request->query('max_price') : null,
            minYear: $request->has('min_year') ? (int) $request->query('min_year') : null,
            maxYear: $request->has('max_year') ? (int) $request->query('max_year') : null,
            transmission: $request->query('transmission'),
            fuelType: $request->query('fuel_type'),
            status: $request->query('status', 'available'),
            featured: $request->has('featured') ? $request->boolean('featured') : null,
            sort: $request->query('sort', '-created_at'),
            perPage: (int) $request->query('per_page', 15),
        );
    }
}
