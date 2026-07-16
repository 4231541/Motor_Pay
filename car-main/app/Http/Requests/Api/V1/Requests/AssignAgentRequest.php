<?php

namespace App\Http\Requests\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agent_id' => [
                'required', 
                'integer', 
                Rule::exists('users', 'id')->where(function ($query) {
                    // Ideally we should check if the user is an agent, but for now we ensure they are active
                    $query->where('is_active', true);
                })
            ],
        ];
    }
}
