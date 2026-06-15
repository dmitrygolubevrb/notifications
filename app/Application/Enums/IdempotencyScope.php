<?php

namespace App\Application\Enums;

enum IdempotencyScope: string
{
    case Bulk = 'bulk';
    case Single = 'single';
}
