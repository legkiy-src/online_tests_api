<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Auth\AuthService;
use App\Service\Auth\Dto\AuthResultDto;
use App\Service\Auth\Dto\LoginCredentialsDto;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthServiceTest extends MockeryTestCase
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->passwordHasher = Mockery::mock(UserPasswordHasherInterface::class);
        $this->jwtManager = Mockery::mock(JWTTokenManagerInterface::class);

        $this->authService = new AuthService(
            $this->userRepository,
            $this->passwordHasher,
            $this->jwtManager
        );

        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public static function authenticateProvider(): array
    {
        return [
            'validCredentials' => [
                [
                    'email' => 'test@example.com',
                    'password' => 'password123',
                    'userExists' => true,
                    'passwordValid' => true,
                    'expectedToken' => 'jwt.token.here',
                    'shouldAuthenticate' => true,
                ]
            ],
            'invalidEmail' => [
                [
                    'email' => 'nonexistent@example.com',
                    'password' => 'password123',
                    'userExists' => false,
                    'passwordValid' => false,
                    'expectedToken' => null,
                    'shouldAuthenticate' => false,
                ]
            ],
            'invalidPassword' => [
                [
                    'email' => 'test@example.com',
                    'password' => 'wrongpassword',
                    'userExists' => true,
                    'passwordValid' => false,
                    'expectedToken' => null,
                    'shouldAuthenticate' => false,
                ]
            ],
        ];
    }

    #[DataProvider('authenticateProvider')]
    public function testAuthenticate(array $data): void
    {
        $email = $data['email'];
        $password = $data['password'];
        $userExists = $data['userExists'];
        $passwordValid = $data['passwordValid'];
        $expectedToken = $data['expectedToken'];
        $shouldAuthenticate = $data['shouldAuthenticate'];

        $loginCredentials = new LoginCredentialsDto($email, $password);

        $userMock = $userExists ? Mockery::mock(User::class) : null;

        $this->userRepository
            ->shouldReceive('findOneByEmail')
            ->once()
            ->with($email)
            ->andReturn($userMock);

        if ($userExists) {
            $this->passwordHasher
                ->shouldReceive('isPasswordValid')
                ->once()
                ->with($userMock, $password)
                ->andReturn($passwordValid);
        } else {
            $this->passwordHasher
                ->shouldReceive('isPasswordValid')
                ->never();
        }

        if ($shouldAuthenticate) {
            $this->jwtManager
                ->shouldReceive('create')
                ->once()
                ->with($userMock)
                ->andReturn($expectedToken);
        } else {
            $this->jwtManager
                ->shouldReceive('create')
                ->never();
        }

        if (!$shouldAuthenticate) {
            $this->expectException(AuthenticationException::class);
            $this->expectExceptionMessage('Invalid credentials');
        }

        $result = $this->authService->authenticate($loginCredentials);

        if ($shouldAuthenticate) {
            $this->assertInstanceOf(AuthResultDto::class, $result);
            $this->assertEquals($expectedToken, $result->getToken());
            $this->assertEquals($userMock, $result->getUser());
            $this->assertEquals(3600, $result->getExpiresIn());
        }
    }

    public static function getUserDataProvider(): array
    {
        return [
            'fullUserData' => [
                [
                    'id' => 1,
                    'email' => 'test@example.com',
                    'roles' => ['ROLE_USER'],
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'patronymic' => 'Smith',
                    'expected' => [
                        'id' => 1,
                        'email' => 'test@example.com',
                        'roles' => ['ROLE_USER'],
                        'firstName' => 'John',
                        'lastName' => 'Doe',
                        'patronymic' => 'Smith',
                    ]
                ]
            ],
            'emptyPatronymic' => [
                [
                    'id' => 2,
                    'email' => 'test2@example.com',
                    'roles' => ['ROLE_ADMIN'],
                    'firstName' => 'Jane',
                    'lastName' => 'Smith',
                    'patronymic' => '',
                    'expected' => [
                        'id' => 2,
                        'email' => 'test2@example.com',
                        'roles' => ['ROLE_ADMIN'],
                        'firstName' => 'Jane',
                        'lastName' => 'Smith',
                        'patronymic' => '',
                    ]
                ]
            ],
            'emptyValues' => [
                [
                    'id' => 3,
                    'email' => 'test3@example.com',
                    'roles' => [],
                    'firstName' => '',
                    'lastName' => '',
                    'patronymic' => '',
                    'expected' => [
                        'id' => 3,
                        'email' => 'test3@example.com',
                        'roles' => [],
                        'firstName' => '',
                        'lastName' => '',
                        'patronymic' => '',
                    ]
                ]
            ],
            'multipleRoles' => [
                [
                    'id' => 4,
                    'email' => 'test4@example.com',
                    'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
                    'firstName' => 'Bob',
                    'lastName' => 'Johnson',
                    'patronymic' => 'William',
                    'expected' => [
                        'id' => 4,
                        'email' => 'test4@example.com',
                        'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
                        'firstName' => 'Bob',
                        'lastName' => 'Johnson',
                        'patronymic' => 'William',
                    ]
                ]
            ],
        ];
    }

    #[DataProvider('getUserDataProvider')]
    public function testGetCurrentUserData(array $data): void
    {
        $id = $data['id'];
        $email = $data['email'];
        $roles = $data['roles'];
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $patronymic = $data['patronymic'];
        $expected = $data['expected'];

        $user = Mockery::mock(User::class);

        $user->shouldReceive('getId')->once()->andReturn($id);
        $user->shouldReceive('getEmail')->once()->andReturn($email);
        $user->shouldReceive('getRoles')->once()->andReturn($roles);
        $user->shouldReceive('getFirstName')->once()->andReturn($firstName);
        $user->shouldReceive('getLastName')->once()->andReturn($lastName);
        $user->shouldReceive('getPatronymic')->once()->andReturn($patronymic);

        $result = $this->authService->getCurrentUserData($user);

        $this->assertEquals($expected, $result);
    }

    public function testAuthenticateWithMockerySpy(): void
    {
        $email = 'test@example.com';
        $password = 'password123';
        $token = 'jwt.token.here';

        $loginCredentials = new LoginCredentialsDto($email, $password);
        $user = Mockery::mock(User::class);

        $userRepositorySpy = Mockery::spy(UserRepository::class);
        $userRepositorySpy
            ->shouldReceive('findOneByEmail')
            ->andReturn($user);

        $passwordHasher = Mockery::mock(UserPasswordHasherInterface::class);
        $passwordHasher
            ->shouldReceive('isPasswordValid')
            ->andReturn(true);

        $jwtManager = Mockery::mock(JWTTokenManagerInterface::class);
        $jwtManager
            ->shouldReceive('create')
            ->andReturn($token);

        $authService = new AuthService($userRepositorySpy, $passwordHasher, $jwtManager);

        $result = $authService->authenticate($loginCredentials);

        $userRepositorySpy
            ->shouldHaveReceived('findOneByEmail')
            ->with($email)
            ->once();

        $this->assertInstanceOf(AuthResultDto::class, $result);
        $this->assertEquals($token, $result->getToken());
        $this->assertEquals($user, $result->getUser());
        $this->assertEquals(3600, $result->getExpiresIn());
    }

    public function testAuthenticateSpyWithNeverCalled(): void
    {
        $email = 'nonexistent@example.com';
        $password = 'password123';

        $loginCredentials = new LoginCredentialsDto($email, $password);

        $userRepositorySpy = Mockery::spy(UserRepository::class);
        $userRepositorySpy
            ->shouldReceive('findOneByEmail')
            ->andReturn(null);

        $passwordHasher = Mockery::mock(UserPasswordHasherInterface::class);
        $jwtManager = Mockery::mock(JWTTokenManagerInterface::class);
        $authService = new AuthService($userRepositorySpy, $passwordHasher, $jwtManager);
        $this->expectException(AuthenticationException::class);

        try {
            $authService->authenticate($loginCredentials);
        } catch (AuthenticationException $exception) {
            $passwordHasher->shouldNotHaveReceived('isPasswordValid');
            $jwtManager->shouldNotHaveReceived('create');

            throw $exception;
        }
    }
}
