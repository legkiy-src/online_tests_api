<?php

declare(strict_types=1);

namespace App\Service\Auth\Dto;

readonly class RegisterUserDto
{
    public function __construct(
        private string $email,
        private string $password,
        private string $firstName,
        private string $lastName,
        private string $patronymic,
        private array $roles = ['ROLE_USER']
    )
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPatronymic(): string
    {
        return $this->patronymic;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
