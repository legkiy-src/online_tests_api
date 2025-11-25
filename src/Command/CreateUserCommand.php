<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'create-user',
    description: 'Создание пользователя',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('firstName', InputArgument::REQUIRED, 'User first name')
            ->addArgument('lastName', InputArgument::REQUIRED, 'User last name')
            ->addArgument('patronymic', InputArgument::REQUIRED, 'User patronymic')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument('roles', InputArgument::IS_ARRAY, 'User roles', ['ROLE_USER']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $patronymic = $input->getArgument('patronymic');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');

        // Проверяем, нет ли уже пользователя с таким email
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($existingUser) {
            $io->error('Пользователь с таким емейлом почты уже существует!');
            return Command::FAILURE;
        }

        // Создаем нового пользователя
        $user = new User(
            email: $email,
            firstName: $firstName,
            lastName: $lastName,
            patronymic: $patronymic
        );

        $user->setRoles($roles);

        // Хешируем пароль
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success("User {$email} created successfully!");
        $io->table(
            ['Email', 'Roles', 'Password'],
            [[$email, implode(', ', $roles), $password]]
        );

        return Command::SUCCESS;
    }
}
