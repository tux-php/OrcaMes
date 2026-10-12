<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Support\NomePadrao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function index(): View
    {
        return view('categorias.index', [
            'categorias' => Categoria::query()->withCount('contas')->orderBy('nome')->paginate(10),
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
