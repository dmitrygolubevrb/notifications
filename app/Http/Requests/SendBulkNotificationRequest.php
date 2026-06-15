<?php

namespace App\Http\Requests;

use App\Application\DTO\SendBulkNotificationDTO;
use App\Domain\Notification\Enums\Channel;
use App\Domain\Notification\Enums\Priority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendBulkNotificationRequest extends FormRequest
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
            'channel' => ['required', Rule::in(Channel::values())],
            'priority' => ['required', Rule::in(Priority::values())],
            'message' => 'required|string|max:255',
            'idempotency_key' => 'required|string|max:255',
            'subscriber_external_ids' => 'required|array|min:1',
            'subscriber_external_ids.*' => 'required|string|distinct|max:255',
        ];
    }

    protected function prepareForValidation()
    {
        if ($key = $this->header('Idempotency-Key')) {
            $this->merge(['idempotency_key' => $key]);
        }
    }

    public function messages()
    {
        return [
            'idempotency_key.required' => 'Idempotency-Key header is reqoired'
        ];
    }

    public function toDto(): SendBulkNotificationDTO
    {
        return SendBulkNotificationDTO::fromValidated(
            data: $this->validated(),
            idempotencyKey: $this->validated('idempotency_key')
        );
    }
}
