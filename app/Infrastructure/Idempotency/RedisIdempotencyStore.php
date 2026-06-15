<?php

namespace App\Infrastructure\Idempotency;

use App\Application\Contracts\IdempotencyStoreInterface;
use Illuminate\Support\Facades\Redis;

class RedisIdempotencyStore implements IdempotencyStoreInterface
{
    private function redisKey(string $key): string
    {
        return "idempotency:{$key}";
    }

    public function acquire(string $key, int $ttlSeconds): bool
    {
        return Redis::set($this->redisKey($key), 'processing', 'EX', $ttlSeconds, 'NX') === true;
    }

    public function get(string $key): ?string
    {
        return Redis::get($this->redisKey($key));
    }

    public function put(string $key, string $value, int $ttlSeconds): void
    {
        Redis::set($this->redisKey($key), $value, 'EX', $ttlSeconds);
    }

    public function release(string $key): void
    {
        Redis::del($this->redisKey($key));
    }
}
