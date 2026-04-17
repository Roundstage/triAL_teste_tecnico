<?php

namespace App\OpenApi\Auth;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/auth/register',
    summary: 'Registrar novo usuário',
    tags: ['Autenticação'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['nome', 'email', 'senha', 'senha_confirmation', 'telefone', 'data_nascimento'],
            properties: [
                new OA\Property(property: 'nome', type: 'string', example: 'João Silva'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@exemplo.com'),
                new OA\Property(property: 'senha', type: 'string', minLength: 6, example: 'senha123'),
                new OA\Property(property: 'senha_confirmation', type: 'string', example: 'senha123'),
                new OA\Property(property: 'telefone', type: 'string', example: '11999999999'),
                new OA\Property(property: 'data_nascimento', type: 'string', format: 'date', example: '1990-05-15'),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Usuário criado', content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')),
        new OA\Response(response: 422, description: 'Dados inválidos'),
    ]
)]
class RegisterDocs {}
