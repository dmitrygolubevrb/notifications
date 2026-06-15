<?php

namespace App\Application\Responses;

use App\Domain\Subscriber\Models\Subscriber;

final readonly class SubscriberData
{
    public function __construct(
        public int $id,
        public string $externalId,
        public ?string $phone,
        public ?string $email,
        public ?string $createdAt = null,
    ) {
    }

    public static function fromModel(Subscriber $subscriber): self
    {
        return new self(
            id: $subscriber->id,
            externalId: $subscriber->external_id,
            phone: $subscriber->phone,
            email: $subscriber->email,
            createdAt: $subscriber->created_at?->toString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->externalId,
            'phone' => $this->phone,
            'email' => $this->email,
            'created_at' => $this->createdAt,
        ];
    }
}
