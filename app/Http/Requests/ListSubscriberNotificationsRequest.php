<?php

namespace App\Http\Requests;

use App\Domain\Notification\Enums\Status;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListSubscriberNotificationsRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(Status::values())],
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function status(): ?Status
    {
        return $this->filled('status') ? Status::from($this->string('status')->toString()) : null;
    }

    public function perPage(): int
    {
        return $this->integer('per_page', 20);
    }
}
