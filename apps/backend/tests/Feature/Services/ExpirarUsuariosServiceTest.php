<?php

use App\Enums\EnumStatusUsuario;
use App\Models\Usuario;
use App\Services\ExpirarUsuariosService;

it('expira usuários ativos com data_expiracao no passado', function () {
    $vencidos = Usuario::factory()->count(3)->create([
        'status'         => EnumStatusUsuario::Ativo,
        'data_expiracao' => now()->subDay()->toDateString(),
    ]);

    $quantidade = app(ExpirarUsuariosService::class)->executar();

    expect($quantidade)->toBe(3);

    foreach ($vencidos as $u) {
        expect($u->fresh()->status)->toBe(EnumStatusUsuario::Expirado);
    }
});

it('não expira usuários ativos com data_expiracao no futuro', function () {
    $ativo = Usuario::factory()->create([
        'status'         => EnumStatusUsuario::Ativo,
        'data_expiracao' => now()->addDays(3)->toDateString(),
    ]);

    app(ExpirarUsuariosService::class)->executar();

    expect($ativo->fresh()->status)->toBe(EnumStatusUsuario::Ativo);
});

it('não toca em usuários já expirados', function () {
    Usuario::factory()->expirado()->create();

    $quantidade = app(ExpirarUsuariosService::class)->executar();

    expect($quantidade)->toBe(0);
});

it('retorna zero quando não há usuários para expirar', function () {
    $quantidade = app(ExpirarUsuariosService::class)->executar();

    expect($quantidade)->toBe(0);
});

it('expira apenas os elegíveis quando há mistura de status', function () {
    Usuario::factory()->count(2)->create([
        'status'         => EnumStatusUsuario::Ativo,
        'data_expiracao' => now()->subDay()->toDateString(),
    ]);
    Usuario::factory()->count(2)->create([
        'status'         => EnumStatusUsuario::Ativo,
        'data_expiracao' => now()->addDay()->toDateString(),
    ]);

    $quantidade = app(ExpirarUsuariosService::class)->executar();

    expect($quantidade)->toBe(2);
});
