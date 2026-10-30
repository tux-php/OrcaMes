<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('titulo', 'OrcaMes')</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0b2545">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="OrcaMes">
    <link rel="apple-touch-icon" href="{{ asset('icones/icone-apple-180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icones/icone-192.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('estilos')
</head>
<body class="@yield('classe_corpo', 'bg-light')">
@auth
    <nav class="navbar navbar-expand-md navbar-dark barra-topo mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('painel') }}">
                <img src="{{ asset('imagens/logo-orcames.svg') }}" alt="" width="32" height="32">
                <b class="fw-bold">Orca<span>Mes</span></b>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link @if(request()->routeIs('painel')) active @endif" href="{{ route('painel') }}">Mês</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->routeIs('contas.*')) active @endif" href="{{ route('contas.index') }}">Contas</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->routeIs('relatorio')) active @endif" href="{{ route('relatorio') }}">Painel anual</a></li>
                    <li class="nav-item"><a class="nav-link @if(request()->routeIs('ir.*')) active @endif" href="{{ route('ir.index') }}">Imposto de Renda</a></li>
                    @can('admin')
                        <li class="nav-item"><a class="nav-link @if(request()->routeIs('categorias.*')) active @endif" href="{{ route('categorias.index') }}">Categorias</a></li>
                        <li class="nav-item"><a class="nav-link @if(request()->routeIs('usuarios.*')) active @endif" href="{{ route('usuarios.index') }}">Usuários</a></li>
                    @endcan
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <span class="navbar-text">{{ auth()->user()->nome }}</span>
                    <a class="btn btn-sm btn-outline-light" href="{{ route('senha.edit') }}">Senha</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-light">Sair</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
@endauth

<main class="container pb-5">
    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif
    @if (session('erro'))
        <div class="alert alert-danger">{{ session('erro') }}</div>
    @endif
    @if (session('senha'))
        <div class="alert alert-warning">{{ session('senha') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('conteudo')
</main>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/mascara-reais.js') }}"></script>
<script src="{{ asset('js/contagem.js') }}"></script>
<script src="{{ asset('js/busca-select.js') }}"></script>
@stack('scripts')
<script>
    // Torna o site instalável como app; ver public/sw.js.
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
    }
</script>
</body>
</html>
