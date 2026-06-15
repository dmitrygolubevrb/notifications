<?php

namespace App\Http\Requests;

use App\Application\DTO\SendNotificationDTO;
use App\Domain\Notification\Enums\Channel;
use App\Domain\Notification\Enums\Priority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendNotificationRequest extends FormRequest
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
            'subscriber_external_id' => 'required|string|max:255',
            'channel' => ['required', Rule::in(Channel::values())],
            'priority' => ['required', Rule::in(Priority::values())],
            'message' => 'required|string|max:10000',
            'idempotency_key' => 'required|string|max:255',
        ];
    }

    protected function prepareForValidation()
    {
        if($key = $this->header('Idempotency-Key')) {
            $this->merge(['idempotency_key' => $key]);
        }
    }

    public function messages()
    {
        return [
            'idempotency_key.required' => 'Idempotency-Key header is required',
        ];
    }

    public function toDto(): SendNotificationDTO
    {
        return SendNotificationDTO::fromValidated(
            data: $this->validated(),
            idempotencyKey: $this->validated('idempotency_key')
        );
    }
}
