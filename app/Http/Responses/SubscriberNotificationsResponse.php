<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class SubscriberNotificationsResponse
{
    public static function json(array $payload): JsonResponse
    {
        return response()->json($payload, Response::HTTP_OK);
    }
}
