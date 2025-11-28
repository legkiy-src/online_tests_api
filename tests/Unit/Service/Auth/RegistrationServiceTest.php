<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Auth\Dto\RegisterUserDto;
use App\Service\Auth\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationServiceTest extends MockeryTestCase
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private RegistrationService $registrationService;

    protected function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->passwordHasher = Mockery::mock(UserPasswordHasherInterface::class);
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);

        $this->registrationService = new RegistrationService(
            $this->userRepository,
            $this->passwordHasher,
            $this->entityManager
        );

        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public static function registerProvider(): array
    {
        return [
            'successfulRegistration' => [
                [
                    'email' => 'test@example.com',
                    'password' => 'password123',
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'patronymic' => 'Smith',
                    'roles' => ['ROLE_USER'],
                    'userExists' => false,
                    'shouldRegister' => true,
                ]
            ],
            'duplicateEmail' => [
                [
                    'email' => 'existing@example.com',
                    'password' => 'password123',
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'patronymic' => 'Smith',
                    'roles' => ['ROLE_USER'],
                    'userExists' => true,
                    'shouldRegister' => false,
                ]
            ],
            'userWithoutPatronymic' => [
                [
                    'email' => 'jane@example.com',
                    'password' => 'securepassword',
                    'firstName' => 'Jane',
                    'lastName' => 'Smith',
                    'patronymic' => '',
                    'roles' => ['ROLE_USER'],
                    'userExists' => false,
                    'shouldRegister' => true,
                ]
            ],
            'userWithEmptyPatronymic' => [
                [
                    'email' => 'bob@example.com',
                    'password' => 'userpass',
                    'firstName' => 'Bob',
                    'lastName' => 'Johnson',
                    'patronymic' => '',
                    'roles' => ['ROLE_USER'],
                    'userExists' => false,
                    'shouldRegister' => true,
                ]
            ],
            'adminUser' => [
                [
                    'email' => 'admin@example.com',
                    'password' => 'adminpass',
                    'firstName' => 'Admin',
                    'lastName' => 'User',
                    'patronymic' => 'System',
                    'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
                    'userExists' => false,
                    'shouldRegister' => true,
                ]
            ],
        ];
    }

    #[DataProvider('registerProvider')]
    public function testRegister(array $data): void
    {
        $email = $data['email'];
        $password = $data['password'];
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $patronymic = $data['patronymic'];
        $roles = $data['roles'];
        $userExists = $data['userExists'];
        $shouldRegister = $data['shouldRegister'];

        $registrationData = new RegisterUserDto(
            email: $email,
            password: $password,
            firstName: $firstName,
            lastName: $lastName,
            patronymic: $patronymic,
            roles: $roles
        );

        $existingUser = $userExists ? Mockery::mock(User::class) : null;
        $hashedPassword = 'hashed_' . $password;

        $this->userRepository
            ->shouldReceive('findOneByEmail')
            ->once()
            ->with($email)
            ->andReturn($existingUser);

        if (!$shouldRegister) {
            $this->passwordHasher
                ->shouldReceive('hashPassword')
                ->never();

            $this->entityManager
                ->shouldReceive('persist')
                ->never();

            $this->entityManager
                ->shouldReceive('flush')
                ->never();

            $this->expectException(\LogicException::class);
            $this->expectExceptionMessage('Пользователь с таким емейлом уже существует');

            $this->registrationService->register($registrationData);
        } else {
            $this->passwordHasher
                ->shouldReceive('hashPassword')
                ->once()
                ->with(Mockery::type(User::class), $password)
                ->andReturn($hashedPassword);

            $this->entityManager
                ->shouldReceive('persist')
                ->once()
                ->with(Mockery::type(User::class));

            $this->entityManager
                ->shouldReceive('flush')
                ->once();

            $result = $this->registrationService->register($registrationData);

            $this->assertInstanceOf(User::class, $result);
            $this->assertEquals($email, $result->getEmail());
            $this->assertEquals($firstName, $result->getFirstName());
            $this->assertEquals($lastName, $result->getLastName());
            $this->assertEquals($patronymic, $result->getPatronymic());
            $this->assertEquals($roles, $result->getRoles());
            $this->assertEquals($hashedPassword, $result->getPassword());
        }
    }

    public static function getUserDataProvider(): array
    {
        return [
            'fullUserData' => [
                [
                    'email' => 'test@example.com',
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'patronymic' => 'Smith',
                    'roles' => ['ROLE_USER'],
                    'expected' => [
                        'email' => 'test@example.com',
                        'firstName' => 'John',
                        'lastName' => 'Doe',
                        'patronymic' => 'Smith',
                        'roles' => ['ROLE_USER'],
                    ]
                ]
            ],
            'emptyPatronymic' => [
                [
                    'email' => 'test2@example.com',
                    'firstName' => 'Jane',
                    'lastName' => 'Smith',
                    'patronymic' => '',
                    'roles' => ['ROLE_ADMIN'],
                    'expected' => [
                        'email' => 'test2@example.com',
                        'firstName' => 'Jane',
                        'lastName' => 'Smith',
                        'patronymic' => '',
                        'roles' => ['ROLE_ADMIN'],
                    ]
                ]
            ],
            'emptyStringPatronymic' => [
                [
                    'email' => 'test3@example.com',
                    'firstName' => 'Bob',
                    'lastName' => 'Johnson',
                    'patronymic' => '',
                    'roles' => ['ROLE_USER'],
                    'expected' => [
                        'email' => 'test3@example.com',
                        'firstName' => 'Bob',
                        'lastName' => 'Johnson',
                        'patronymic' => '',
                        'roles' => ['ROLE_USER'],
                    ]
                ]
            ],
            'multipleRoles' => [
                [
                    'email' => 'test4@example.com',
                    'firstName' => 'Alice',
                    'lastName' => 'Brown',
                    'patronymic' => 'Marie',
                    'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
                    'expected' => [
                        'email' => 'test4@example.com',
                        'firstName' => 'Alice',
                        'lastName' => 'Brown',
                        'patronymic' => 'Marie',
                        'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
                    ]
                ]
            ],
        ];
    }

    #[DataProvider('getUserDataProvider')]
    public function testUserCreation(array $data): void
    {
        $email = $data['email'];
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $patronymic = $data['patronymic'];
        $roles = $data['roles'];
        $expected = $data['expected'];

        $registrationData = new RegisterUserDto(
            email: $email,
            password: 'password123',
            firstName: $firstName,
            lastName: $lastName,
            patronymic: $patronymic,
            roles: $roles
        );

        $this->userRepository
            ->shouldReceive('findOneByEmail')
            ->once()
            ->with($email)
            ->andReturn(null);

        $this->passwordHasher
            ->shouldReceive('hashPassword')
            ->once()
            ->with(Mockery::type(User::class), 'password123')
            ->andReturn('hashed_password');

        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with(Mockery::on(function (User $user) use ($expected) {
                $this->assertEquals($expected['email'], $user->getEmail());
                $this->assertEquals($expected['firstName'], $user->getFirstName());
                $this->assertEquals($expected['lastName'], $user->getLastName());
                $this->assertEquals($expected['patronymic'], $user->getPatronymic());
                $this->assertEquals($expected['roles'], $user->getRoles());
                return true;
            }));

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $result = $this->registrationService->register($registrationData);

        $this->assertInstanceOf(User::class, $result);
    }

    public function testRegisterWithMockerySpy(): void
    {
        $email = 'test@example.com';
        $password = 'password123';
        $firstName = 'John';
        $lastName = 'Doe';
        $patronymic = 'Smith';
        $roles = ['ROLE_USER'];

        $registrationData = new RegisterUserDto(
            email: $email,
            password: $password,
            firstName: $firstName,
            lastName: $lastName,
            patronymic: $patronymic,
            roles: $roles
        );

        $userRepository = Mockery::mock(UserRepository::class);
        $userRepository
            ->shouldReceive('findOneByEmail')
            ->andReturn(null);

        $passwordHasher = Mockery::mock(UserPasswordHasherInterface::class);
        $passwordHasher
            ->shouldReceive('hashPassword')
            ->andReturn('hashed_password');

        $entityManagerSpy = Mockery::spy(EntityManagerInterface::class);

        $registrationService = new RegistrationService(
            $userRepository,
            $passwordHasher,
            $entityManagerSpy
        );

        $result = $registrationService->register($registrationData);

        $entityManagerSpy
            ->shouldHaveReceived()
            ->persist(Mockery::type(User::class))
            ->once();

        $entityManagerSpy
            ->shouldHaveReceived()
            ->flush()
            ->once();

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($email, $result->getEmail());
        $this->assertEquals($firstName, $result->getFirstName());
        $this->assertEquals($lastName, $result->getLastName());
        $this->assertEquals($patronymic, $result->getPatronymic());
        $this->assertEquals($roles, $result->getRoles());
    }

    public function testRegisterSpyWithNeverCalled(): void
    {
        $email = 'existing@example.com';
        $password = 'password123';
        $firstName = 'John';
        $lastName = 'Doe';
        $patronymic = 'Smith';
        $roles = ['ROLE_USER'];

        $registrationData = new RegisterUserDto(
            email: $email,
            password: $password,
            firstName: $firstName,
            lastName: $lastName,
            patronymic: $patronymic,
            roles: $roles
        );

        $userRepository = Mockery::mock(UserRepository::class);
        $userRepository
            ->shouldReceive('findOneByEmail')
            ->andReturn(Mockery::mock(User::class));

        $passwordHasherSpy = Mockery::spy(UserPasswordHasherInterface::class);
        $entityManagerSpy = Mockery::spy(EntityManagerInterface::class);

        $registrationService = new RegistrationService(
            $userRepository,
            $passwordHasherSpy,
            $entityManagerSpy
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Пользователь с таким емейлом уже существует');

        try {
            $registrationService->register($registrationData);
        } catch (\LogicException $exception) {
            $passwordHasherSpy->shouldNotHaveReceived('hashPassword');
            $entityManagerSpy->shouldNotHaveReceived('persist');
            $entityManagerSpy->shouldNotHaveReceived('flush');

            throw $exception;
        }
    }

    public function testRegisterCallsInCorrectOrder(): void
    {
        $registrationData = new RegisterUserDto(
            email: 'test@example.com',
            password: 'password123',
            firstName: 'John',
            lastName: 'Doe',
            patronymic: 'Smith',
            roles: ['ROLE_USER']
        );

        $this->userRepository
            ->shouldReceive('findOneByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn(null);

        $this->passwordHasher
            ->shouldReceive('hashPassword')
            ->once()
            ->andReturn('hashed_password');

        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with(Mockery::type(User::class))
            ->ordered();

        $this->entityManager
            ->shouldReceive('flush')
            ->once()
            ->ordered();

        $result = $this->registrationService->register($registrationData);

        $this->assertInstanceOf(User::class, $result);
    }
}
