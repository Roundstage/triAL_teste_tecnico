<?php

use App\Models\Usuario;

it('encerra a sessão com sucesso', function () {
    $usuario = Usuario::factory()->create();
    $token = auth('api')->login($usuario);

    $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/auth/logout')
        ->assertStatus(200)
        ->assertJson(['message' => 'Sessão encerrada.']);
});

it('rejeita logout sem autenticação', function () {
    $this->postJson('/api/auth/logout')
        ->assertStatus(401);
});
