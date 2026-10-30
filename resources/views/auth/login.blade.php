@extends('layouts.app')

@section('titulo', 'Entrar — OrcaMes')

@section('classe_corpo', 'tela-login')

@section('conteudo')
    <div class="login-coluna">
        <div class="card">
            <div class="card-body">
                <div class="login-marca">
                    <img src="{{ asset('imagens/logo-orcames.svg') }}" alt="" width="104" height="104">
                    <h1 class="login-nome">Orca<span>Mes</span></h1>
                    <p class="login-lema">Seu orçamento, mês a mês</p>
                </div>
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
                    <button class="btn btn-primary w-100 login-entrar">Entrar</button>
                </form>
            </div>
        </div>
        <p class="login-versao">Versão {{ config('app.versao') }} · desde 2020</p>
    </div>
@endsection
