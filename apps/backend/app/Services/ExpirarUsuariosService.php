<?php

namespace App\Services;

use App\Enums\EnumStatusUsuario;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

class ExpirarUsuariosService
{
    public function executar(): int
    {
        $quantidade = Usuario::query()
            ->where('status', EnumStatusUsuario::Ativo)
            ->whereDate('data_expiracao', '<', now()->toDateString())
            ->update(['status' => EnumStatusUsuario::Expirado]);

        Log::info(sprintf(
            '[%s] ExpirarUsuariosJob: %d usuários expirados',
            now()->toDateString(),
            $quantidade
        ));

        return $quantidade;
    }
}
