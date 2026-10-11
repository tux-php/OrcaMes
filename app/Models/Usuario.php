<?php

namespace App\Models;

use App\Enums\Perfil;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['nome', 'sobrenome', 'email', 'password', 'perfil', 'ativo', 'deve_trocar_senha'])]
#[Hidden(['password', 'remember_token'])]
class Usuario extends Authenticatable
{
    /** @use HasFactory<UsuarioFactory> */
    use HasFactory;

    protected $table = 'usuarios';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'perfil' => Perfil::class,
            'ativo' => 'boolean',
            'deve_trocar_senha' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->perfil === Perfil::Admin;
    }

    public function contas(): HasMany
    {
        return $this->hasMany(Conta::class);
    }

    public function receitas(): HasMany
    {
        return $this->hasMany(Receita::class);
    }
}
