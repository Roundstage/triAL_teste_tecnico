<?php

namespace Database\Factories;

use App\Enums\EnumStatusUsuario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    protected static ?string $hashedPassword = null;

    public function definition(): array
    {
        return [
            'nome'               => fake()->name(),
            'email'              => fake()->unique()->safeEmail(),
            'senha'              => static::$hashedPassword ??= Hash::make('password'),
            'telefone'           => fake()->numerify('119########'),
            'data_nascimento'    => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'status'             => EnumStatusUsuario::Ativo,
            'data_expiracao'     => now()->addDays(7)->toDateString(),
        ];
    }

    public function expirado(): static
    {
        return $this->state([
            'status' => EnumStatusUsuario::Expirado,
            'data_expiracao' => now()->subDay()->toDateString(),
        ]);
    }
}
