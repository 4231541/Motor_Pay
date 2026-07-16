<?php

namespace App\Http\Requests\Api\V1\Cars;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\TransmissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:120'], // slug
            'model' => ['nullable', 'string', 'max:120'], // slug
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'min_year' => ['nullable', 'integer', 'min:1900'],
            'max_year' => ['nullable', 'integer', 'min:1900', 'gte:min_year'],
            'transmission' => ['nullable', Rule::enum(TransmissionType::class)],
            'fuel_type' => ['nullable', Rule::enum(FuelType::class)],
            'status' => ['nullable', Rule::enum(CarStatus::class)],
            'featured' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'string', 'in:price,-price,year,-year,created_at,-created_at,mileage,-mileage,view_count,-view_count'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
