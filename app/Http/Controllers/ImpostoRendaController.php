<?php

namespace App\Http\Controllers;

use App\Enums\ResultadoIr;
use App\Models\ComprovanteIr;
use App\Models\DeclaracaoIr;
use App\Models\Lancamento;
use App\Models\Usuario;
use App\Support\Dinheiro;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImpostoRendaController extends Controller
{
    public function index(Request $request): View
    {
        // O padrão é o ano-base da declaração que se entrega no ano corrente.
        $ano = $request->integer('ano') ?: CarbonImmutable::now()->year - 1;
        $donos = $this->donosVisiveis($request);

        $dedutiveis = Lancamento::query()
            ->with('conta')
            ->dosUsuarios($donos)
            ->whereYear('competencia', $ano)
            ->whereNotNull('pago_em')
            ->whereHas('conta', fn ($conta) => $conta->where('dedutivel_ir', true))
            ->get()
            ->groupBy(fn (Lancamento $l) => $l->conta->nome)
            ->map(fn ($grupo) => $grupo->sum(fn (Lancamento $l) => Dinheiro::centavos($l->valor)) / 100.0)
            ->sortKeys();

        return view('ir.index', [
            'ano' => $ano,
            'declaracoes' => DeclaracaoIr::query()->with('usuario')->dosUsuarios($donos)->where('ano_base', $ano)->get(),
            'comprovantes' => ComprovanteIr::query()->with('usuario')->dosUsuarios($donos)->where('ano_base', $ano)->orderBy('descricao')->get(),
            'dedutiveis' => $dedutiveis,
            'usuarios' => $request->user()->isAdmin() ? Usuario::query()->orderBy('nome')->get() : collect(),
            'filtroUsuario' => $request->user()->isAdmin() ? $request->integer('usuario') : 0,
        ]);
    }

    /**
     * Uma declaração por pessoa e ano-base: gravar de novo substitui a anterior.
     */
    public function salvarDeclaracao(Request $request): RedirectResponse
    {
        $request->merge(['valor' => Dinheiro::normalizar($request->input('valor'))]);

        $dados = $request->validate([
            'ano_base' => ['required', 'integer', 'between:2000,2100'],
            'resultado' => ['required', Rule::enum(ResultadoIr::class)],
            'valor' => ['required', 'decimal:0,2', 'min:0'],
            'observacao' => ['nullable', 'string', 'max:255'],
        ]);

        DeclaracaoIr::updateOrCreate(
            ['usuario_id' => $this->donoParaGravar($request), 'ano_base' => $dados['ano_base']],
            ['resultado' => $dados['resultado'], 'valor' => $dados['valor'], 'observacao' => $dados['observacao'] ?? null],
        );

        return back()->with('ok', 'Resultado da declaração salvo.');
    }

    public function excluirDeclaracao(DeclaracaoIr $declaracao): RedirectResponse
    {
        $this->authorize('gerenciar', $declaracao);
        $declaracao->delete();

        return back()->with('ok', 'Resultado da declaração excluído.');
    }

    public function anexarComprovante(Request $request): RedirectResponse
    {
        $request->merge(['valor' => Dinheiro::normalizar($request->input('valor'))]);

        $dados = $request->validate([
            'ano_base' => ['required', 'integer', 'between:2000,2100'],
            'descricao' => ['required', 'string', 'max:255'],
            'valor' => ['nullable', 'decimal:0,2', 'min:0'],
            'arquivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $dono = $this->donoParaGravar($request);
        $arquivo = $request->file('arquivo');

        ComprovanteIr::create([
            'usuario_id' => $dono,
            'ano_base' => $dados['ano_base'],
            'descricao' => $dados['descricao'],
            'valor' => $dados['valor'] ?? null,
            'arquivo' => $arquivo->store("comprovantes-ir/{$dono}/{$dados['ano_base']}", ComprovanteIr::DISCO),
            'nome_original' => $arquivo->getClientOriginalName(),
        ]);

        return back()->with('ok', 'Comprovante anexado.');
    }

    public function baixarComprovante(ComprovanteIr $comprovante): StreamedResponse
    {
        $this->authorize('gerenciar', $comprovante);

        return Storage::disk(ComprovanteIr::DISCO)->download($comprovante->arquivo, $comprovante->nome_original);
    }

    public function excluirComprovante(ComprovanteIr $comprovante): RedirectResponse
    {
        $this->authorize('gerenciar', $comprovante);

        Storage::disk(ComprovanteIr::DISCO)->delete($comprovante->arquivo);
        $comprovante->delete();

        return back()->with('ok', 'Comprovante excluído.');
    }
}
