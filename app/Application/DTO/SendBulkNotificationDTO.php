<?php

namespace App\Application\DTO;

use App\Domain\Notification\Enums\Channel;
use App\Domain\Notification\Enums\Priority;

final readonly class SendBulkNotificationDTO
{
    public function __construct(
        public Channel $channel,
        public Priority $priority,
        public string $message,

        /**
         * @var list<string>
         */
        public array $subscriberExternalIds,
        public string $idempotencyKey,
    ){}

    public static function fromValidated(array $data, string $idempotencyKey): self
    {
        return new self(
            channel: Channel::from($data['channel']),
            priority: Priority::from($data['priority']),
            message: $data['message'],
            subscriberExternalIds: array_values($data['subscriber_external_ids']),
            idempotencyKey: $idempotencyKey,
        );
    }
}
