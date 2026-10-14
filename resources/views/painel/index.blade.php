@extends('layouts.app')

@php
    use App\Enums\TipoReceita;
    use App\Support\Competencia;
    use App\Support\Dinheiro;

    $mes = $competencia->format('Y-m');
    $filtro = $filtroUsuario ? ['usuario' => $filtroUsuario] : [];
    $admin = auth()->user()->isAdmin();
@endphp

@section('titulo', Competencia::rotulo($competencia).' — OrcaMes')

@section('conteudo')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('painel', ['mes' => $competencia->subMonth()->format('Y-m')] + $filtro) }}" aria-label="Mês anterior">&laquo;</a>
            <h1 class="h4 mb-0">{{ Competencia::rotulo($competencia) }}</h1>
            <a class="btn btn-outline-secondary" href="{{ route('painel', ['mes' => $competencia->addMonth()->format('Y-m')] + $filtro) }}" aria-label="Próximo mês">&raquo;</a>
            <a class="btn btn-outline-secondary" href="{{ route('relatorio.pagamentos', ['de' => $mes, 'ate' => $mes] + $filtro) }}" title="Extrato de pagamentos do mês em PDF">PDF</a>
        </div>

        @if ($admin)
            <form method="GET" action="{{ route('painel') }}" class="d-flex gap-2">
                <input type="hidden" name="mes" value="{{ $mes }}">
                <select class="form-select" name="usuario" onchange="this.form.submit()" aria-label="Pessoa">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" @selected($filtroUsuario === $usuario->id)>{{ $usuario->nome }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Receitas', $resumo['receitas'], 'primary'],
            ['Pago', $resumo['pago'], 'success'],
            ['Em aberto', $resumo['aberto'], 'warning'],
            ['Sobra hoje', $resumo['sobra'], 'info'],
            ['Sobra prevista', $resumo['sobra_prevista'], 'secondary'],
        ] as [$rotulo, $valor, $cor])
            <div class="{{ $loop->last ? 'col-12' : 'col-6' }} col-md">
                <div class="card border-{{ $cor }} h-100">
                    <div class="card-body py-2">
                        <div class="small text-muted">{{ $rotulo }}</div>
                        <div class="fs-5 fw-semibold contagem @if($valor < 0) text-danger @endif" data-contagem="{{ $valor }}">R$ {{ Dinheiro::br($valor) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <strong>Lançamentos</strong>
            <form method="POST" action="{{ route('meses.gerar') }}" class="d-flex gap-2">
                @csrf
                <input type="hidden" name="mes" value="{{ $mes }}">
                @if ($admin)
                    <select class="form-select form-select-sm" name="usuario_id" aria-label="Gerar para">
                        @foreach ($usuarios->where('ativo', true) as $usuario)
                            <option value="{{ $usuario->id }}" @selected(($filtroUsuario ?: auth()->id()) === $usuario->id)>{{ $usuario->nome }}</option>
                        @endforeach
                    </select>
                @endif
                <button class="btn btn-sm btn-outline-primary text-nowrap" title="Cria as contas recorrentes e o salário que ainda faltam neste mês, repetindo os últimos valores">Gerar mês</button>
            </form>
        </div>
        {{-- No celular cada lançamento é um bloco, com o valor e o botão de pagar à vista. --}}
        <div class="lancamentos-celular d-md-none">
            {{-- Os que ainda estão em aberto vêm primeiro, para pagar sem rolar a tela. --}}
            @forelse ($lancamentos->sortBy(fn ($lancamento) => $lancamento->estaPago()) as $lancamento)
                <div class="lancamento-celular">
                    <div class="d-flex justify-content-between align-items-baseline gap-3">
                        <div class="fw-semibold">{{ $lancamento->conta->nome }}</div>
                        <div class="fw-semibold text-nowrap">R$ {{ Dinheiro::br($lancamento->valor) }}</div>
                    </div>
                    <div class="lista-detalhe">
                        {{ $lancamento->conta->categoria?->nome ?? 'Sem categoria' }}@if ($admin) · {{ $lancamento->conta->usuario->nome }}@endif
                        @if ($lancamento->observacao)<div>{{ $lancamento->observacao }}</div>@endif
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
                        @if ($lancamento->estaPago())
                            <span class="selo selo-ok">Pago em {{ $lancamento->pago_em->format('d/m/Y') }}</span>
                        @else
                            <span class="selo selo-atencao">Em aberto</span>
                        @endif
                        @include('painel.acoes-lancamento', ['celular' => true])
                    </div>
                </div>
            @empty
                <div class="lancamento-celular text-center text-muted py-4">Nenhum lançamento neste mês. Use “Gerar mês” ou toque em “Lançar gasto”.</div>
            @endforelse
        </div>
        <div class="table-responsive d-none d-md-block">
            <table class="table">
                <thead>
                    <tr>
                        <th>Conta</th>
                        <th>Categoria</th>
                        @if ($admin)<th>Responsável</th>@endif
                        <th class="text-end">Valor</th>
                        <th>Lançado</th>
                        <th>Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lancamentos as $lancamento)
                        <tr>
                            <td>
                                {{ $lancamento->conta->nome }}
                                @if ($lancamento->observacao)
                                    <div class="lista-detalhe">{{ $lancamento->observacao }}</div>
                                @endif
                            </td>
                            <td>{{ $lancamento->conta->categoria?->nome ?? '—' }}</td>
                            @if ($admin)<td>{{ $lancamento->conta->usuario->nome }}</td>@endif
                            <td class="text-end text-nowrap">R$ {{ Dinheiro::br($lancamento->valor) }}</td>
                            <td class="text-nowrap">{{ $lancamento->lancado_em->format('d/m/Y') }}</td>
                            <td class="text-nowrap">
                                @if ($lancamento->estaPago())
                                    <span class="selo selo-ok">Pago em {{ $lancamento->pago_em->format('d/m/Y') }}</span>
                                @else
                                    <span class="selo selo-atencao">Em aberto</span>
                                @endif
                            </td>
                            <td class="text-nowrap">@include('painel.acoes-lancamento')</td>
                        </tr>
                    @empty
                        <tr class="lista-vazia"><td colspan="{{ $admin ? 7 : 6 }}">Nenhum lançamento neste mês. Use “Gerar mês” ou inclua abaixo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-none d-md-block">
            <form method="POST" action="{{ route('lancamentos.store') }}" class="row g-2 align-items-end">
                @csrf
                <input type="hidden" name="mes" value="{{ $mes }}">
                <div class="col-md-4">
                    <label class="form-label small mb-1" for="conta_id">Conta</label>
                    <select class="form-select" id="conta_id" name="conta_id" required>
                        <option value="">Escolha…</option>
                        @foreach ($contas as $conta)
                            <option value="{{ $conta->id }}" @selected((int) old('conta_id') === $conta->id)>{{ $conta->nome }}@if ($admin) ({{ $conta->usuario->nome }})@endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1" for="valor">Valor (R$)</label>
                    <input class="form-control" id="valor" name="valor" inputmode="numeric" data-reais autocomplete="off" placeholder="0,00" value="{{ old('valor') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1" for="observacao">Observação</label>
                    <input class="form-control" id="observacao" name="observacao" maxlength="255" value="{{ old('observacao') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Incluir</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Receitas</strong><span class="lista-nota">{{ $receitas->count() }} neste mês</span></div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        @if ($admin)<th>De quem</th>@endif
                        <th class="text-end">Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($receitas as $receita)
                        <tr>
                            <td>{{ $receita->tipo->rotulo() }}</td>
                            <td>{{ $receita->descricao ?? '—' }}</td>
                            @if ($admin)<td>{{ $receita->usuario->nome }}</td>@endif
                            <td class="text-end text-nowrap">R$ {{ Dinheiro::br($receita->valor) }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('receitas.destroy', $receita) }}" class="d-inline" onsubmit="return confirm('Excluir esta receita?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="lista-vazia"><td colspan="{{ $admin ? 5 : 4 }}">Nenhuma receita neste mês.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <form method="POST" action="{{ route('receitas.store') }}" class="row g-2 align-items-end">
                @csrf
                <input type="hidden" name="mes" value="{{ $mes }}">
                <div class="col-md-2">
                    <label class="form-label small mb-1" for="tipo">Tipo</label>
                    <select class="form-select" id="tipo" name="tipo">
                        @foreach (TipoReceita::cases() as $tipo)
                            <option value="{{ $tipo->value }}">{{ $tipo->rotulo() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md">
                    <label class="form-label small mb-1" for="descricao">Descrição</label>
                    <input class="form-control" id="descricao" name="descricao" maxlength="255">
                </div>
                @if ($admin)
                    <div class="col-md-2">
                        <label class="form-label small mb-1" for="receita_usuario">De quem</label>
                        <select class="form-select" id="receita_usuario" name="usuario_id">
                            @foreach ($usuarios->where('ativo', true) as $usuario)
                                <option value="{{ $usuario->id }}" @selected(($filtroUsuario ?: auth()->id()) === $usuario->id)>{{ $usuario->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-2">
                    <label class="form-label small mb-1" for="receita_valor">Valor (R$)</label>
                    <input class="form-control" id="receita_valor" name="valor" inputmode="numeric" data-reais autocomplete="off" placeholder="0,00" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Incluir</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lançamento rápido do celular: botão fixo que abre o formulário por cima da tela. --}}
    <button type="button" class="btn btn-primary botao-lancar d-md-none" data-bs-toggle="offcanvas" data-bs-target="#lancar" aria-controls="lancar">+ Lançar gasto</button>

    <div class="offcanvas offcanvas-bottom folha-lancar" tabindex="-1" id="lancar" aria-labelledby="lancar-titulo"
        data-abrir="{{ request()->boolean('lancar') || old('origem') === 'rapido' ? '1' : '0' }}">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title fs-5" id="lancar-titulo">Lançar gasto em {{ Competencia::rotulo($competencia) }}</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body">
            @if ($contas->isEmpty())
                <p class="mb-3">Cadastre uma conta antes de lançar um gasto.</p>
                <a class="btn btn-primary w-100" href="{{ route('contas.create') }}">Nova conta</a>
            @else
                <form method="POST" action="{{ route('lancamentos.store') }}">
                    @csrf
                    <input type="hidden" name="mes" value="{{ $mes }}">
                    <input type="hidden" name="origem" value="rapido">
                    <div class="mb-3">
                        <label class="form-label" for="rapido_valor">Valor (R$)</label>
                        <input class="form-control form-control-lg" id="rapido_valor" name="valor" inputmode="numeric" data-reais autocomplete="off" placeholder="0,00" value="{{ old('origem') === 'rapido' ? old('valor') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="rapido_conta">Conta</label>
                        <select class="form-select form-select-lg" id="rapido_conta" name="conta_id" required>
                            <option value="">Escolha…</option>
                            @foreach ($contas as $conta)
                                <option value="{{ $conta->id }}" @selected(old('origem') === 'rapido' && (int) old('conta_id') === $conta->id)>{{ $conta->nome }}@if ($admin) ({{ $conta->usuario->nome }})@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="rapido_observacao">Observação <span class="text-muted">(opcional)</span></label>
                        <input class="form-control" id="rapido_observacao" name="observacao" maxlength="255" value="{{ old('origem') === 'rapido' ? old('observacao') : '' }}">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="rapido_pago" name="pago" value="1" @checked(old('origem') === 'rapido' && old('pago'))>
                        <label class="form-check-label" for="rapido_pago">Já está pago</label>
                    </div>
                    <button class="btn btn-primary btn-lg w-100">Incluir</button>
                </form>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const folha = document.getElementById('lancar');

            folha.addEventListener('shown.bs.offcanvas', () => document.getElementById('rapido_valor')?.focus());

            if (folha.dataset.abrir === '1') {
                // Tira o "?lancar=1" do endereço para a folha não reabrir depois de salvar.
                const endereco = new URL(location.href);
                endereco.searchParams.delete('lancar');
                history.replaceState(null, '', endereco);

                bootstrap.Offcanvas.getOrCreateInstance(folha).show();
            }
        })();
    </script>
@endpush
