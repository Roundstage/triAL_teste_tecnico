<?php

namespace App\Jobs;

use App\Services\ExpirarUsuariosService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpirarUsuariosJob implements ShouldQueue
{
    use Queueable;

    public function handle(ExpirarUsuariosService $service): void
    {
        $service->executar();
    }
}
