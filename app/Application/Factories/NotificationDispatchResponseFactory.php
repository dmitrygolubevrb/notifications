<?php

namespace App\Application\Factories;

use App\Application\Responses\NotificationData;
use App\Domain\Notification\Models\Notification;
use App\Domain\Notification\Models\NotificationBatch;
use App\Http\Resources\NotificationBatchResource;

final class NotificationDispatchResponseFactory
{
    public function toSingle(Notification $notification): array
    {
        return [
            'notification' => NotificationData::fromModel($notification)->toArray(),
        ];
    }

    public function toBulk(NotificationBatch $batch): array
    {
        return NotificationBatchResource::make($batch)->resolve();
    }
}
