<?php

namespace App\Http\Middleware;

use App\Enums\EnumStatusUsuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUsuarioAtivo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('api')->user()->status === EnumStatusUsuario::Expirado) {
            return response()->json(['message' => 'Conta expirada.'], 403);
        }

        return $next($request);
    }
}
