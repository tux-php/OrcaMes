<?php

namespace Database\Factories;

use App\Models\ComprovanteIr;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComprovanteIr>
 */
class ComprovanteIrFactory extends Factory
{
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'ano_base' => 2025,
            'descricao' => 'Recibo',
            'valor' => '150.00',
            'arquivo' => 'comprovantes-ir/teste/recibo.pdf',
            'nome_original' => 'recibo.pdf',
        ];
    }
}
