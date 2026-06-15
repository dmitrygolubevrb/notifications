<?php

namespace App\Application\Contracts;

use App\Application\Enums\IdempotencyScope;

interface IdempotencyServiceInterface
{
    public function resolve(string $key, IdempotencyScope $idempotencyScope, callable $callback): mixed;
}
