<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SenhaController extends Controller
{
    public function edit(): View
    {
        return view('senha.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'senha_atual' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:senha_atual', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => $dados['password'],
            'deve_trocar_senha' => false,
        ]);

        return redirect()->route('painel')->with('ok', 'Senha alterada.');
    }
}
