<?php

namespace Database\Factories;

use App\Domain\Notification\Enums\Channel;
use App\Domain\Notification\Enums\Priority;
use App\Domain\Notification\Enums\Status;
use App\Domain\Notification\Models\Notification;
use App\Domain\Subscriber\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{

    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscriber_id' => Subscriber::factory(),
            'channel' => fake()->randomElement(Channel::values()),
            'priority' => fake()->randomElement(Priority::values()),
            'status' => Status::Queued,
            'message' => fake()->sentence(),
            'idempotency_key' => fake()->uuid(),
            'attempts' => 0,
            'notification_batch_id' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn() => [
            'status' => Status::Sent,
            'sent_at' => now(),
            'provider_ref' => 'mock-ref',
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn() => [
            'status' => Status::Delivered,
            'sent_at' => now(),
            'delivered_at' => now(),
        ]);
    }
}
