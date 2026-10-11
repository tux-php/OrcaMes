<?php

namespace Database\Factories;

use App\Models\Conta;
use App\Models\Lancamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lancamento>
 */
class LancamentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conta_id' => Conta::factory(),
            'competencia' => '2026-10-01',
            'valor' => '100.00',
            'lancado_em' => '2026-10-01',
            'pago_em' => null,
        ];
    }

    public function pago(string $em = '2026-10-05'): static
    {
        return $this->state(['pago_em' => $em]);
    }
}
