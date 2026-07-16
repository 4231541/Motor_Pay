<?php

namespace App\Http\Requests\Api\V1\Cars;

use Illuminate\Foundation\Http\FormRequest;

class ReorderCarMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media_uuids' => ['required', 'array'],
            'media_uuids.*' => ['required', 'string', 'exists:media,uuid'],
        ];
    }
}
