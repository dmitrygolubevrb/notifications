<?php

use App\Http\Controllers\Api\Notification\IndexBySubscriber;
use App\Http\Controllers\Api\Notification\StoreBulkController;
use App\Http\Controllers\Api\Notification\StoreController;
use App\Http\Controllers\Api\Subscriber\StoreController as StoreSubscriberController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/subscribers', StoreSubscriberController::class);
    Route::get('/subscribers/{subscriberExternalId}/notifications', IndexBySubscriber::class);
    Route::post('/notifications', StoreController::class);
    Route::post('/notifications/bulk', StoreBulkController::class);
});
