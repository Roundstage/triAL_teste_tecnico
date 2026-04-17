<?php

namespace App\OpenApi\Auth;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/auth/me',
    summary: 'Dados do usuário autenticado',
    tags: ['Autenticação'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(response: 200, description: 'Usuário autenticado', content: new OA\JsonContent(ref: '#/components/schemas/Usuario')),
        new OA\Response(response: 401, description: 'Não autenticado'),
    ]
)]
class MeDocs {}
