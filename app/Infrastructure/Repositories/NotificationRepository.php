<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Enums\Channel;
use App\Domain\Notification\Enums\Priority;
use App\Domain\Notification\Enums\Status;
use App\Domain\Notification\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{

    public function findById(int $id): ?Notification
    {
        return Notification::find($id);
    }

    public function findByIdempotencyKey(string $idempotencyKey): ?Notification
    {
        return Notification::where('idempotency_key', $idempotencyKey)->first();
    }

    public function findForUpdate(int $id): ?Notification
    {
        return Notification::query()->whereKey($id)->lockForUpdate()->first();
    }

    public function create(array $attributes): Notification
    {
        return Notification::create($attributes);
    }

    /**
     * @param list<array{
     *      subscriber_id: int,
     *      channel: Channel,
     *      priority: Priority,
     *      message: string,
     *      idempotency_key?: string|null,
     *      notification_batch_id?: int|null,
     *      attempts?: int
     *  }> $items
     *
     * @return Collection
     */
    public function createMany(array $items): Collection
    {
        $currentTimestamp = now();
        $itemsCollection = collect($items);
        $preparedRows = $itemsCollection->map(fn(array $item) => [
            'subscriber_id' => $item['subscriber_id'],
            'channel' => $item['channel']->value,
            'priority' => $item['priority']->value,
            'status' => Status::Queued->value,
            'message' => $item['message'],
            'idempotency_key' => $item['idempotency_key'] ?? null,
            'notification_batch_id' => $item['notification_batch_id'] ?? null,
            'attempts' => $item['attempts'] ?? 0,
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ])->all();

        Notification::insert($preparedRows);

        $batchId = $itemsCollection->value('notification_batch_id');

        if ($batchId) {
            return Notification::where('notification_batch_id', $batchId)->get();
        }

        return Notification::whereIn(
            'idempotency_key',
            $itemsCollection->pluck('idempotency_key')
                ->filter()
                ->all()
        )->get();
    }

    public function listBySubscriber(int $subscriberId, ?Status $status = null, int $perPage = 20): LengthAwarePaginator
    {
        return Notification::where('subscriber_id', $subscriberId)->when($status, function (Builder $query, Status $status) {
            $query->where('status', $status->value);
        })->latest()->paginate($perPage);
    }
}
