<?php

namespace Tests\Support;

use App\Application\Contracts\IdempotencyStoreInterface;

final class ArrayIdempotencyStore implements IdempotencyStoreInterface
{

    /** @var array<string, string> */
    private array $store = [];
    public function acquire(string $key, int $ttlSeconds): bool
    {
        if (isset($this->store[$key])) {
            return false;
        }
        $this->store[$key] = 'processing';
        return true;
    }
    public function get(string $key): ?string
    {
        return $this->store[$key] ?? null;
    }
    public function put(string $key, string $value, int $ttlSeconds): void
    {
        $this->store[$key] = $value;
    }
    public function release(string $key): void
    {
        unset($this->store[$key]);
    }
}
