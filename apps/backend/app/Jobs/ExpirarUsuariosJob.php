<?php

namespace App\Jobs;

use App\Services\ExpirarUsuariosService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ExpirarUsuariosJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function handle(ExpirarUsuariosService $service): void
    {
        $service->executar();
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[ExpirarUsuariosJob] Falha ao expirar usuários: '.$exception->getMessage());
    }
}
