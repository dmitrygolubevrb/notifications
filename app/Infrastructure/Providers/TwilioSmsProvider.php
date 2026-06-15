<?php

namespace App\Infrastructure\Providers;

use App\Domain\Notification\Contracts\NotificationProviderInterface;
use App\Domain\Notification\Exceptions\PermanentProviderException;
use App\Domain\Notification\Exceptions\TemporaryProviderException;
use App\Domain\Notification\Models\Notification;
use App\Domain\Notification\ValueObjects\ProviderSendResult;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class TwilioSmsProvider implements NotificationProviderInterface
{

    private Client $client;

    public function __construct(){
        $this->client = new Client(config('notifications.twilio.sid'), config('notifications.twilio.token'));
    }

    public function send(Notification $notification): ProviderSendResult
    {
        $notification->loadMissing('subscriber');

        $phone = $notification->subscriber?->phone;
        if(!$phone) throw new PermanentProviderException('Subscriber phone is missing');

        try {
            $message = $this->client->messages->create(
                $phone,
                [
                    'from' => config('notifications.twilio.from'),
                    'body' => $notification->message,
                ],
            );
            return new ProviderSendResult(providerRef: $message->sid);
        } catch (RestException $exception) {
            if($exception->getStatusCode() >= 500 || $exception->getStatusCode() === 429) {
                throw new TemporaryProviderException($exception->getMessage(), previous: $exception);
            }
            throw new PermanentProviderException($exception->getMessage(), $exception->getStatusCode(), $exception);
        } catch (\Throwable $exception) {
            throw new TemporaryProviderException($exception->getMessage(), previous: $exception);
        }
    }
}
