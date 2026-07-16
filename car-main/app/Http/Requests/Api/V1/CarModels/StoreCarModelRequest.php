<?php

namespace App\Http\Requests\Api\V1\CarModels;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarModelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('car_models')->where(function ($query) {
                    return $query->where('brand_id', $this->brand_id);
                }),
            ],
            'is_active' => ['boolean'],
        ];
    }
}
