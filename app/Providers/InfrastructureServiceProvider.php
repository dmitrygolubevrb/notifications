<?php

namespace App\Providers;

use App\Application\Contracts\IdempotencyServiceInterface;
use App\Application\Contracts\IdempotencyStoreInterface;
use App\Application\Contracts\NotificationDispatchServiceInterface;
use App\Application\Contracts\NotificationQueryServiceInterface;
use App\Application\Services\IdempotencyService;
use App\Application\Services\NotificationDispatchService;
use App\Application\Services\NotificationQueryService;
use App\Domain\Notification\Contracts\NotificationBatchRepositoryInterface;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Subscriber\Contracts\SubscriberRepositoryInterface;
use App\Infrastructure\Idempotency\RedisIdempotencyStore;
use App\Infrastructure\Providers\EmailProviderMock;
use App\Infrastructure\Providers\NotificationProviderResolver;
use App\Infrastructure\Providers\SendGridEmailProvider;
use App\Infrastructure\Providers\SmsProviderMock;
use App\Infrastructure\Providers\TwilioSmsProvider;
use App\Infrastructure\Repositories\NotificationBatchRepository;
use App\Infrastructure\Repositories\NotificationRepository;
use App\Infrastructure\Repositories\SubscriberRepository;
use Illuminate\Support\ServiceProvider;

class InfrastructureServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(
            NotificationBatchRepositoryInterface::class,
            NotificationBatchRepository::class
        );
        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );
        $this->app->bind(
            SubscriberRepositoryInterface::class,
            SubscriberRepository::class
        );

        // Stores
        $this->app->bind(
            IdempotencyStoreInterface::class,
            RedisIdempotencyStore::class
        );

        //Services
        $this->app->bind(
            IdempotencyServiceInterface::class,
            IdempotencyService::class
        );
        $this->app->bind(
            NotificationDispatchServiceInterface::class,
            NotificationDispatchService::class
        );
        $this->app->bind(
            NotificationQueryServiceInterface::class,
            NotificationQueryService::class
        );

        //providers
        $this->app->singleton(EmailProviderMock::class);
        $this->app->singleton(SendGridEmailProvider::class);
        $this->app->singleton(SmsProviderMock::class);
        $this->app->singleton(TwilioSmsProvider::class);
        $this->app->singleton(NotificationProviderResolver::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
