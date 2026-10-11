<?php

namespace App\Http\Controllers;

use App\Enums\TipoReceita;
use App\Models\Receita;
use App\Support\Competencia;
use App\Support\Dinheiro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReceitaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['valor' => Dinheiro::normalizar($request->input('valor'))]);

        $dados = $request->validate([
            'mes' => ['required', 'date_format:Y-m'],
            'tipo' => ['required', Rule::enum(TipoReceita::class)],
            'descricao' => ['nullable', 'string', 'max:255'],
            'valor' => ['required', 'decimal:0,2', 'min:0'],
        ]);

        Receita::create([
            'usuario_id' => $this->donoParaGravar($request),
            'competencia' => Competencia::deTexto($dados['mes'])->toDateString(),
            'tipo' => $dados['tipo'],
            'descricao' => $dados['descricao'] ?? null,
            'valor' => $dados['valor'],
        ]);

        return back()->with('ok', 'Receita incluída.');
    }

    public function destroy(Receita $receita): RedirectResponse
    {
        $this->authorize('gerenciar', $receita);
        $receita->delete();

        return back()->with('ok', 'Receita excluída.');
    }
}
