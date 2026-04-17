<?php

use App\Models\Usuario;

it('retorna os dados do usuário autenticado', function () {
    $usuario = Usuario::factory()->create();

    $this->actingAs($usuario, 'api')
        ->getJson('/api/auth/me')
        ->assertStatus(200)
        ->assertJsonFragment(['email' => $usuario->email, 'nome' => $usuario->nome]);
});

it('não retorna dados sem autenticação', function () {
    $this->getJson('/api/auth/me')
        ->assertStatus(401);
});

it('não expõe a senha no retorno', function () {
    $usuario = Usuario::factory()->create();

    $response = $this->actingAs($usuario, 'api')->getJson('/api/auth/me');

    expect($response->json())->not->toHaveKey('senha');
});
