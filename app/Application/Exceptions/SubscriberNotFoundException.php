<?php

namespace App\Application\Exceptions;

use RuntimeException;

class SubscriberNotFoundException extends RuntimeException
{

    /**
     * @param list<string> $externalIds
     */
    public function __construct(public readonly array $externalIds)
    {
        if ($externalIds === []) {
            throw new \InvalidArgumentException('subscriber_external_ids must not be empty.');
        }
        parent::__construct('Subscriber(s) not found: ' . implode(', ', $this->externalIds));
    }

}
