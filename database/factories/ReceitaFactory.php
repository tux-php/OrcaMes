<?php

namespace Database\Factories;

use App\Enums\TipoReceita;
use App\Models\Receita;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Receita>
 */
class ReceitaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'competencia' => '2026-10-01',
            'tipo' => TipoReceita::Salario,
            'descricao' => 'Salário',
            'valor' => '1000.00',
        ];
    }
}
