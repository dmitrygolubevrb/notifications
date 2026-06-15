<?php

namespace App\Application\Contracts;

use App\Application\DTO\SendBulkNotificationDTO;
use App\Application\DTO\SendNotificationDTO;

interface NotificationDispatchServiceInterface
{
    public function dispatchSingle(SendNotificationDTO $notificationDTO): array;
    public function dispatchBulk(SendBulkNotificationDTO $bulkNotificationDTO): array;
}
