<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Receita;
use App\Models\Usuario;
use App\Services\ResumoMensal;
use App\Support\Competencia;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PainelController extends Controller
{
    public function index(Request $request): View
    {
        $competencia = Competencia::deTexto($request->query('mes'));
        $donos = $this->donosVisiveis($request);

        $lancamentos = Lancamento::query()
            ->with('conta.categoria', 'conta.usuario')
            ->dosUsuarios($donos)
            ->daCompetencia($competencia)
            ->get()
            ->sortBy(fn (Lancamento $lancamento) => mb_strtolower($lancamento->conta->nome))
            ->values();

        $receitas = Receita::query()
            ->with('usuario')
            ->dosUsuarios($donos)
            ->daCompetencia($competencia)
            ->orderByDesc('tipo')
            ->orderBy('id')
            ->get();

        return view('painel.index', [
            'competencia' => $competencia,
            'lancamentos' => $lancamentos,
            'receitas' => $receitas,
            'resumo' => ResumoMensal::calcular($donos, $competencia),
            'contas' => Conta::query()->with('usuario')->dosUsuarios($donos)->where('ativa', true)->orderBy('nome')->get(),
            'usuarios' => $request->user()->isAdmin() ? Usuario::query()->orderBy('nome')->get() : collect(),
            'filtroUsuario' => $request->user()->isAdmin() ? $request->integer('usuario') : 0,
        ]);
    }
}
