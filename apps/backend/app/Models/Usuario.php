<?php

namespace App\Models;

use App\Enums\EnumStatusUsuario;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[Fillable(['nome', 'email', 'senha', 'telefone', 'status', 'data_expiracao'])]
#[Hidden(['senha'])]
class Usuario extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UsuarioFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected function casts(): array
    {
        return [
            'data_expiracao' => 'date',
            'senha'          => 'hashed',
            'status'         => EnumStatusUsuario::class,
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /** @return array<string, mixed> */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
