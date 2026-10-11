<?php

namespace App\Http\Controllers;

use App\Enums\Perfil;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        return view('usuarios.index', ['usuarios' => Usuario::query()->orderBy('nome')->get()]);
    }

    public function create(): View
    {
        return view('usuarios.form', ['usuario' => new Usuario(['perfil' => Perfil::Membro, 'ativo' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $senha = Str::password(12, symbols: false);

        $usuario = Usuario::create([
            ...$this->validar($request),
            'ativo' => true,
            'password' => $senha,
            'deve_trocar_senha' => true,
        ]);

        return redirect()->route('usuarios.index')->with('senha', $this->avisoDeSenha($usuario, $senha));
    }

    public function edit(Usuario $usuario): View
    {
        return view('usuarios.form', ['usuario' => $usuario]);
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $dados = [...$this->validar($request, $usuario), 'ativo' => $request->boolean('ativo')];

        $deixaDeSerAdmin = $usuario->isAdmin() && $usuario->ativo
            && ($dados['perfil'] !== Perfil::Admin->value || ! $dados['ativo']);

        if ($deixaDeSerAdmin && ! $this->existeOutroAdmin($usuario)) {
            throw ValidationException::withMessages([
                'perfil' => 'É preciso manter pelo menos um administrador ativo.',
            ]);
        }

        $aviso = null;

        if ($request->boolean('redefinir_senha')) {
            $senha = Str::password(12, symbols: false);
            $dados['password'] = $senha;
            $dados['deve_trocar_senha'] = true;
            $aviso = $this->avisoDeSenha($usuario, $senha);
        }

        $usuario->update($dados);

        return redirect()->route('usuarios.index')->with('ok', 'Usuário alterado.')->with('senha', $aviso);
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request, ?Usuario $usuario = null): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sobrenome' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($usuario)],
            'perfil' => ['required', Rule::enum(Perfil::class)],
        ]);
    }

    private function existeOutroAdmin(Usuario $usuario): bool
    {
        return Usuario::query()
            ->whereKeyNot($usuario->id)
            ->where('perfil', Perfil::Admin)
            ->where('ativo', true)
            ->exists();
    }

    private function avisoDeSenha(Usuario $usuario, string $senha): string
    {
        return "Senha provisória de {$usuario->email}: {$senha} — ela não será mostrada de novo.";
    }
}
