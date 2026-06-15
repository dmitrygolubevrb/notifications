<?php

namespace App\Domain\Notification\ValueObjects;

readonly class ProviderSendResult
{
        public function __construct(
            public string $providerRef,
        ){}
}
