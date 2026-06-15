<?php

namespace Tests\Feature\Api\Notification;

use App\Domain\Subscriber\Models\Subscriber;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubscriberNotFoundTest extends TestCase
{
    public function test_returns_not_found_when_single_subscriber_is_missing(): void
    {
        Queue::fake();
        $this->authenticateApi();

        $response = $this->withIdempotencyKey('key-single-404')
            ->postJson('/api/v1/notifications', [
                'subscriber_external_id' => 'missing-user',
                'channel' => 'sms',
                'priority' => 'normal',
                'message' => 'Hi',
            ]);

        $response
            ->assertNotFound()
            ->assertJsonPath('subscriber_external_ids', ['missing-user']);

        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_returns_not_found_when_bulk_subscriber_is_missing(): void
    {
        Queue::fake();
        $this->authenticateApi();

        $existing = Subscriber::factory()->create(['external_id' => 'exists']);

        $response = $this->withIdempotencyKey('key-bulk-404')
            ->postJson('/api/v1/notifications/bulk', [
                'subscriber_external_ids' => [$existing->external_id, 'missing-user'],
                'channel' => 'email',
                'priority' => 'marketing',
                'message' => 'Bulk hi',
            ]);

        $response
            ->assertNotFound()
            ->assertJsonPath('subscriber_external_ids', ['missing-user']);

        $this->assertDatabaseCount('notification_batches', 0);
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_returns_not_found_when_listing_notifications_for_missing_subscriber(): void
    {
        $this->authenticateApi();

        $response = $this->getJson('/api/v1/subscribers/missing-user/notifications');

        $response
            ->assertNotFound()
            ->assertJsonPath('subscriber_external_ids', ['missing-user']);
    }
}
