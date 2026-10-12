@extends('layouts.app')

@section('titulo', 'Categorias — OrcaMes')

@section('conteudo')
    <h1 class="h4 mb-1">Categorias</h1>
    <p class="text-muted">Agrupam as contas no painel anual, em “Para onde foi o dinheiro”.</p>

    <form method="POST" action="{{ route('categorias.store') }}" class="row g-2 mb-3">
        @csrf
        <div class="col-sm-6 col-md-4">
            <input class="form-control" name="nome" maxlength="255" placeholder="Nome da nova categoria" aria-label="Nome da nova categoria" required>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Incluir</button>
        </div>
    </form>

    <form method="GET" action="{{ route('categorias.index') }}" class="row g-2 mb-3" role="search">
        <div class="col-sm-6 col-md-4">
            <input class="form-control" type="search" name="busca" maxlength="255" value="{{ $busca }}" placeholder="Buscar categoria pelo nome" aria-label="Buscar categoria pelo nome">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary">Buscar</button>
        </div>
    </form>

    @if ($busca !== '')
        <p class="mb-3">
            Mostrando só as categorias com <strong>{{ $busca }}</strong> no nome.
            <a href="{{ route('categorias.index') }}">Ver todas</a>
        </p>
    @endif

    <div class="card">
        <div class="card-header"><strong>{{ $busca !== '' ? 'Resultado da busca' : 'Todas as categorias' }}</strong><span class="lista-nota">{{ $categorias->total() }} {{ $categorias->total() === 1 ? 'categoria' : 'categorias' }}</span></div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Contas nesta categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categorias as $categoria)
                        <tr>
                            <td>{{ $categoria->nome }}</td>
                            <td>
                                @if ($categoria->contas_count > 0)
                                    <a href="{{ route('contas.index', ['categoria' => $categoria->id]) }}">{{ $categoria->contas_count }} {{ $categoria->contas_count === 1 ? 'conta' : 'contas' }}</a>
                                @else
                                    <span class="lista-detalhe">Nenhuma</span>
                                @endif
                            </td>
                            <td class="text-nowrap"><div class="acoes">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#renomear"
                                    data-acao="{{ route('categorias.update', $categoria) }}" data-nome="{{ $categoria->nome }}">Renomear</button>
                                <form method="POST" action="{{ route('categorias.destroy', $categoria) }}" class="d-inline"
                                    onsubmit="return confirm(@js($categoria->contas_count > 0
                                        ? "Excluir a categoria “{$categoria->nome}”? Ela está em {$categoria->contas_count} conta(s), que ficarão sem categoria."
                                        : "Excluir a categoria “{$categoria->nome}”?"))">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </div></td>
                        </tr>
                    @empty
                        <tr class="lista-vazia"><td colspan="3">
                            @if ($busca !== '')
                                Nenhuma categoria com “{{ $busca }}” no nome.
                                <form method="POST" action="{{ route('categorias.store') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="nome" value="{{ $busca }}">
                                    <button class="btn btn-link p-0 align-baseline">Incluir “{{ $busca }}”</button>
                                </form>
                            @else
                                Nenhuma categoria cadastrada.
                            @endif
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categorias->hasPages())
            <div class="card-footer">{{ $categorias->links() }}</div>
        @endif
    </div>

    <div class="modal fade" id="renomear" tabindex="-1" aria-labelledby="renomear-titulo" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="renomear-titulo">Renomear categoria</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label" for="renomear-nome">Nome</label>
                    <input class="form-control" id="renomear-nome" name="nome" maxlength="255" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const janela = document.getElementById('renomear');
            const campo = document.getElementById('renomear-nome');

            janela.addEventListener('show.bs.modal', (evento) => {
                janela.querySelector('form').action = evento.relatedTarget.dataset.acao;
                campo.value = evento.relatedTarget.dataset.nome;
            });
            janela.addEventListener('shown.bs.modal', () => campo.select());
        })();
    </script>
@endpush
