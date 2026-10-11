<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarAcesso
{
    /**
     * Derruba a sessão de quem foi desativado e segura quem ainda está com senha provisória.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && ! $usuario->ativo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        if ($usuario?->deve_trocar_senha && ! $request->routeIs('senha.*', 'logout')) {
            return redirect()->route('senha.edit');
        }

        return $next($request);
    }
}
