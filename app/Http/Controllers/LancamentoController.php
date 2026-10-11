<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Usuario;
use App\Services\GeradorDeMes;
use App\Support\Competencia;
use App\Support\Dinheiro;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LancamentoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $dados = $this->validar($request);
        $conta = Conta::query()->findOrFail($dados['conta_id']);
        $this->authorize('gerenciar', $conta);

        Lancamento::create([
            'conta_id' => $conta->id,
            'competencia' => Competencia::deTexto($dados['mes'])->toDateString(),
            'valor' => $dados['valor'],
            'lancado_em' => CarbonImmutable::today()->toDateString(),
            'observacao' => $dados['observacao'] ?? null,
        ]);

        return back()->with('ok', 'Lançamento incluído.');
    }

    public function edit(Lancamento $lancamento): View
    {
        $this->authorize('gerenciar', $lancamento);

        return view('lancamentos.edit', [
            'lancamento' => $lancamento,
            'contas' => Conta::query()->where('usuario_id', $lancamento->donoId())->orderBy('nome')->get(),
        ]);
    }

    public function update(Request $request, Lancamento $lancamento): RedirectResponse
    {
        $this->authorize('gerenciar', $lancamento);

        $dados = $this->validar($request, ['pago_em' => ['nullable', 'date']]);
        $conta = Conta::query()->findOrFail($dados['conta_id']);

        // Trocar a conta não pode mudar o lançamento de dono.
        if ($conta->usuario_id !== $lancamento->donoId()) {
            throw ValidationException::withMessages(['conta_id' => 'A conta escolhida pertence a outra pessoa.']);
        }

        $lancamento->update([
            'conta_id' => $conta->id,
            'competencia' => Competencia::deTexto($dados['mes'])->toDateString(),
            'valor' => $dados['valor'],
            'pago_em' => $dados['pago_em'] ?? null,
            'observacao' => $dados['observacao'] ?? null,
        ]);

        return redirect()
            ->route('painel', ['mes' => $dados['mes']])
            ->with('ok', 'Lançamento alterado.');
    }

    public function destroy(Lancamento $lancamento): RedirectResponse
    {
        $this->authorize('gerenciar', $lancamento);
        $lancamento->delete();

        return back()->with('ok', 'Lançamento excluído.');
    }

    public function pagar(Lancamento $lancamento): RedirectResponse
    {
        $this->authorize('gerenciar', $lancamento);
        $lancamento->update(['pago_em' => CarbonImmutable::today()->toDateString()]);

        return back()->with('ok', 'Pagamento registrado.');
    }

    public function estornar(Lancamento $lancamento): RedirectResponse
    {
        $this->authorize('gerenciar', $lancamento);
        $lancamento->update(['pago_em' => null]);

        return back()->with('ok', 'Pagamento estornado.');
    }

    public function gerar(Request $request): RedirectResponse
    {
        $dados = $request->validate(['mes' => ['required', 'date_format:Y-m']]);
        $dono = Usuario::query()->findOrFail($this->donoParaGravar($request));

        $criados = GeradorDeMes::gerar($dono, Competencia::deTexto($dados['mes']));

        return back()->with('ok', sprintf(
            'Mês gerado para %s: %d lançamento(s) e %d receita(s) criados.',
            $dono->nome,
            $criados['lancamentos'],
            $criados['receitas'],
        ));
    }

    /**
     * @param  array<string, mixed>  $regrasExtras
     * @return array<string, mixed>
     */
    private function validar(Request $request, array $regrasExtras = []): array
    {
        $request->merge(['valor' => Dinheiro::normalizar($request->input('valor'))]);

        return $request->validate([
            'conta_id' => ['required', 'integer', 'exists:contas,id'],
            'mes' => ['required', 'date_format:Y-m'],
            'valor' => ['required', 'decimal:0,2', 'min:0'],
            'observacao' => ['nullable', 'string', 'max:255'],
            ...$regrasExtras,
        ]);
    }
}
