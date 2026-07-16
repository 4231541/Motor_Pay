<?php

namespace App\Http\Requests\Api\V1\Cars;

use App\Enums\CarCondition;
use App\Enums\FuelType;
use App\Enums\TransmissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => ['sometimes', 'required', 'integer', 'exists:brands,id'],
            'car_model_id' => [
                'sometimes', 
                'required', 
                'integer', 
                Rule::exists('car_models', 'id')->where(function ($query) {
                    $brandId = $this->input('brand_id') ?? $this->route('car')->brand_id;
                    return $query->where('brand_id', $brandId);
                })
            ],
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'array'],
            'year' => ['sometimes', 'required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'min_installment' => ['nullable', 'numeric', 'min:0'],
            'mileage' => ['sometimes', 'required', 'integer', 'min:0'],
            'condition' => ['sometimes', 'required', Rule::enum(CarCondition::class)],
            'transmission' => ['sometimes', 'required', Rule::enum(TransmissionType::class)],
            'fuel_type' => ['sometimes', 'required', Rule::enum(FuelType::class)],
            'grade' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
        ];
    }
}
