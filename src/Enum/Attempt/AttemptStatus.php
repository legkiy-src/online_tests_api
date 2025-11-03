<?php

declare(strict_types=1);

namespace App\Enum\Attempt;

enum AttemptStatus: string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::IN_PROGRESS => 'В процессе',
            self::COMPLETED => 'Завершено',
            self::CANCELLED => 'Отменено',
            self::EXPIRED => 'Время истекло',
        };
    }

    public function isActive(): bool
    {
        return $this === self::IN_PROGRESS;
    }

    public function isFinished(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED, self::EXPIRED]);
    }

    public static function choices(): array
    {
        return [
            'В процессе' => self::IN_PROGRESS->value,
            'Завершено' => self::COMPLETED->value,
            'Отменено' => self::CANCELLED->value,
            'Время истекло' => self::EXPIRED->value,
        ];
    }
}
