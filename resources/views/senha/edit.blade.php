@extends('layouts.app')

@section('titulo', 'Trocar senha — OrcaMes')

@section('conteudo')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h1 class="h4 mb-3">Trocar senha</h1>
            @if (auth()->user()->deve_trocar_senha)
                <div class="alert alert-info">Sua senha é provisória. Defina uma nova para continuar.</div>
            @endif
            <form method="POST" action="{{ route('senha.update') }}" class="card card-body shadow-sm">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="senha_atual">Senha atual</label>
                    <input class="form-control" type="password" id="senha_atual" name="senha_atual" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Nova senha (mínimo de 8 caracteres)</label>
                    <input class="form-control" type="password" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Repita a nova senha</label>
                    <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
                <button class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
@endsection
