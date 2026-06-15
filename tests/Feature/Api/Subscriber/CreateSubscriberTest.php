<?php

namespace Tests\Feature\Api\Subscriber;

use App\Domain\Subscriber\Models\Subscriber;
use Tests\TestCase;

class CreateSubscriberTest extends TestCase
{
    public function test_creates_subscriber_and_returns_created(): void
    {
        $this->authenticateApi();

        $response = $this->postJson('/api/v1/subscribers', [
            'external_id' => 'user-demo',
            'phone' => '+79001234567',
            'email' => 'demo@example.com',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'subscriber' => ['id', 'external_id', 'phone', 'email', 'created_at'],
            ])
            ->assertJsonPath('subscriber.external_id', 'user-demo');

        $this->assertDatabaseHas('subscribers', [
            'external_id' => 'user-demo',
            'phone' => '+79001234567',
            'email' => 'demo@example.com',
        ]);
    }

    public function test_returns_validation_error_when_external_id_already_exists(): void
    {
        $this->authenticateApi();

        Subscriber::factory()->create(['external_id' => 'user-demo']);

        $response = $this->postJson('/api/v1/subscribers', [
            'external_id' => 'user-demo',
            'phone' => '+79001234567',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['external_id']);

        $this->assertDatabaseCount('subscribers', 1);
    }
}
