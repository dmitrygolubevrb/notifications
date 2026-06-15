<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Subscriber\Contracts\SubscriberRepositoryInterface;
use App\Domain\Subscriber\Models\Subscriber;
use Illuminate\Support\Collection;

class SubscriberRepository implements SubscriberRepositoryInterface
{

    public function findByExternalId(string $externalId): ?Subscriber
    {
        return Subscriber::where('external_id', $externalId)->first();
    }

    public function findByExternalIds(array $externalIds): Collection
    {
        return Subscriber::whereIn('external_id', $externalIds)->get();
    }

    public function create(array $attributes): Subscriber
    {
        return Subscriber::create($attributes);
    }
}
