<?php

namespace App\Infrastructure\Providers;

use App\Domain\Notification\Contracts\NotificationProviderInterface;
use App\Domain\Notification\Exceptions\PermanentProviderException;
use App\Domain\Notification\Exceptions\TemporaryProviderException;
use App\Domain\Notification\Models\Notification;
use App\Domain\Notification\ValueObjects\ProviderSendResult;
use SendGrid;
use SendGrid\Mail\Mail;

class SendGridEmailProvider implements NotificationProviderInterface
{

    public function send(Notification $notification): ProviderSendResult
    {
        $notification->loadMissing('subscriber');

        $email = $notification->subscriber?->email;
        if ($email === null || $email === '') {
            throw new PermanentProviderException('Subscriber email is missing');
        }

        $mail = new Mail();
        $mail->setFrom(
            config('notifications.sendgrid.from'),
            config('notifications.sendgrid.from_name')
        );
        $mail->addTo($email);
        $mail->setSubject(config('notifications.sendgrid.subject'));
        $mail->addContent('text/plain', $notification->message);

        if (config('notifications.sendgrid.sandbox')) {
            $mail->enableSandBoxMode();
        }

        try {
            $response = (new SendGrid(config('notifications.sendgrid.api_key')))->send($mail);
            $status = $response->statusCode();

            if ($status >= 200 && $status < 300) {
                $messageId = $this->extractMessageId($response->headers());

                return new ProviderSendResult(providerRef: $messageId ?? sprintf('sendgrid-%d', $notification->id));
            }

            if ($status >= 500 || $status === 429) {
                throw new TemporaryProviderException('SendGrid error: HTTP ' . $status);
            }

            throw new PermanentProviderException('SendGrid error: HTTP ' . $status);

        } catch (TemporaryProviderException|PermanentProviderException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new TemporaryProviderException($exception->getMessage(), previous: $exception);
        }
    }

    private function extractMessageId(array $headers): ?string
    {
        foreach ($headers as $header) {
            if (stripos($header, 'X-Message-Id:') === 0) {
                return trim(substr($header, strlen('X-Message-Id:')));
            }
        }
        return null;
    }
}
