<?php

namespace App\Domain\Notification\Contracts;

use App\Domain\Notification\Enums\Status;
use App\Domain\Notification\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface NotificationRepositoryInterface
{
    public function findById(int $id): ?Notification;
    public function findByIdempotencyKey(string $idempotencyKey): ?Notification;
    public function findForUpdate(int $id): ?Notification;
    public function create(array $attributes): Notification;
    public function createMany(array $items): Collection;
    public function listBySubscriber(
        int $subscriberId,
        ?Status $status = null,
        int $perPage = 20,
    ): LengthAwarePaginator;
}
