<?php

namespace App\Http\Requests\Api;

use App\Rules\TelefoneRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nome'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:usuarios'],
            'senha'              => ['required', 'string', 'min:6', 'confirmed'],
            'senha_confirmation' => ['required'],
            'telefone'           => ['required', 'string', new TelefoneRule],
        ];
    }
}
