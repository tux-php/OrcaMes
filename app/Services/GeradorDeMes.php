<?php

namespace App\Services;

use App\Enums\TipoReceita;
use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Receita;
use App\Models\Usuario;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class GeradorDeMes
{
    /**
     * Cria os lançamentos das contas recorrentes e o salário do mês, repetindo
     * os últimos valores conhecidos. Pode ser executado de novo: só cria o que falta.
     *
     * @return array{lancamentos: int, receitas: int}
     */
    public static function gerar(Usuario $dono, CarbonInterface $competencia): array
    {
        $dia = $competencia->toDateString();

        return DB::transaction(function () use ($dono, $competencia, $dia) {
            $criados = ['lancamentos' => 0, 'receitas' => 0];

            $contas = Conta::query()
                ->where('usuario_id', $dono->id)
                ->where('ativa', true)
                ->where('recorrente', true)
                ->whereDoesntHave('lancamentos', fn ($q) => $q->daCompetencia($competencia))
                ->get();

            foreach ($contas as $conta) {
                $anterior = $conta->lancamentos()
                    ->whereDate('competencia', '<', $dia)
                    ->orderByDesc('competencia')
                    ->orderByDesc('id')
                    ->first();

                Lancamento::create([
                    'conta_id' => $conta->id,
                    'competencia' => $dia,
                    'valor' => $anterior?->valor ?? 0,
                    'lancado_em' => CarbonImmutable::today()->toDateString(),
                ]);
                $criados['lancamentos']++;
            }

            $salarios = Receita::query()->where('usuario_id', $dono->id)->where('tipo', TipoReceita::Salario);

            if (! (clone $salarios)->daCompetencia($competencia)->exists()) {
                $ultimo = (clone $salarios)->whereDate('competencia', '<', $dia)->max('competencia');

                if ($ultimo !== null) {
                    $modelos = (clone $salarios)->whereDate('competencia', substr((string) $ultimo, 0, 10))->get();

                    foreach ($modelos as $modelo) {
                        Receita::create([
                            'usuario_id' => $dono->id,
                            'competencia' => $dia,
                            'tipo' => TipoReceita::Salario,
                            'descricao' => $modelo->descricao,
                            'valor' => $modelo->valor,
                        ]);
                        $criados['receitas']++;
                    }
                }
            }

            return $criados;
        });
    }
}
