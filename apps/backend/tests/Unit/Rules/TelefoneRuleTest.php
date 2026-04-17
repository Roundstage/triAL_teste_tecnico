<?php

use App\Rules\TelefoneRule;

function validarTelefone(string $valor): ?string
{
    $erro = null;
    (new TelefoneRule())->validate('telefone', $valor, function (string $msg) use (&$erro) {
        $erro = $msg;
    });
    return $erro;
}

it('aceita celular brasileiro sem formatação', function () {
    expect(validarTelefone('11987654321'))->toBeNull();
});

it('aceita fixo brasileiro sem formatação', function () {
    expect(validarTelefone('1133334444'))->toBeNull();
});

it('aceita número com DDI 55', function () {
    expect(validarTelefone('5511987654321'))->toBeNull();
});

it('aceita número com DDI +55', function () {
    expect(validarTelefone('+5511987654321'))->toBeNull();
});

it('aceita número com formatação (xx) xxxxx-xxxx', function () {
    expect(validarTelefone('(11) 98765-4321'))->toBeNull();
});

it('rejeita número muito curto', function () {
    expect(validarTelefone('11987'))->not->toBeNull();
});

it('rejeita número muito longo', function () {
    expect(validarTelefone('119876543210000'))->not->toBeNull();
});

it('rejeita DDD inválido', function () {
    expect(validarTelefone('00987654321'))->not->toBeNull();
});
