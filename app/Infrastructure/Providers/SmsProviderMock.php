<?php

namespace App\Infrastructure\Providers;

use App\Domain\Notification\Contracts\NotificationProviderInterface;
use App\Domain\Notification\Exceptions\PermanentProviderException;
use App\Domain\Notification\Exceptions\TemporaryProviderException;
use App\Domain\Notification\Models\Notification;
use App\Domain\Notification\ValueObjects\ProviderSendResult;

class SmsProviderMock implements NotificationProviderInterface
{

    public function send(Notification $notification): ProviderSendResult
    {
        $message = $notification->message;

        if (str_contains($message, '__fail_temporary__')) {
            throw new TemporaryProviderException('SMS gateway timeout');
        }
        if (str_contains($message, '__fail_permanent__')) {
            throw new PermanentProviderException('Invalid phone number');
        }

        usleep(random_int(50_000, 200_000));

        return new ProviderSendResult(
            providerRef: sprintf('sms-%d-%s', $notification->id, bin2hex(random_bytes(4))),
        );
    }
}
