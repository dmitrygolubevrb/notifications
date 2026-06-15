<?php

namespace App\Application\Exceptions;

use RuntimeException;

class IdempotencyConflictException extends RuntimeException {}
