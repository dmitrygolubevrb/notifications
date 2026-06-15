<?php

namespace App\Application\Contracts;

interface IdempotencyStoreInterface
{
    public function acquire(string $key, int $ttlSeconds): bool;

    public function get(string $key): ?string;
    public function put(string $key, string $value, int $ttlSeconds): void;
    public function release(string $key): void;
}
