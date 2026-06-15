<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Notification\Contracts\NotificationBatchRepositoryInterface;
use App\Domain\Notification\Models\NotificationBatch;

class NotificationBatchRepository implements NotificationBatchRepositoryInterface
{

    public function findByIdempotencyKey(string $idempotencyKey): ?NotificationBatch
    {
        return NotificationBatch::where('idempotency_key', $idempotencyKey)->first();
    }

    public function findByIdempotencyKeyWithNotifications(string $idempotencyKey): ?NotificationBatch
    {
        return NotificationBatch::query()
            ->with('notifications')
            ->where('idempotency_key', $idempotencyKey)
            ->first();
    }

    public function create(array $attributes): NotificationBatch
    {
        return NotificationBatch::create($attributes);
    }
}
