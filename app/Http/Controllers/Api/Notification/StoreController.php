<?php

namespace App\Http\Controllers\Api\Notification;

use App\Application\Contracts\NotificationDispatchServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendNotificationRequest;
use App\Http\Responses\NotificationCreatedResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{

    public function __invoke(
        SendNotificationRequest $sendNotificationRequest,
        NotificationDispatchServiceInterface $notificationDispatchService
    ): JsonResponse
    {
        return NotificationCreatedResponse::json($notificationDispatchService->dispatchSingle($sendNotificationRequest->toDto()));
    }
}
