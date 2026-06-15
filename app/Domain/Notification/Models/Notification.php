<?php

namespace App\Domain\Notification\Models;

use App\Domain\Notification\Enums\Channel;
use App\Domain\Notification\Enums\Priority;
use App\Domain\Notification\Enums\Status;
use App\Domain\Subscriber\Models\Subscriber;
use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{

    use HasFactory;

    protected $fillable = [
        'subscriber_id',
        'channel',
        'priority',
        'status',
        'message',
        'idempotency_key',
        'provider_ref',
        'error_message',
        'attempts',
        'sent_at',
        'delivered_at',
        'failed_at',
        'notification_batch_id',
    ];

    protected $casts = [
        'channel' => Channel::class,
        'priority' => Priority::class,
        'status' => Status::class,
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at'    => 'datetime',
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function notificationBatch(): BelongsTo
    {
        return $this->belongsTo(NotificationBatch::class);
    }

    public function markAsSent(string $providerRef): bool
    {
        return $this->update([
            'status' => Status::Sent,
            'provider_ref' => $providerRef,
            'sent_at' => now(),
        ]);
    }

    public function markAsDelivered(): bool
    {
        return $this->update([
            'status' => Status::Delivered,
            'delivered_at' => now()
        ]);
    }

    public function markAsFailed(string $errorMessage): bool
    {
        return $this->update([
            'status' => Status::Failed,
            'error_message' => $errorMessage,
            'failed_at' => now(),
        ]);
    }

    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }
}
