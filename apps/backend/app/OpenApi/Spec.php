<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'triAL API', version: '1.0.0', description: 'API de autenticação e gerenciamento de usuários')]
#[OA\Server(url: '/api')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer', bearerFormat: 'JWT')]
#[OA\Schema(
    schema: 'Usuario',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nome', type: 'string', example: 'João Silva'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@exemplo.com'),
        new OA\Property(property: 'telefone', type: 'string', example: '11999999999'),
        new OA\Property(property: 'data_nascimento', type: 'string', format: 'date', example: '1990-05-15'),
        new OA\Property(property: 'status', type: 'string', enum: ['ativo', 'expirado'], example: 'ativo'),
        new OA\Property(property: 'data_expiracao', type: 'string', format: 'date', example: '2026-04-24'),
    ]
)]
#[OA\Schema(
    schema: 'AuthResponse',
    properties: [
        new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
        new OA\Property(property: 'usuario', ref: '#/components/schemas/Usuario'),
    ]
)]
class Spec {}
