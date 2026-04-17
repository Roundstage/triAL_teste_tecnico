<?php

use App\Models\Usuario;

it('registra um novo usuário com sucesso', function () {
    $this->postJson('/api/auth/register', usuarioPayload())
        ->assertStatus(201)
        ->assertJsonStructure(['token', 'usuario' => ['id', 'nome', 'email', 'status', 'data_expiracao']]);

    expect(Usuario::where('email', 'joao@gmail.com')->exists())->toBeTrue();
});

it('cria usuário com status ativo e expiração em 7 dias', function () {
    $this->postJson('/api/auth/register', usuarioPayload());

    $usuario = Usuario::where('email', 'joao@gmail.com')->first();

    expect($usuario->status->value)->toBe('ativo')
        ->and($usuario->data_expiracao->toDateString())->toBe(now()->addDays(7)->toDateString());
});

it('não registra sem campos obrigatórios', function () {
    $this->postJson('/api/auth/register', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['nome', 'email', 'senha', 'telefone', 'data_nascimento']);
});

it('não registra com email duplicado', function () {
    Usuario::factory()->create(['email' => 'joao@gmail.com']);

    $this->postJson('/api/auth/register', usuarioPayload())
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('não registra com senha abaixo de 6 caracteres', function () {
    $this->postJson('/api/auth/register', usuarioPayload(['senha' => '123', 'senha_confirmation' => '123']))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['senha']);
});

it('não registra com senhas que não conferem', function () {
    $this->postJson('/api/auth/register', usuarioPayload(['senha_confirmation' => 'diferente']))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['senha']);
});

it('não registra com telefone inválido', function () {
    $this->postJson('/api/auth/register', usuarioPayload(['telefone' => '123']))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['telefone']);
});

it('não registra com data de nascimento futura', function () {
    $this->postJson('/api/auth/register', usuarioPayload(['data_nascimento' => now()->addYear()->toDateString()]))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data_nascimento']);
});

it('não registra com data de nascimento antiga demais', function () {
    $this->postJson('/api/auth/register', usuarioPayload(['data_nascimento' => '1800-01-01']))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data_nascimento']);
});

it('não expõe a senha no retorno', function () {
    $response = $this->postJson('/api/auth/register', usuarioPayload());

    expect($response->json('usuario'))->not->toHaveKey('senha');
});
