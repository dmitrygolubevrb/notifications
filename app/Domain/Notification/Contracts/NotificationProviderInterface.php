<?php

namespace App\Domain\Notification\Contracts;

use App\Domain\Notification\Models\Notification;
use App\Domain\Notification\ValueObjects\ProviderSendResult;

interface NotificationProviderInterface
{
    public function send(Notification $notification): ProviderSendResult;
}
