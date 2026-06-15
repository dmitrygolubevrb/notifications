<?php

namespace App\Application\Services;

use App\Application\Contracts\IdempotencyServiceInterface;
use App\Application\Contracts\NotificationDispatchServiceInterface;
use App\Application\DTO\SendBulkNotificationDTO;
use App\Application\DTO\SendNotificationDTO;
use App\Application\Enums\IdempotencyScope;
use App\Application\Exceptions\SubscriberNotFoundException;
use App\Application\Factories\NotificationDispatchResponseFactory;
use App\Application\Jobs\SendNotificationJob;
use App\Domain\Notification\Contracts\NotificationBatchRepositoryInterface;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Enums\Status;
use App\Domain\Notification\Models\NotificationBatch;
use App\Domain\Subscriber\Contracts\SubscriberRepositoryInterface;
use Illuminate\Support\Arr;

class NotificationDispatchService implements NotificationDispatchServiceInterface
{

    public function __construct(
        private readonly IdempotencyServiceInterface $idempotencyService,
        private readonly NotificationRepositoryInterface $notificationRepository,
        private readonly NotificationBatchRepositoryInterface $notificationBatchRepository,
        private readonly SubscriberRepositoryInterface $subscriberRepository,
        private readonly NotificationDispatchResponseFactory $notificationDispatchResponseFactory,
    ){}

    public function dispatchSingle(SendNotificationDTO $notificationDTO): array
    {
        return $this->idempotencyService->resolve(
            key: $notificationDTO->idempotencyKey,
            idempotencyScope: IdempotencyScope::Single,
            callback: fn () => $this->createSingleNotification($notificationDTO),
        );
    }

    private function createSingleNotification(SendNotificationDTO $sendNotificationDTO): array
    {
        $subscriber = $this->subscriberRepository->findByExternalId($sendNotificationDTO->subscriberExternalId);

        if ($subscriber === null) {
            throw new SubscriberNotFoundException([$sendNotificationDTO->subscriberExternalId]);
        }

        $notification = $this->notificationRepository->create(attributes: [
            'subscriber_id' => $subscriber->id,
            'channel' => $sendNotificationDTO->channel,
            'priority' => $sendNotificationDTO->priority,
            'message' => $sendNotificationDTO->message,
            'status' => Status::Queued,
            'idempotency_key' => $sendNotificationDTO->idempotencyKey,
            'notification_batch_id' => null,
            'attempts' => 0,
        ]);

        SendNotificationJob::dispatch($notification->id)->onQueue($sendNotificationDTO->priority->queueName());

        return $this->notificationDispatchResponseFactory->toSingle($notification);
    }

    public function dispatchBulk(SendBulkNotificationDTO $bulkNotificationDTO): array
    {
        return $this->idempotencyService->resolve(
            key: $bulkNotificationDTO->idempotencyKey,
            idempotencyScope: IdempotencyScope::Bulk,
            callback: fn() => $this->notificationDispatchResponseFactory->toBulk($this->createBulkNotification($bulkNotificationDTO)),
        );
    }

    private function createBulkNotification(SendBulkNotificationDTO $bulkNotificationDTO): NotificationBatch
    {
        $externalIds = array_values(array_unique($bulkNotificationDTO->subscriberExternalIds));

        if ($externalIds === []) {
            throw new \InvalidArgumentException('subscriber_external_ids must not be empty.');
        }

        $subscribers = $this->subscriberRepository->findByExternalIds($externalIds);
        if ($subscribers->count() !== count($externalIds)) {
            $foundIds = $subscribers->pluck('external_id')->all();
            $missingIds = array_values(array_diff($externalIds, $foundIds));

            throw new SubscriberNotFoundException($missingIds);
        }

        $notificationBatch = $this->notificationBatchRepository->create(attributes: [
            'channel' => $bulkNotificationDTO->channel,
            'priority' => $bulkNotificationDTO->priority,
            'message' => $bulkNotificationDTO->message,
            'total_count' => count($externalIds),
            'idempotency_key' => $bulkNotificationDTO->idempotencyKey,
        ]);

        $items = $subscribers->map(fn ($subscriber) => [
            'subscriber_id' => $subscriber->id,
            'channel' => $bulkNotificationDTO->channel,
            'priority' => $bulkNotificationDTO->priority,
            'message' => $bulkNotificationDTO->message,
            'notification_batch_id' => $notificationBatch->id,
        ])->values()->all();

        $notifications = $this->notificationRepository->createMany(items: $items);

        foreach ($notifications as $notification) {
            SendNotificationJob::dispatch($notification->id)->onQueue($bulkNotificationDTO->priority->queueName());
        }
        $notificationBatch->load('notifications');

        return $notificationBatch;
    }
}
