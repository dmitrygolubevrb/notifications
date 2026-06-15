<?php

namespace App\Application\Responses;

use App\Domain\Notification\Models\Notification;

final readonly class NotificationData
{

    public function __construct(
        public int     $id,
        public string  $status,
        public string  $channel,
        public string  $message,
        public string  $priority,
        public ?string $createdAt = null,
    )
    {
    }

    public static function fromModel(Notification $notification): self
    {
        return new self(
            id: $notification->id,
            status: $notification->status->value,
            channel: $notification->channel->value,
            message: $notification->message,
            priority: $notification->priority->value,
            createdAt: $notification->created_at?->toString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'channel' => $this->channel,
            'message' => $this->message,
            'priority' => $this->priority,
            'created_at' => $this->createdAt,
        ];
    }
}
