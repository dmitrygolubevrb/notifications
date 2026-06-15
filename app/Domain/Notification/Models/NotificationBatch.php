<?php

namespace App\Domain\Notification\Models;

use App\Domain\Notification\Enums\Channel;
use App\Domain\Notification\Enums\Priority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationBatch extends Model
{
    protected $fillable = ['channel', 'priority', 'message', 'total_count', 'idempotency_key'];

    protected $casts = [
        'channel' => Channel::class,
        'priority' => Priority::class,
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
