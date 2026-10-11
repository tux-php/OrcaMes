<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * Donos cujos dados a tela pode mostrar: o membro só vê os próprios;
     * o admin vê todos ou filtra um com ?usuario=ID.
     *
     * @return array<int, int>
     */
    protected function donosVisiveis(Request $request): array
    {
        $usuario = $request->user();

        if (! $usuario->isAdmin()) {
            return [$usuario->id];
        }

        $filtro = $request->integer('usuario');

        return $filtro > 0 ? [$filtro] : Usuario::query()->pluck('id')->all();
    }

    /**
     * Dono de um registro novo: o admin pode lançar em nome de outra pessoa.
     */
    protected function donoParaGravar(Request $request): int
    {
        $usuario = $request->user();

        if ($usuario->isAdmin() && $request->filled('usuario_id')) {
            return Usuario::query()->findOrFail($request->integer('usuario_id'))->id;
        }

        return $usuario->id;
    }
}
