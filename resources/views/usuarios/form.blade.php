@extends('layouts.app')

@php use App\Enums\Perfil; @endphp

@section('titulo', ($usuario->exists ? 'Editar usuário' : 'Novo usuário').' — OrcaMes')

@section('conteudo')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <h1 class="h4 mb-3">{{ $usuario->exists ? 'Editar usuário' : 'Novo usuário' }}</h1>
            <form method="POST" action="{{ $usuario->exists ? route('usuarios.update', $usuario) : route('usuarios.store') }}" class="card card-body shadow-sm">
                @csrf
                @if ($usuario->exists) @method('PUT') @endif
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="nome">Nome</label>
                        <input class="form-control" id="nome" name="nome" maxlength="255" value="{{ old('nome', $usuario->nome) }}" required autofocus>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="sobrenome">Sobrenome</label>
                        <input class="form-control" id="sobrenome" name="sobrenome" maxlength="255" value="{{ old('sobrenome', $usuario->sobrenome) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">E-mail</label>
                    <input class="form-control" type="email" id="email" name="email" maxlength="255" value="{{ old('email', $usuario->email) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="perfil">Perfil</label>
                    <select class="form-select" id="perfil" name="perfil">
                        @foreach (Perfil::cases() as $perfil)
                            <option value="{{ $perfil->value }}" @selected(old('perfil', $usuario->perfil?->value) === $perfil->value)>{{ $perfil->rotulo() }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">O administrador vê os lançamentos de todos e gerencia usuários e categorias.</div>
                </div>
                @if ($usuario->exists)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1" @checked(old('ativo', $usuario->ativo))>
                        <label class="form-check-label" for="ativo">Ativo</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="redefinir_senha" name="redefinir_senha" value="1">
                        <label class="form-check-label" for="redefinir_senha">Gerar nova senha provisória</label>
                    </div>
                @else
                    <div class="form-text mb-2">Uma senha provisória será gerada e mostrada uma única vez.</div>
                @endif
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary">Salvar</button>
                    <a class="btn btn-outline-secondary" href="{{ route('usuarios.index') }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
