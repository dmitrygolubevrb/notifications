<?php

namespace App\Http\Controllers\Api\Notification;

use App\Application\Contracts\NotificationDispatchServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendBulkNotificationRequest;
use App\Http\Responses\BulkNotificationCreatedResponse;
use Illuminate\Http\JsonResponse;

class StoreBulkController extends Controller
{

    public function __invoke(
        SendBulkNotificationRequest          $sendBulkNotificationRequest,
        NotificationDispatchServiceInterface $notificationDispatchService
    ): JsonResponse
    {
        return BulkNotificationCreatedResponse::json($notificationDispatchService->dispatchBulk($sendBulkNotificationRequest->toDto()));
    }

}
