<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Domain\Auth\DTO\LoginUserDTO;
use App\Domain\Auth\DTO\RegisterUserDTO;
use App\Domain\Auth\Services\AuthService;
use App\Exceptions\BusinessException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * @param LoginUserDTO $dto
     * @return JsonResponse
     * @throws BusinessException
     */
    public function login(LoginUserDTO $dto): JsonResponse
    {
        $userData = $this->authService->login($dto);

        return responder()->ok($userData->toArray());
    }

    /**
     * @param RegisterUserDTO $dto
     * @return JsonResponse
     * @throws BusinessException
     */
    public function register(RegisterUserDTO $dto): JsonResponse
    {
        $userData = $this->authService->register($dto);

        return responder()->ok($userData->toArray());
    }
}
