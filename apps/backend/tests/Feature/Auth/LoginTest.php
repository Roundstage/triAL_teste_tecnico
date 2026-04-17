<?php

use App\Models\Usuario;

it('autentica com credenciais válidas', function () {
    Usuario::factory()->create(['email' => 'joao@example.com']);

    $this->postJson('/api/auth/login', ['email' => 'joao@example.com', 'senha' => 'password'])
        ->assertStatus(200)
        ->assertJsonStructure(['token', 'usuario']);
});

it('rejeita credenciais inválidas', function () {
    Usuario::factory()->create(['email' => 'joao@example.com']);

    $this->postJson('/api/auth/login', ['email' => 'joao@example.com', 'senha' => 'errada'])
        ->assertStatus(401)
        ->assertJson(['message' => 'Credenciais inválidas ou conta expirada.']);
});

it('rejeita email inexistente', function () {
    $this->postJson('/api/auth/login', ['email' => 'naoexiste@example.com', 'senha' => 'qualquer'])
        ->assertStatus(401);
});

it('não autentica sem campos obrigatórios', function () {
    $this->postJson('/api/auth/login', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'senha']);
});

it('rejeita login de usuário expirado', function () {
    Usuario::factory()->expirado()->create(['email' => 'expirado@example.com']);

    $this->postJson('/api/auth/login', ['email' => 'expirado@example.com', 'senha' => 'password'])
        ->assertStatus(401)
        ->assertJson(['message' => 'Credenciais inválidas ou conta expirada.']);
});

it('não expõe a senha no retorno do login', function () {
    Usuario::factory()->create(['email' => 'joao@example.com']);

    $response = $this->postJson('/api/auth/login', ['email' => 'joao@example.com', 'senha' => 'password']);

    expect($response->json('usuario'))->not->toHaveKey('senha');
});
