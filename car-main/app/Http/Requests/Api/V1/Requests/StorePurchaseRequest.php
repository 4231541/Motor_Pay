<?php

namespace App\Http\Requests\Api\V1\Requests;

use App\Enums\RequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_id' => ['required', 'string', 'exists:cars,id'],
            'type' => ['required', Rule::enum(RequestType::class)],
            'customer_message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
