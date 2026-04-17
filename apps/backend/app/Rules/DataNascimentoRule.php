<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DataNascimentoRule implements ValidationRule
{
    private const IDADE_MINIMA = 1;
    private const IDADE_MAXIMA = 120;

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $nascimento = \Carbon\Carbon::parse($value);

        if (!$nascimento->isValid()) {
            $fail("O campo {$attribute} não é uma data válida.");
            return;
        }

        $idade = $nascimento->age;

        if ($idade < self::IDADE_MINIMA) {
            $fail("O campo {$attribute} indica uma idade mínima de " . self::IDADE_MINIMA . " ano(s).");
            return;
        }

        if ($idade > self::IDADE_MAXIMA) {
            $fail("O campo {$attribute} indica uma idade máxima de " . self::IDADE_MAXIMA . " anos.");
        }
    }
}
