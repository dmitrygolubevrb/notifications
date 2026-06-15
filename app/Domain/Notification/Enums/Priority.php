<?php

namespace App\Domain\Notification\Enums;

enum Priority: string
{
    case Transactional = 'transactional';
    case Normal = 'normal';
    case Marketing = 'marketing';

    public function label(): string
    {
        return match ($this) {
            self::Transactional => 'Transactional',
            self::Normal => 'Normal',
            self::Marketing => 'Marketing',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function queueName(): string
    {
        return match ($this) {
            self::Transactional => 'notifications.high',
            self::Normal => 'notifications',
            self::Marketing => 'notifications.low',
        };
    }

    public function queuePriority(): int
    {
        return match ($this) {
            self::Transactional => 10,
            self::Normal => 5,
            self::Marketing => 1,
        };
    }
}
