@extends('layouts.app')

@section('titulo', ($conta->exists ? 'Editar conta' : 'Nova conta').' — OrcaMes')

@section('conteudo')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <h1 class="h4 mb-3">{{ $conta->exists ? 'Editar conta' : 'Nova conta' }}</h1>
            <form method="POST" action="{{ $conta->exists ? route('contas.update', $conta) : route('contas.store') }}" class="card card-body shadow-sm">
                @csrf
                @if ($conta->exists) @method('PUT') @endif
                <div class="mb-3">
                    <label class="form-label" for="nome">Nome</label>
                    <input class="form-control" id="nome" name="nome" maxlength="255" value="{{ old('nome', $conta->nome) }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="categoria_id">Categoria</label>
                    <select class="form-select" id="categoria_id" name="categoria_id">
                        <option value="">Sem categoria</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected((int) old('categoria_id', $conta->categoria_id) === $categoria->id)>{{ $categoria->nome }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($usuarios->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label" for="usuario_id">Responsável</label>
                        <select class="form-select" id="usuario_id" name="usuario_id">
                            @foreach ($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" @selected((int) old('usuario_id', auth()->id()) === $usuario->id)>{{ $usuario->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @foreach ([
                    'recorrente' => 'Recorrente (entra no “Gerar mês”)',
                    'dedutivel_ir' => 'Dedutível no Imposto de Renda',
                    'ativa' => 'Ativa',
                ] as $campo => $rotulo)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="{{ $campo }}" name="{{ $campo }}" value="1" @checked(old($campo, $conta->{$campo}))>
                        <label class="form-check-label" for="{{ $campo }}">{{ $rotulo }}</label>
                    </div>
                @endforeach
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary">Salvar</button>
                    <a class="btn btn-outline-secondary" href="{{ route('contas.index') }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
