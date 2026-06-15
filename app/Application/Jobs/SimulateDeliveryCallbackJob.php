<?php

namespace App\Application\Jobs;

use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Enums\Status;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class SimulateDeliveryCallbackJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $notificationId,
    ){}

    public function handle(NotificationRepositoryInterface $notificationRepository): void
    {
        $notification = DB::transaction(function () use ($notificationRepository){
            $notification = $notificationRepository->findForUpdate($this->notificationId);
            if($notification === null || $notification->status !== Status::Sent) return null;
            return $notification;
        });

        if($notification === null) return;

        $notification->markAsDelivered();
    }
}
