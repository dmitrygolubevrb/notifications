<?php

namespace App\Infrastructure\Providers;

use App\Domain\Notification\Contracts\NotificationProviderInterface;
use App\Domain\Notification\Enums\Channel;

final class NotificationProviderResolver
{
    public function __construct(
        protected EmailProviderMock     $emailProviderMock,
        protected SmsProviderMock       $smsProviderMock,
        protected SendGridEmailProvider $sendGridEmailProvider,
        protected TwilioSmsProvider     $twilioSmsProvider,
    )
    {
    }

    public function resolve(Channel $channel): NotificationProviderInterface
    {
        return match ($channel) {
            Channel::Email => match (config('notifications.email_driver')) {
                'sendgrid' => $this->sendGridEmailProvider,
                default => $this->emailProviderMock,
            },
            Channel::SMS => match (config('notifications.sms_driver')) {
                'twilio' => $this->twilioSmsProvider,
                default => $this->smsProviderMock,
            },
        };
    }
}
