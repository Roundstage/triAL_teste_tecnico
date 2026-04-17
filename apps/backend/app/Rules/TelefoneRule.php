<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TelefoneRule implements ValidationRule
{
    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (strlen($digits) === 13 && str_starts_with($digits, '55')) {
            $digits = substr($digits, 2);
        }

        $length = strlen($digits);

        if ($length < 10 || $length > 11) {
            $fail("O campo {$attribute} não é um telefone brasileiro válido.");
            return;
        }

        $ddd = (int) substr($digits, 0, 2);

        if ($ddd < 11 || $ddd > 99) {
            $fail("O campo {$attribute} contém um DDD inválido.");
        }
    }
}
