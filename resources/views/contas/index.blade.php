@extends('layouts.app')

@section('titulo', 'Contas — OrcaMes')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Contas</h1>
        <a class="btn btn-primary" href="{{ route('contas.create') }}">Nova conta</a>
    </div>

    @if ($categoriaFiltrada)
        <p class="mb-3">
            Mostrando só as contas da categoria <strong>{{ $categoriaFiltrada->nome }}</strong>.
            <a href="{{ route('contas.index') }}">Ver todas</a>
        </p>
    @endif

    <div class="card">
        <div class="card-header"><strong>{{ $categoriaFiltrada ? 'Contas da categoria' : 'Todas as contas' }}</strong><span class="lista-nota">{{ $contas->total() }} {{ $contas->total() === 1 ? 'conta' : 'contas' }}</span></div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Categoria</th>
                        @can('admin')<th>Responsável</th>@endcan
                        <th>Situação</th>
                        <th class="text-end">Lançamentos</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contas as $conta)
                        <tr @class(['lista-inativa' => ! $conta->ativa])>
                            <td>{{ $conta->nome }}</td>
                            <td>{{ $conta->categoria?->nome ?? '—' }}</td>
                            @can('admin')<td>{{ $conta->usuario->nome }}</td>@endcan
                            <td>
                                <span class="selo {{ $conta->ativa ? 'selo-ok' : 'selo-neutro' }}">{{ $conta->ativa ? 'Ativa' : 'Inativa' }}</span>
                                @if ($conta->recorrente)<span class="selo selo-info">Recorrente</span>@endif
                                @if ($conta->dedutivel_ir)<span class="selo selo-destaque">Dedutível no IR</span>@endif
                            </td>
                            <td class="text-end">{{ $conta->lancamentos_count }}</td>
                            <td class="text-nowrap"><div class="acoes">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('contas.edit', $conta) }}">Editar</a>
                                @if ($conta->lancamentos_count === 0)
                                    <form method="POST" action="{{ route('contas.destroy', $conta) }}" class="d-inline" onsubmit="return confirm('Excluir esta conta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr class="lista-vazia"><td colspan="6">Nenhuma conta cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contas->hasPages())
            <div class="card-footer">{{ $contas->links() }}</div>
        @endif
    </div>
@endsection
