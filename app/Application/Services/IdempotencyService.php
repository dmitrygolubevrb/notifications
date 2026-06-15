<?php

namespace App\Application\Services;

use App\Application\Contracts\IdempotencyServiceInterface;
use App\Application\Contracts\IdempotencyStoreInterface;
use App\Application\Enums\IdempotencyScope;
use App\Application\Exceptions\IdempotencyConflictException;
use App\Application\Factories\NotificationDispatchResponseFactory;
use App\Domain\Notification\Contracts\NotificationBatchRepositoryInterface;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;


class IdempotencyService implements IdempotencyServiceInterface
{
    private int $defaultTTLSeconds = 86400;

    public function __construct(
        private readonly IdempotencyStoreInterface            $idempotencyStore,
        private readonly NotificationRepositoryInterface      $notificationRepository,
        private readonly NotificationBatchRepositoryInterface $notificationBatchRepository,
        private readonly NotificationDispatchResponseFactory  $notificationDispatchResponseFactory
    )
    {
    }

    public function resolve(string $key, IdempotencyScope $idempotencyScope, callable $callback): mixed
    {
        $cached = $this->idempotencyStore->get(key: $key);
        if ($cached !== null && $cached !== 'processing') {
            $decoded = json_decode($cached, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        if ($duplicate = $this->findDuplicate(key: $key, idempotencyScope: $idempotencyScope)) {
            $this->idempotencyStore->put(key: $key, value: json_encode($duplicate), ttlSeconds: $this->defaultTTLSeconds);

            return $duplicate;
        }

        if (!$this->idempotencyStore->acquire(key: $key, ttlSeconds: $this->defaultTTLSeconds)) {

            if ($cached = $this->idempotencyStore->get(key: $key)) {
                if ($cached !== 'processing') {
                    $decoded = json_decode($cached, true);
                    if (is_array($decoded)) {
                        return $decoded;
                    }
                }
            }

            if ($duplicate = $this->findDuplicate(key: $key, idempotencyScope: $idempotencyScope)) {
                $this->idempotencyStore->put(key: $key, value: json_encode($duplicate), ttlSeconds: $this->defaultTTLSeconds);

                return $duplicate;
            }

            throw new IdempotencyConflictException('Request is already being processed');
        }

        try {
            $result = $callback();
            $this->idempotencyStore->put(key: $key, value: json_encode($result), ttlSeconds: $this->defaultTTLSeconds);

            return $result;
        } catch (\Throwable $exception) {
            $this->idempotencyStore->release(key: $key);
            throw $exception;
        }
    }

    private function findDuplicate(string $key, IdempotencyScope $idempotencyScope): ?array
    {
        return match ($idempotencyScope) {

            IdempotencyScope::Single => ($notification = $this->notificationRepository
                ->findByIdempotencyKey($key)) ? $this->notificationDispatchResponseFactory->toSingle($notification) : null,

            IdempotencyScope::Bulk => ($bulk = $this->notificationBatchRepository
                ->findByIdempotencyKeyWithNotifications($key)) ? $this->notificationDispatchResponseFactory->toBulk($bulk) : null,
        };
    }
}
