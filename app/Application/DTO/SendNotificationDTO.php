<?php

namespace App\Application\DTO;

use App\Domain\Notification\Enums\Channel;
use App\Domain\Notification\Enums\Priority;

final readonly class SendNotificationDTO
{
    public function __construct(
        public Channel $channel,
        public Priority $priority,
        public string $message,
        public string $subscriberExternalId,
        public string $idempotencyKey,
    ){}

    public static function fromValidated(array $data, string $idempotencyKey): self
    {
        return new self(
            channel: Channel::from($data['channel']),
            priority: Priority::from($data['priority']),
            message: $data['message'],
            subscriberExternalId: $data['subscriber_external_id'],
            idempotencyKey: $idempotencyKey
        );
    }
}
