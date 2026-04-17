<?php

namespace App\Services;

use App\Enums\EnumStatusUsuario;
use App\Models\Usuario;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function registrar(array $dados): array
    {
        $usuario = Usuario::create(array_merge($dados, [
            'status' => EnumStatusUsuario::Ativo,
            'data_expiracao' => now()->addDays(7)->toDateString(),
        ]));

        $token = JWTAuth::fromUser($usuario);

        return ['token' => $token, 'usuario' => $usuario];
    }

    public function autenticar(string $email, string $senha): ?string
    {
        $token = auth('api')->attempt(['email' => $email, 'password' => $senha]);

        if (! $token) {
            return null;
        }

        if (auth('api')->user()->status === EnumStatusUsuario::Expirado) {
            auth('api')->logout();

            return null;
        }

        return $token;
    }

    public function encerrarSessao(): void
    {
        auth('api')->logout();
    }
}
