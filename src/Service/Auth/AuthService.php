<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Auth\Dto\LoginCredentialsDto;
use App\Service\Auth\Dto\AuthResultDto;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    public function authenticate(LoginCredentialsDto $loginCredentialsDto): AuthResultDto
    {
        $user = $this->userRepository->findOneByEmail($loginCredentialsDto->getEmail());

        if (empty($user) || false === $this->passwordHasher->isPasswordValid($user, $loginCredentialsDto->getPassword())) {
            throw new AuthenticationException('Invalid credentials', 401);
        }

        $token = $this->jwtManager->create($user);

        return new AuthResultDto(
            token: $token,
            user: $user,
            expiresIn: 3600 // или получать из конфигурации
        );
    }

    public function getCurrentUserData(UserInterface  $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'patronymic' => $user->getPatronymic(),
        ];
    }
}
