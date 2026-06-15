<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class NotificationCreatedResponse
{
    public static function json(array $payload): JsonResponse
    {
        return response()->json($payload, Response::HTTP_ACCEPTED);
    }
}
