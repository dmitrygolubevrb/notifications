<?php

namespace Tests\Feature\Api;

use App\Domain\Subscriber\Models\Subscriber;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class IdempotencyTest extends TestCase
{
    public function test_returns_same_response_for_duplicate_single_notification_key(): void
    {
        Queue::fake();
        $this->authenticateApi();

        Subscriber::factory()->create(['external_id' => 'user-1']);

        $payload = [
            'subscriber_external_id' => 'user-1',
            'channel' => 'sms',
            'priority' => 'normal',
            'message' => 'Once',
        ];

        $first = $this->withIdempotencyKey('same-single-key')
            ->postJson('/api/v1/notifications', $payload);

        $second = $this->withIdempotencyKey('same-single-key')
            ->postJson('/api/v1/notifications', $payload);

        $first->assertAccepted();
        $second->assertAccepted();
        $this->assertSame($first->json(), $second->json());
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_returns_same_response_when_redis_cache_is_missing_but_db_record_exists(): void
    {
        Queue::fake();
        $this->authenticateApi();

        $subscriber = Subscriber::factory()->create(['external_id' => 'user-1']);

        $payload = [
            'subscriber_external_id' => 'user-1',
            'channel' => 'sms',
            'priority' => 'normal',
            'message' => 'Once',
        ];

        $first = $this->withIdempotencyKey('redis-miss-key')
            ->postJson('/api/v1/notifications', $payload);

        $first->assertAccepted();

        app(\App\Application\Contracts\IdempotencyStoreInterface::class)->release('redis-miss-key');

        $second = $this->withIdempotencyKey('redis-miss-key')
            ->postJson('/api/v1/notifications', $payload);

        $second->assertAccepted();
        $this->assertSame($first->json(), $second->json());
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_returns_same_response_for_duplicate_bulk_notification_key(): void
    {
        Queue::fake();
        $this->authenticateApi();

        $subscribers = Subscriber::factory()->count(2)->create();
        $externalIds = $subscribers->pluck('external_id')->all();

        $payload = [
            'subscriber_external_ids' => $externalIds,
            'channel' => 'email',
            'priority' => 'marketing',
            'message' => 'Bulk once',
        ];

        $first = $this->withIdempotencyKey('same-bulk-key')
            ->postJson('/api/v1/notifications/bulk', $payload);

        $second = $this->withIdempotencyKey('same-bulk-key')
            ->postJson('/api/v1/notifications/bulk', $payload);

        $first->assertAccepted();
        $second->assertAccepted();
        $this->assertSame($first->json(), $second->json());
        $this->assertDatabaseCount('notification_batches', 1);
        $this->assertDatabaseCount('notifications', 2);
    }
}
