@extends('layouts.app')

@section('titulo', 'Usuários — OrcaMes')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Usuários</h1>
        <a class="btn btn-primary" href="{{ route('usuarios.create') }}">Novo usuário</a>
    </div>

    <div class="card">
        <div class="card-header"><strong>Quem usa o OrcaMes</strong><span class="lista-nota">{{ $usuarios->count() }} {{ $usuarios->count() === 1 ? 'usuário' : 'usuários' }}</span></div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->nome }} {{ $usuario->sobrenome }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td><span class="selo {{ $usuario->isAdmin() ? 'selo-destaque' : 'selo-neutro' }}">{{ $usuario->perfil->rotulo() }}</span></td>
                            <td>
                                @if (! $usuario->ativo)
                                    <span class="selo selo-neutro">Inativo</span>
                                @elseif ($usuario->deve_trocar_senha)
                                    <span class="selo selo-atencao">Senha provisória</span>
                                @else
                                    <span class="selo selo-ok">Ativo</span>
                                @endif
                            </td>
                            <td><a class="btn btn-sm btn-outline-primary" href="{{ route('usuarios.edit', $usuario) }}">Editar</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
