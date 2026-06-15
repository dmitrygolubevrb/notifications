<?php

namespace App\Http\Controllers\Api\Notification;

use App\Application\Contracts\NotificationQueryServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListSubscriberNotificationsRequest;
use App\Http\Responses\SubscriberNotificationsResponse;
use Illuminate\Http\JsonResponse;

class IndexBySubscriber extends Controller
{

    public function __invoke(
        ListSubscriberNotificationsRequest $listSubscriberNotificationsRequest,
        string                             $subscriberExternalId,
        NotificationQueryServiceInterface  $notificationQueryService,
    ): JsonResponse
    {

        return SubscriberNotificationsResponse::json(
            $notificationQueryService->listBySubscriberExternalId(
                subscriberExternalId: $subscriberExternalId,
                status: $listSubscriberNotificationsRequest->status(),
                perPage: $listSubscriberNotificationsRequest->perPage()
            )
        );
    }

}
