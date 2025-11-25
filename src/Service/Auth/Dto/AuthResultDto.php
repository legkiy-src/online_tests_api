<?php

declare(strict_types=1);

namespace App\Service\Auth\Dto;

use App\Entity\User;

readonly class AuthResultDto
{
    public function __construct(
        private string $token,
        private User $user,
        private int $expiresIn
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }
}
