<?php

namespace Database\Factories;

use App\Domain\Subscriber\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{

    protected $model = Subscriber::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_id' => fake()->uuid(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
        ];
    }
}
