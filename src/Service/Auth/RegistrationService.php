<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Auth\Dto\RegisterUserDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class RegistrationService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {}

    public function register(RegisterUserDto $registrationData): User
    {
        if ($this->userRepository->findOneByEmail($registrationData->getEmail())) {
            throw new \LogicException('Пользователь с таким емейлом уже существует');
        }

        $user = new User(
            email: $registrationData->getEmail(),
            firstName: $registrationData->getFirstName(),
            lastName: $registrationData->getLastName(),
            patronymic: $registrationData->getPatronymic()
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, $registrationData->getPassword());
        $user->setPassword($hashedPassword);
        $user->setRoles($registrationData->getRoles());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
