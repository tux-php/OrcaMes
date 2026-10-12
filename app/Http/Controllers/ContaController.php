<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Usuario;
use App\Support\Busca;
use App\Support\NomePadrao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContaController extends Controller
{
    public function index(Request $request): View
    {
        $categoria = Categoria::query()->find($request->integer('categoria'));
        $busca = $request->string('busca')->trim()->toString();

        return view('contas.index', [
            'categoriaFiltrada' => $categoria,
            'busca' => $busca,
            'contas' => Conta::query()
                ->with('categoria', 'usuario')
                ->withCount('lancamentos')
                ->dosUsuarios($this->donosVisiveis($request))
                ->when($categoria, fn ($query) => $query->where('categoria_id', $categoria->id))
                ->when($busca !== '', fn ($query) => Busca::contendo($query, 'nome', $busca))
                ->orderByDesc('ativa')
                ->orderBy('nome')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create(Request $request): View
    {
        // A busca da lista manda o nome procurado quando não encontra a conta.
        $conta = new Conta(['nome' => $request->string('nome')->trim()->toString() ?: null, 'recorrente' => true, 'ativa' => true]);

        return view('contas.form', $this->dadosDoFormulario($request, $conta));
    }

    public function store(Request $request): RedirectResponse
    {
        Conta::create([
            ...$this->validar($request),
            'usuario_id' => $this->donoParaGravar($request),
        ]);

        return redirect()->route('contas.index')->with('ok', 'Conta criada.');
    }

    public function edit(Request $request, Conta $conta): View
    {
        $this->authorize('gerenciar', $conta);

        return view('contas.form', $this->dadosDoFormulario($request, $conta));
    }

    public function update(Request $request, Conta $conta): RedirectResponse
    {
        $this->authorize('gerenciar', $conta);
        $conta->update($this->validar($request));

        return redirect()->route('contas.index')->with('ok', 'Conta alterada.');
    }

    public function destroy(Conta $conta): RedirectResponse
    {
        $this->authorize('gerenciar', $conta);

        if ($conta->lancamentos()->exists()) {
            return back()->with('erro', 'Esta conta tem lançamentos. Desative-a em vez de excluir.');
        }

        $conta->delete();

        return back()->with('ok', 'Conta excluída.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request): array
    {
        $request->merge(['nome' => NomePadrao::aoDigitar($request->input('nome'))]);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'categoria_id' => ['nullable', 'integer', 'exists:categorias,id'],
        ]);

        return [
            ...$dados,
            'recorrente' => $request->boolean('recorrente'),
            'dedutivel_ir' => $request->boolean('dedutivel_ir'),
            'ativa' => $request->boolean('ativa'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function dadosDoFormulario(Request $request, Conta $conta): array
    {
        return [
            'conta' => $conta,
            'categorias' => Categoria::query()->orderBy('nome')->get(),
            'usuarios' => $request->user()->isAdmin() && ! $conta->exists
                ? Usuario::query()->where('ativo', true)->orderBy('nome')->get()
                : collect(),
        ];
    }
}
