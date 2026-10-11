@extends('layouts.app')

@php
    use App\Enums\ResultadoIr;
    use App\Support\Dinheiro;

    $filtro = $filtroUsuario ? ['usuario' => $filtroUsuario] : [];
    $admin = auth()->user()->isAdmin();
@endphp

@section('titulo', "Imposto de Renda {$ano} — OrcaMes")

@section('conteudo')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('ir.index', ['ano' => $ano - 1] + $filtro) }}" aria-label="Ano anterior">&laquo;</a>
            <h1 class="h4 mb-0">Imposto de Renda — ano-base {{ $ano }}</h1>
            <a class="btn btn-outline-secondary" href="{{ route('ir.index', ['ano' => $ano + 1] + $filtro) }}" aria-label="Próximo ano">&raquo;</a>
        </div>
        @if ($admin)
            <form method="GET" action="{{ route('ir.index') }}">
                <input type="hidden" name="ano" value="{{ $ano }}">
                <select class="form-select" name="usuario" onchange="this.form.submit()" aria-label="Pessoa">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" @selected($filtroUsuario === $usuario->id)>{{ $usuario->nome }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>
    <p class="text-muted">A declaração de {{ $ano + 1 }} é feita no programa da Receita Federal. Aqui ficam o resultado e os comprovantes.</p>

    <div class="card mb-4">
        <div class="card-header"><strong>Resultado da declaração</strong></div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        @if ($admin)<th>De quem</th>@endif
                        <th>Resultado</th>
                        <th>Valor</th>
                        <th>Observação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($declaracoes as $declaracao)
                        <tr>
                            @if ($admin)<td>{{ $declaracao->usuario->nome }}</td>@endif
                            <td><span class="selo {{ $declaracao->resultado === ResultadoIr::ARestituir ? 'selo-ok' : 'selo-atencao' }}">{{ $declaracao->resultado->rotulo() }}</span></td>
                            <td class="text-nowrap fw-semibold">R$ {{ Dinheiro::br($declaracao->valor) }}</td>
                            <td>{{ $declaracao->observacao ?? '—' }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('ir.declaracao.excluir', $declaracao) }}" class="d-inline" onsubmit="return confirm('Excluir o resultado desta declaração?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="lista-vazia"><td colspan="{{ $admin ? 5 : 4 }}">Nenhum resultado registrado para {{ $ano }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <form method="POST" action="{{ route('ir.declaracao.salvar') }}" class="row g-2 align-items-end">
                @csrf
                <input type="hidden" name="ano_base" value="{{ $ano }}">
                <div class="col-md-3">
                    <label class="form-label small mb-1" for="resultado">Resultado</label>
                    <select class="form-select" id="resultado" name="resultado">
                        @foreach (ResultadoIr::cases() as $resultado)
                            <option value="{{ $resultado->value }}">{{ $resultado->rotulo() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1" for="declaracao_valor">Valor (R$)</label>
                    <input class="form-control" id="declaracao_valor" name="valor" inputmode="numeric" data-reais autocomplete="off" placeholder="0,00" required>
                </div>
                <div class="col-md">
                    <label class="form-label small mb-1" for="declaracao_observacao">Observação</label>
                    <input class="form-control" id="declaracao_observacao" name="observacao" maxlength="255" placeholder="Ex.: 8 cotas, 1ª em abril">
                </div>
                @if ($admin)
                    <div class="col-md-2">
                        <label class="form-label small mb-1" for="declaracao_usuario">De quem</label>
                        <select class="form-select" id="declaracao_usuario" name="usuario_id">
                            @foreach ($usuarios->where('ativo', true) as $usuario)
                                <option value="{{ $usuario->id }}" @selected(($filtroUsuario ?: auth()->id()) === $usuario->id)>{{ $usuario->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Salvar</button>
                </div>
            </form>
            <div class="form-text">Salvar de novo para a mesma pessoa substitui o resultado deste ano.</div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Comprovantes</strong><span class="lista-nota">{{ $comprovantes->count() }} anexados</span></div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        @if ($admin)<th>De quem</th>@endif
                        <th class="text-end">Valor</th>
                        <th>Arquivo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comprovantes as $comprovante)
                        <tr>
                            <td>{{ $comprovante->descricao }}</td>
                            @if ($admin)<td>{{ $comprovante->usuario->nome }}</td>@endif
                            <td class="text-end text-nowrap">{{ $comprovante->valor !== null ? 'R$ '.Dinheiro::br($comprovante->valor) : '—' }}</td>
                            <td><a href="{{ route('ir.comprovantes.baixar', $comprovante) }}">{{ $comprovante->nome_original }}</a></td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('ir.comprovantes.excluir', $comprovante) }}" class="d-inline" onsubmit="return confirm('Excluir este comprovante e o arquivo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="lista-vazia"><td colspan="{{ $admin ? 5 : 4 }}">Nenhum comprovante anexado para {{ $ano }}.</td></tr>
                    @endforelse
                </tbody>
                @if ($comprovantes->isNotEmpty())
                    <tfoot>
                        <tr class="fw-semibold">
                            <td colspan="{{ $admin ? 2 : 1 }}">Total dos comprovantes</td>
                            <td class="text-end text-nowrap">R$ {{ Dinheiro::br($comprovantes->sum(fn ($c) => Dinheiro::centavos($c->valor)) / 100) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        <div class="card-footer">
            <form method="POST" action="{{ route('ir.comprovantes.anexar') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
                @csrf
                <input type="hidden" name="ano_base" value="{{ $ano }}">
                <div class="col-md">
                    <label class="form-label small mb-1" for="comprovante_descricao">Descrição</label>
                    <input class="form-control" id="comprovante_descricao" name="descricao" maxlength="255" placeholder="Ex.: Recibo do dentista" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1" for="comprovante_valor">Valor (R$)</label>
                    <input class="form-control" id="comprovante_valor" name="valor" inputmode="numeric" data-reais autocomplete="off" placeholder="opcional">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1" for="arquivo">Arquivo (PDF, JPG ou PNG, até 5 MB)</label>
                    <input class="form-control" type="file" id="arquivo" name="arquivo" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                @if ($admin)
                    <div class="col-md-2">
                        <label class="form-label small mb-1" for="comprovante_usuario">De quem</label>
                        <select class="form-select" id="comprovante_usuario" name="usuario_id">
                            @foreach ($usuarios->where('ativo', true) as $usuario)
                                <option value="{{ $usuario->id }}" @selected(($filtroUsuario ?: auth()->id()) === $usuario->id)>{{ $usuario->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Anexar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Contas dedutíveis pagas em {{ $ano }}</strong><span class="lista-nota">para conferir com os comprovantes</span></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Conta</th>
                    <th>Total pago</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dedutiveis as $nome => $total)
                    <tr><td>{{ $nome }}</td><td class="text-end text-nowrap">R$ {{ Dinheiro::br($total) }}</td></tr>
                @empty
                    <tr class="lista-vazia"><td colspan="2">Marque uma conta como “Dedutível no Imposto de Renda” para ela aparecer aqui.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
