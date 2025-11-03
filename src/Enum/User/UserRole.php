<?php

declare(strict_types=1);

namespace App\Enum\User;

enum UserRole: string
{
    case STUDENT = 'ROLE_STUDENT';
    case TEACHER = 'ROLE_TEACHER';
    case ADMIN = 'ROLE_ADMIN';

    public function label(): string
    {
        return match($this) {
            self::STUDENT => 'Студент',
            self::TEACHER => 'Преподаватель',
            self::ADMIN => 'Администратор',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::STUDENT => 'Может проходить тесты и просматривать результаты',
            self::TEACHER => 'Может создавать тесты, проверять ответы и просматривать аналитику',
            self::ADMIN => 'Полный доступ ко всем функциям системы',
        };
    }

    public static function choices(): array
    {
        return [
            self::STUDENT->label() => self::STUDENT->value,
            self::TEACHER->label() => self::TEACHER->value,
            self::ADMIN->label() => self::ADMIN->value,
        ];
    }

    public function isStudent(): bool
    {
        return $this === self::STUDENT;
    }

    public function isTeacher(): bool
    {
        return $this === self::TEACHER || $this === self::ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }
}
