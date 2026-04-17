<?php

namespace App\OpenApi\Auth;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/auth/login',
    summary: 'Autenticar usuário',
    tags: ['Autenticação'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'senha'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@exemplo.com'),
                new OA\Property(property: 'senha', type: 'string', example: 'senha123'),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: 'Login realizado', content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')),
        new OA\Response(response: 401, description: 'Credenciais inválidas ou conta expirada'),
    ]
)]
class LoginDocs {}
