<?php

namespace Tests;

use App\Application\Contracts\IdempotencyStoreInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\ArrayIdempotencyStore;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->singleton(
            IdempotencyStoreInterface::class,
            fn () => new ArrayIdempotencyStore(),
        );
    }

    protected function authenticateApi(?User $user = null): User
    {
        $user ??= User::factory()->create();
        Sanctum::actingAs($user);
        return $user;
    }

    protected function withIdempotencyKey(string $key = 'test-idempotency-key'): static
    {
        return $this->withHeader('Idempotency-Key', $key);
    }
}
