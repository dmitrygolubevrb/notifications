<?php

namespace Tests\Feature\Api\Notification;

use App\Domain\Notification\Enums\Status;
use App\Domain\Notification\Models\Notification;
use App\Domain\Subscriber\Models\Subscriber;
use Tests\TestCase;

class ListNotificationsTest extends TestCase
{
    public function test_lists_notifications_with_pagination(): void
    {
        $this->authenticateApi();

        $subscriber = Subscriber::factory()->create(['external_id' => 'list-user']);

        Notification::factory()
            ->for($subscriber)
            ->count(3)
            ->create(['status' => Status::Queued]);

        Notification::factory()
            ->for($subscriber)
            ->sent()
            ->create();

        $response = $this->getJson('/api/v1/subscribers/list-user/notifications');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'status', 'channel', 'message', 'priority', 'created_at'],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(4, 'data');
    }

    public function test_filters_notifications_by_status(): void
    {
        $this->authenticateApi();

        $subscriber = Subscriber::factory()->create(['external_id' => 'filter-user']);

        Notification::factory()
            ->for($subscriber)
            ->count(2)
            ->create(['status' => Status::Queued]);

        Notification::factory()
            ->for($subscriber)
            ->sent()
            ->create();

        $response = $this->getJson('/api/v1/subscribers/filter-user/notifications?status=queued');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.status', Status::Queued->value)
            ->assertJsonPath('data.1.status', Status::Queued->value);
    }
}
