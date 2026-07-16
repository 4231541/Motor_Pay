<?php

namespace App\Http\Requests\Api\V1\Cars;

use App\Enums\CarCondition;
use App\Enums\FuelType;
use App\Enums\TransmissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'car_model_id' => [
                'required', 
                'integer', 
                Rule::exists('car_models', 'id')->where(function ($query) {
                    return $query->where('brand_id', $this->brand_id);
                })
            ],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'array'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'price' => ['required', 'numeric', 'min:0'],
            'min_installment' => ['nullable', 'numeric', 'min:0'],
            'mileage' => ['required', 'integer', 'min:0'],
            'condition' => ['required', Rule::enum(CarCondition::class)],
            'transmission' => ['required', Rule::enum(TransmissionType::class)],
            'fuel_type' => ['required', Rule::enum(FuelType::class)],
            'grade' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
        ];
    }
}
