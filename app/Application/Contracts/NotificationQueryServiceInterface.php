<?php

namespace App\Application\Contracts;

use App\Domain\Notification\Enums\Status;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationQueryServiceInterface
{
    public function listBySubscriberExternalId(
        string $subscriberExternalId,
        ?Status $status,
        int $perPage,
    ): array;
}
