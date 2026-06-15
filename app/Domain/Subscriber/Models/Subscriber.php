<?php

namespace App\Domain\Subscriber\Models;

use App\Domain\Notification\Models\Notification;
use Database\Factories\SubscriberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{

    use HasFactory;

    protected $fillable = ['external_id', 'phone', 'email'];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    protected static function newFactory(): SubscriberFactory
    {
        return SubscriberFactory::new();
    }
}
