<?php

namespace App\Http\Controllers;

use App\Models\Lancamento;
use App\Models\Usuario;
use App\Services\ResumoMensal;
use App\Support\Dinheiro;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    private const CATEGORIAS_NO_GRAFICO = 8;

    public function index(Request $request): View
    {
        $ano = $request->integer('ano') ?: CarbonImmutable::now()->year;
        $donos = $this->donosVisiveis($request);

        $meses = [];
        for ($mes = 1; $mes <= 12; $mes++) {
            $meses[$mes] = ResumoMensal::calcular($donos, CarbonImmutable::create($ano, $mes, 1));
        }

        $centavos = fn ($grupo) => $grupo->sum(fn (Lancamento $l) => Dinheiro::centavos($l->valor));

        $porCategoria = Lancamento::query()
            ->with('conta.categoria')
            ->dosUsuarios($donos)
            ->whereYear('competencia', $ano)
            ->get()
            ->groupBy(fn (Lancamento $l) => $l->conta->categoria?->nome ?? 'Sem categoria')
            ->map(fn ($grupo) => $centavos($grupo) / 100.0)
            ->sortDesc();

        if ($porCategoria->count() > self::CATEGORIAS_NO_GRAFICO) {
            $porCategoria = $porCategoria->take(self::CATEGORIAS_NO_GRAFICO - 1)
                ->put('Outras', round($porCategoria->skip(self::CATEGORIAS_NO_GRAFICO - 1)->sum(), 2));
        }

        $anoAnterior = Lancamento::query()
            ->dosUsuarios($donos)
            ->whereYear('competencia', $ano - 1)
            ->get()
            ->groupBy(fn (Lancamento $l) => $l->competencia->month)
            ->map(fn ($grupo) => $centavos($grupo) / 100.0);
        $totaisAnteriores = array_map(fn (int $mes) => $anoAnterior[$mes] ?? 0.0, range(1, 12));

        // A variação compara só os meses que o ano escolhido já tem, para não
        // confrontar um ano em andamento com o anterior inteiro.
        $totais = array_column($meses, 'total');
        $mesesComDados = array_keys(array_filter($totais));
        $ultimoMes = $mesesComDados === [] ? 0 : max($mesesComDados) + 1;
        $total = round(array_sum($totais), 2);
        $mesmoPeriodo = round(array_sum(array_slice($totaisAnteriores, 0, $ultimoMes)), 2);

        return view('relatorios.index', [
            'ano' => $ano,
            'meses' => $meses,
            'indicadores' => [
                'total' => $total,
                'pago' => round(array_sum(array_column($meses, 'pago')), 2),
                'aberto' => round(array_sum(array_column($meses, 'aberto')), 2),
                'media' => $mesesComDados === [] ? 0.0 : round($total / count($mesesComDados), 2),
                'variacao' => $mesmoPeriodo > 0 ? round(($total - $mesmoPeriodo) / $mesmoPeriodo * 100, 1) : null,
            ],
            'graficos' => [
                'ano' => $ano,
                'pago' => array_column($meses, 'pago'),
                'aberto' => array_column($meses, 'aberto'),
                'total' => $totais,
                'anterior' => $totaisAnteriores,
                'temAnterior' => array_sum($totaisAnteriores) > 0,
                'categorias' => $porCategoria->keys()->all(),
                'porCategoria' => $porCategoria->values()->all(),
            ],
            'usuarios' => $request->user()->isAdmin() ? Usuario::query()->orderBy('nome')->get() : collect(),
            'filtroUsuario' => $request->user()->isAdmin() ? $request->integer('usuario') : 0,
        ]);
    }
}
