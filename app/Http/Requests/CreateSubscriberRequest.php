<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'external_id' => ['required', 'string', 'max:255', Rule::unique('subscribers', 'external_id')],
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ];
    }
}
