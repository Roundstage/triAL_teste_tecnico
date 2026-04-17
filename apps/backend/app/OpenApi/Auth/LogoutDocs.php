<?php

namespace App\OpenApi\Auth;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/auth/logout',
    summary: 'Encerrar sessão',
    tags: ['Autenticação'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(response: 200, description: 'Sessão encerrada', content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string', example: 'Sessão encerrada.')])),
        new OA\Response(response: 401, description: 'Não autenticado'),
    ]
)]
class LogoutDocs {}
