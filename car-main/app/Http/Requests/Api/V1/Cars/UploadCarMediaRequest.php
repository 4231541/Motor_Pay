<?php

namespace App\Http\Requests\Api\V1\Cars;

use Illuminate\Foundation\Http\FormRequest;

class UploadCarMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,webp,jpg', 'max:5120'], // 5MB max per image
        ];
    }
}
