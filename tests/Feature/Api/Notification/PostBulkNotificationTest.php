<?php

namespace Tests\Feature\Api\Notification;

use App\Domain\Notification\Enums\Status;
use App\Domain\Subscriber\Models\Subscriber;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PostBulkNotificationTest extends TestCase
{

    public function test_creates_batch_and_returns_accepted(): void
    {
        Queue::fake();
        $this->authenticateApi();
        $subscribers = Subscriber::factory()->count(2)->create();
        $externalIds = $subscribers->pluck('external_id')->all();
        $response = $this
            ->withIdempotencyKey('key-bulk-1')
            ->postJson('/api/v1/notifications/bulk', [
                'subscriber_external_ids' => $externalIds,
                'channel' => 'email',
                'priority' => 'marketing',
                'message' => 'Bulk hello',
            ]);
        $response
            ->assertAccepted()
            ->assertJsonStructure([
                'batch_id',
                'channel',
                'priority',
                'message',
                'total_count',
                'notifications' => [
                    ['id', 'status', 'channel', 'message', 'priority', 'created_at'],
                ],
            ])
            ->assertJsonPath('total_count', 2)
            ->assertJsonPath('channel', 'email')
            ->assertJsonCount(2, 'notifications');
        $batchId = $response->json('batch_id');
        $this->assertDatabaseHas('notification_batches', [
            'id' => $batchId,
            'total_count' => 2,
            'message' => 'Bulk hello',
        ]);
        $this->assertDatabaseCount('notifications', 2);
        $this->assertDatabaseHas('notifications', [
            'notification_batch_id' => $batchId,
            'status' => Status::Queued->value,
            'message' => 'Bulk hello',
        ]);
    }

}
