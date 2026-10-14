<?php

namespace App\Http\Controllers;

use App\Models\Lancamento;
use App\Models\Usuario;
use App\Support\Competencia;
use App\Support\Dinheiro;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class ExtratoController extends Controller
{
    // Um período maior que isso vira um PDF que ninguém lê e demora a gerar.
    private const MESES_NO_MAXIMO = 24;

    public function pagamentos(Request $request): Response
    {
        $request->validate([
            'de' => ['required', 'date_format:Y-m'],
            'ate' => ['required', 'date_format:Y-m', 'after_or_equal:de'],
        ], [
            'ate.after_or_equal' => 'O mês final não pode vir antes do inicial.',
        ]);

        $de = Competencia::deTexto($request->query('de'));
        $ate = Competencia::deTexto($request->query('ate'));

        if ($de->diffInMonths($ate) >= self::MESES_NO_MAXIMO) {
            return back()->withErrors(['ate' => 'Escolha no máximo '.self::MESES_NO_MAXIMO.' meses.'])->withInput();
        }

        $donos = $this->donosVisiveis($request);

        $lancamentos = Lancamento::query()
            ->with('conta.categoria', 'conta.usuario')
            ->dosUsuarios($donos)
            ->where('competencia', '>=', $de->toDateString())
            ->where('competencia', '<', $ate->addMonth()->toDateString())
            ->get()
            ->sortBy([
                fn (Lancamento $a, Lancamento $b) => $a->competencia <=> $b->competencia,
                fn (Lancamento $a, Lancamento $b) => $a->pago_em <=> $b->pago_em,
                fn (Lancamento $a, Lancamento $b) => mb_strtolower($a->conta->nome) <=> mb_strtolower($b->conta->nome),
            ])
            ->values();

        [$pagos, $abertos] = $lancamentos->partition(fn (Lancamento $l) => $l->estaPago());

        $soma = fn (Collection $grupo) => $grupo->sum(fn (Lancamento $l) => Dinheiro::centavos($l->valor)) / 100.0;

        $porCategoria = $lancamentos
            ->groupBy(fn (Lancamento $l) => $l->conta->categoria?->nome ?? 'Sem categoria')
            ->map(fn (Collection $grupo) => [
                'pago' => $soma($grupo->filter->estaPago()),
                'aberto' => $soma($grupo->reject->estaPago()),
            ])
            ->sortByDesc(fn (array $valores) => $valores['pago'] + $valores['aberto']);

        $usuario = $request->user();
        $filtro = $usuario->isAdmin() ? $request->integer('usuario') : $usuario->id;

        $pdf = Pdf::loadView('relatorios.extrato-pagamentos', [
            'periodo' => $de->equalTo($ate)
                ? Competencia::rotulo($de)
                : Competencia::rotulo($de).' a '.Competencia::rotulo($ate),
            'pessoa' => $filtro > 0 ? (Usuario::query()->find($filtro)?->nome ?? '—') : 'Todos',
            'variasPessoas' => count($donos) > 1,
            'geradoEm' => CarbonImmutable::now(),
            'geradoPor' => $usuario->nome,
            'pagosPorMes' => $pagos->groupBy(fn (Lancamento $l) => $l->competencia->format('Y-m')),
            'abertos' => $abertos,
            'porCategoria' => $porCategoria,
            'totais' => ['pago' => $soma($pagos), 'aberto' => $soma($abertos)],
        ])->setPaper('a4');

        // Numeração no rodapé: só dá para saber o total de páginas depois de renderizar.
        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            $canvas->get_width() - 110, $canvas->get_height() - 31,
            'Página {PAGE_NUM} de {PAGE_COUNT}',
            $dompdf->getFontMetrics()->getFont('DejaVu Sans'), 8, [0.32, 0.32, 0.31],
        );

        return $pdf->download(sprintf('extrato-pagamentos-%s-a-%s.pdf', $de->format('Y-m'), $ate->format('Y-m')));
    }
}
