<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\EnumStatusUsuario;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $usuario = Usuario::create(array_merge($request->validated(), [
            'status'         => EnumStatusUsuario::Ativo,
            'data_expiracao' => now()->addDays(7)->toDateString(),
        ]));

        $token = JWTAuth::fromUser($usuario);

        return response()->json(['token' => $token, 'usuario' => $usuario], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (! $token = auth('api')->attempt(['email' => $request->email, 'password' => $request->senha])) {
            return response()->json(['message' => 'Credenciais inválidas.'], 401);
        }

        return response()->json(['token' => $token, 'usuario' => auth('api')->user()]);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }
}
