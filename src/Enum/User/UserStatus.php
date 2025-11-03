<?php

declare(strict_types=1);

namespace App\Enum\User;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Активный',
            self::INACTIVE => 'Неактивный',
            self::SUSPENDED => 'Заблокирован',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::ACTIVE => 'Пользователь может использовать систему',
            self::INACTIVE => 'Пользователь не может входить в систему',
            self::SUSPENDED => 'Пользователь временно заблокирован',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'secondary',
            self::SUSPENDED => 'danger',
        };
    }

    public static function choices(): array
    {
        return [
            self::ACTIVE->label() => self::ACTIVE->value,
            self::INACTIVE->label() => self::INACTIVE->value,
            self::SUSPENDED->label() => self::SUSPENDED->value,
        ];
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canLogin(): bool
    {
        return $this === self::ACTIVE;
    }
}
