<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        return response()->json(
            $this->authService->registrar($request->validated()),
            201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $token = $this->authService->autenticar($data['email'], $data['senha']);

        if (! $token) {
            return response()->json(['message' => 'Credenciais inválidas ou conta expirada.'], 401);
        }

        return response()->json(['token' => $token, 'usuario' => auth('api')->user()]);
    }

    public function logout(): JsonResponse
    {
        $this->authService->encerrarSessao();

        return response()->json(['message' => 'Sessão encerrada.']);
    }

    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }
}
