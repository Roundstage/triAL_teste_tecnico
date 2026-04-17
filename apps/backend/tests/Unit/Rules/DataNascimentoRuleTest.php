<?php

use App\Rules\DataNascimentoRule;

function validarNascimento(string $valor): ?string
{
    $erro = null;
    (new DataNascimentoRule())->validate('data_nascimento', $valor, function (string $msg) use (&$erro) {
        $erro = $msg;
    });
    return $erro;
}

it('aceita data de nascimento válida', function () {
    expect(validarNascimento('1990-06-15'))->toBeNull();
});

it('aceita usuário com 18 anos', function () {
    expect(validarNascimento(now()->subYears(18)->toDateString()))->toBeNull();
});

it('aceita usuário com 1 ano', function () {
    expect(validarNascimento(now()->subYear()->subDay()->toDateString()))->toBeNull();
});

it('rejeita nascido hoje', function () {
    expect(validarNascimento(now()->toDateString()))->not->toBeNull();
});

it('rejeita data no futuro', function () {
    expect(validarNascimento(now()->addYear()->toDateString()))->not->toBeNull();
});

it('rejeita idade acima de 120 anos', function () {
    expect(validarNascimento(now()->subYears(121)->toDateString()))->not->toBeNull();
});
