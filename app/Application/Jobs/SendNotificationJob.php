<?php

namespace App\Application\Jobs;

use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Enums\Status;
use App\Domain\Notification\Exceptions\PermanentProviderException;
use App\Domain\Notification\Exceptions\TemporaryProviderException;
use App\Infrastructure\Providers\NotificationProviderResolver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];


    public function __construct(
        private readonly int $notificationId,
    ){}

    public function handle(
        NotificationRepositoryInterface $notificationRepository,
        NotificationProviderResolver $notificationProviderResolver,
    ): void
    {
        $notification = DB::transaction(function () use ($notificationRepository) {
           $notification = $notificationRepository->findForUpdate($this->notificationId);

           if($notification === null || $notification->status !== Status::Queued) return null;
            $notification->increment('attempts');
            return $notification->fresh();
        });

        if($notification === null) return;

        try {
            $provider = $notificationProviderResolver->resolve($notification->channel);
            $result = $provider->send($notification);

            $notification->markAsSent($result->providerRef);

            SimulateDeliveryCallbackJob::dispatch($this->notificationId)
                ->onQueue($notification->priority->queueName())
                ->delay(now()->addSeconds(3));
        } catch (TemporaryProviderException $exception) {
            throw $exception;
        } catch (PermanentProviderException $exception) {
            $notification->markAsFailed($exception->getMessage());
        }
    }


    public function failed(?\Throwable $exception): void
    {
        $notification = app(NotificationRepositoryInterface::class)->findById($this->notificationId);
        if($notification !== null && $notification->status === Status::Queued) {
            $notification->markAsFailed($exception?->getMessage() ?? 'Delivery failed');
        }
    }


}
