<?php

namespace App\Domain\Notification\Enums;

enum Channel: string
{
    case SMS = 'sms';
    case Email = 'email';

    public function label(): string
    {
        return match ($this) {
            self::SMS => 'SMS',
            self::Email => 'Email',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
