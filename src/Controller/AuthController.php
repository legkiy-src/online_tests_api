<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Auth\AuthService;
use App\Service\Auth\Dto\LoginCredentialsDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    #[Route('/api/auth/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $credentials = new LoginCredentialsDto(
            email: $data['email'] ?? '',
            password: $data['password'] ?? ''
        );

        $authResult = $this->authService->authenticate($credentials);

        return $this->json([
            'token' => $authResult->getToken(),
            'user' => $this->authService->getCurrentUserData($authResult->getUser()),
            'expires_in' => $authResult->getExpiresIn()
        ]);
    }

    #[Route('/api/auth/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Not authenticated'], 401);
        }

        return $this->json([
            'user' => $this->authService->getCurrentUserData($user)
        ]);
    }
}
