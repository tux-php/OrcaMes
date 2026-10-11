<?php

namespace Database\Factories;

use App\Enums\Perfil;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->firstName(),
            'sobrenome' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'senha-de-teste',
            'perfil' => Perfil::Membro,
            'ativo' => true,
            'deve_trocar_senha' => false,
        ];
    }

    public function admin(): static
    {
        return $this->state(['perfil' => Perfil::Admin]);
    }
}
