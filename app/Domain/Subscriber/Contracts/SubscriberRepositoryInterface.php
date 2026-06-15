<?php

namespace App\Domain\Subscriber\Contracts;

use App\Domain\Subscriber\Models\Subscriber;
use Illuminate\Support\Collection;

interface SubscriberRepositoryInterface
{
    public function findByExternalId(string $externalId): ?Subscriber;
    public function findByExternalIds(array $externalIds): Collection;
    public function create(array $attributes): Subscriber;
}
