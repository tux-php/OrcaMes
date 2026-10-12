<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Support\Busca;
use App\Support\NomePadrao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function index(Request $request): View
    {
        $busca = $request->string('busca')->trim()->toString();

        return view('categorias.index', [
            'busca' => $busca,
            'categorias' => Categoria::query()
                ->withCount('contas')
                ->when($busca !== '', fn ($query) => Busca::contendo($query, 'nome', $busca))
                ->orderBy('nome')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['nome' => NomePadrao::aoDigitar($request->input('nome'))]);

        Categoria::create($request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:categorias,nome'],
        ]));

        return back()->with('ok', 'Categoria criada.');
    }

    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        $request->merge(['nome' => NomePadrao::aoDigitar($request->input('nome'))]);

        $categoria->update($request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('categorias', 'nome')->ignore($categoria)],
        ]));

        return back()->with('ok', 'Categoria alterada.');
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        // As contas ligadas a ela ficam sem categoria (nullOnDelete).
        $categoria->delete();

        return back()->with('ok', 'Categoria excluída.');
    }
}
