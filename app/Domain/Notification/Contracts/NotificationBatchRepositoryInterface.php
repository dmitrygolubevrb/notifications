<?php

namespace App\Domain\Notification\Contracts;


use App\Domain\Notification\Models\NotificationBatch;

interface NotificationBatchRepositoryInterface
{
    public function findByIdempotencyKey(string $idempotencyKey): ?NotificationBatch;

    public function findByIdempotencyKeyWithNotifications(string $idempotencyKey): ?NotificationBatch;

    public function create(array $attributes): NotificationBatch;
}
