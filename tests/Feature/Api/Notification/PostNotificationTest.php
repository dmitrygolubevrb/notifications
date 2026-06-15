<?php

namespace Tests\Feature\Api\Notification;

use App\Domain\Notification\Enums\Status;
use App\Domain\Subscriber\Models\Subscriber;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PostNotificationTest extends TestCase
{

    public function test_creates_notification_and_returns_accepted(): void
    {
        Queue::fake();
        $this->authenticateApi();

        $subscriber = Subscriber::factory()->create([
            'external_id' => 'user-123',
        ]);

        $response = $this->withIdempotencyKey('key-single-1')
            ->postJson('/api/v1/notifications', [
                'subscriber_external_id' => $subscriber->external_id,
                'channel' => 'sms',
                'priority' => 'transactional',
                'message' => 'Hello',
            ]);

        $response->assertAccepted()
            ->assertJsonStructure([
                'notification' => [
                    'id',
                    'status',
                    'channel',
                    'message',
                    'priority',
                    'created_at',
                ]
            ])
            ->assertJsonPath('notification.status', Status::Queued->value);

        $this->assertDatabaseHas('notifications', [
            'subscriber_id' => $subscriber->id,
            'status' => Status::Queued->value,
            'message' => 'Hello'
        ]);

    }

}
