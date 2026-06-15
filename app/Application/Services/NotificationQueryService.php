<?php

namespace App\Application\Services;

use App\Application\Contracts\NotificationQueryServiceInterface;
use App\Application\Exceptions\SubscriberNotFoundException;
use App\Application\Factories\NotificationQueryResponseFactory;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Enums\Status;
use App\Domain\Subscriber\Contracts\SubscriberRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationQueryService implements NotificationQueryServiceInterface
{

    public function __construct(
        private readonly SubscriberRepositoryInterface    $subscriberRepository,
        private readonly NotificationRepositoryInterface  $notificationRepository,
        private readonly NotificationQueryResponseFactory $notificationQueryResponseFactory,
    ){}

    public function listBySubscriberExternalId(
        string  $subscriberExternalId,
        ?Status $status,
        int     $perPage,
    ): array
    {
        $subscriber = $this->subscriberRepository->findByExternalId(externalId: $subscriberExternalId);

        if ($subscriber === null) {
            throw new SubscriberNotFoundException([$subscriberExternalId]);
        }

        $paginator = $this->notificationRepository->listBySubscriber(
            subscriberId: $subscriber->id,
            status: $status,
            perPage: $perPage
        );
        return $this->notificationQueryResponseFactory->toPaginatedList($paginator);
    }
}
