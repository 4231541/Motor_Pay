<?php

namespace App\Http\Requests\Api\V1\CarModels;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarModelRequest extends FormRequest
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
        $modelId = $this->route('model') ? $this->route('model')->id : null;
        $brandId = $this->input('brand_id') ?? ($this->route('model') ? $this->route('model')->brand_id : null);

        return [
            'brand_id' => ['sometimes', 'required', 'integer', 'exists:brands,id'],
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('car_models')->where(function ($query) use ($brandId) {
                    return $query->where('brand_id', $brandId);
                })->ignore($modelId),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
