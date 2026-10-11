<?php

namespace Database\Factories;

use App\Models\Conta;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conta>
 */
class ContaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'nome' => fake()->words(2, true),
            'recorrente' => true,
            'dedutivel_ir' => false,
            'ativa' => true,
        ];
    }
}
