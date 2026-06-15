<?php

namespace App\Http\Controllers\Api\Subscriber;

use App\Application\Responses\SubscriberData;
use App\Domain\Subscriber\Contracts\SubscriberRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSubscriberRequest;
use App\Http\Responses\SubscriberCreatedResponse;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function __invoke(
        CreateSubscriberRequest $request,
        SubscriberRepositoryInterface $subscriberRepository,
    ): JsonResponse {

        $subscriber = $subscriberRepository->create($request->validated());

        return SubscriberCreatedResponse::json([
            'subscriber' => SubscriberData::fromModel($subscriber)->toArray(),
        ]);
    }
}
