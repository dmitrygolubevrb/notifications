<?php

namespace App\Application\Factories;

use App\Http\Resources\NotificationResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class NotificationQueryResponseFactory
{

    public function toPaginatedList(LengthAwarePaginator $paginator): array
    {
        return NotificationResource::collection($paginator)->response()->getData(true);
    }

}
