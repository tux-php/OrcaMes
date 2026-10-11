@extends('layouts.app')

@section('titulo', 'Entrar — OrcaMes')

@section('conteudo')
    <div class="row justify-content-center mt-5">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h4 mb-3">OrcaMes</h1>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email">E-mail</label>
                            <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Senha</label>
                            <input class="form-control" type="password" id="password" name="password" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="lembrar" name="lembrar" value="1">
                            <label class="form-check-label" for="lembrar">Manter conectado</label>
                        </div>
                        <button class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
