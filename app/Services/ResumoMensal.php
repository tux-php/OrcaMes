<?php

namespace App\Services;

use App\Models\Lancamento;
use App\Models\Receita;
use App\Support\Dinheiro;
use Carbon\CarbonInterface;

final class ResumoMensal
{
    /**
     * @param  array<int, int>  $donos
     * @return array{receitas: float, pago: float, aberto: float, total: float, sobra: float, sobra_prevista: float}
     */
    public static function calcular(array $donos, CarbonInterface $competencia): array
    {
        $receitas = Dinheiro::centavos(
            Receita::query()->dosUsuarios($donos)->daCompetencia($competencia)->sum('valor')
        );
        $pago = Dinheiro::centavos(
            Lancamento::query()->dosUsuarios($donos)->daCompetencia($competencia)->whereNotNull('pago_em')->sum('valor')
        );
        $aberto = Dinheiro::centavos(
            Lancamento::query()->dosUsuarios($donos)->daCompetencia($competencia)->whereNull('pago_em')->sum('valor')
        );

        return [
            'receitas' => $receitas / 100.0,
            'pago' => $pago / 100.0,
            'aberto' => $aberto / 100.0,
            'total' => ($pago + $aberto) / 100.0,
            'sobra' => ($receitas - $pago) / 100.0,
            'sobra_prevista' => ($receitas - $pago - $aberto) / 100.0,
        ];
    }
}
