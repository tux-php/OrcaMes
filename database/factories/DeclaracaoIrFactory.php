<?php

namespace Database\Factories;

use App\Enums\ResultadoIr;
use App\Models\DeclaracaoIr;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeclaracaoIr>
 */
class DeclaracaoIrFactory extends Factory
{
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'ano_base' => 2025,
            'resultado' => ResultadoIr::ARestituir,
            'valor' => '1000.00',
        ];
    }
}
