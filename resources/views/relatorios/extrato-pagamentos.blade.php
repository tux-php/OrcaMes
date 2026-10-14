@php
    use App\Support\Competencia;
    use App\Support\Dinheiro;

    $colunas = $variasPessoas ? 5 : 4;
@endphp
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Extrato de pagamentos — {{ $periodo }}</title>
    <style>
        @page { margin: 22mm 16mm 20mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #0b0b0b; }
        h1 { font-size: 15pt; margin: 0 0 2mm; }
        h2 { font-size: 11pt; margin: 7mm 0 2mm; }
        .apoio { color: #52514e; }
        .rodape { position: fixed; bottom: -12mm; left: 0; font-size: 8pt; color: #52514e; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 8pt; color: #52514e; font-weight: normal; border-bottom: 1px solid #898781; padding: 1.5mm 2mm; }
        td { padding: 1.5mm 2mm; border-bottom: 1px solid #e1e0d9; vertical-align: top; }
        .valor { text-align: right; white-space: nowrap; }
        .data { white-space: nowrap; }
        .obs { font-size: 7.5pt; color: #52514e; }
        tr.mes td { background: #f2f1ec; font-weight: bold; border-bottom: 1px solid #c3c2b7; }
        tr.subtotal td { font-weight: bold; border-bottom: none; padding-bottom: 3mm; }
        tfoot td { font-weight: bold; border-top: 1px solid #898781; border-bottom: none; }
        .resumo td { border: 1px solid #c3c2b7; padding: 2.5mm 3mm; width: 33%; }
        .resumo .rotulo { font-size: 8pt; color: #52514e; }
        .resumo .numero { font-size: 13pt; font-weight: bold; }
        .vazio { color: #52514e; padding: 3mm 0; }
    </style>
</head>
<body>
    <div class="rodape">OrcaMes · Extrato de pagamentos · {{ $periodo }}</div>

    <h1>Extrato de pagamentos</h1>
    <div>Período: <strong>{{ $periodo }}</strong> (pelo mês de referência) · Pessoa: <strong>{{ $pessoa }}</strong></div>
    <div class="apoio">Gerado em {{ $geradoEm->format('d/m/Y \à\s H:i') }} por {{ $geradoPor }}</div>

    <table class="resumo" style="margin-top: 5mm">
        <tr>
            <td><div class="rotulo">Pago</div><div class="numero">R$ {{ Dinheiro::br($totais['pago']) }}</div></td>
            <td><div class="rotulo">Em aberto</div><div class="numero">R$ {{ Dinheiro::br($totais['aberto']) }}</div></td>
            <td><div class="rotulo">Total lançado</div><div class="numero">R$ {{ Dinheiro::br($totais['pago'] + $totais['aberto']) }}</div></td>
        </tr>
    </table>

    <h2>Pagamentos</h2>
    @if ($pagosPorMes->isEmpty())
        <div class="vazio">Nenhum pagamento no período.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Pago em</th>
                    <th>Conta</th>
                    <th>Categoria</th>
                    @if ($variasPessoas)<th>Pessoa</th>@endif
                    <th class="valor">Valor (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pagosPorMes as $mes => $lancamentos)
                    <tr class="mes"><td colspan="{{ $colunas }}">{{ Competencia::rotulo(Competencia::deTexto($mes)) }}</td></tr>
                    @foreach ($lancamentos as $lancamento)
                        <tr>
                            <td class="data">{{ $lancamento->pago_em->format('d/m/Y') }}</td>
                            <td>
                                {{ $lancamento->conta->nome }}
                                @if ($lancamento->observacao)<div class="obs">{{ $lancamento->observacao }}</div>@endif
                            </td>
                            <td>{{ $lancamento->conta->categoria?->nome ?? 'Sem categoria' }}</td>
                            @if ($variasPessoas)<td>{{ $lancamento->conta->usuario->nome }}</td>@endif
                            <td class="valor">{{ Dinheiro::br($lancamento->valor) }}</td>
                        </tr>
                    @endforeach
                    @if ($pagosPorMes->count() > 1)
                        <tr class="subtotal">
                            <td colspan="{{ $colunas - 1 }}">Subtotal de {{ Competencia::rotulo(Competencia::deTexto($mes)) }}</td>
                            <td class="valor">{{ Dinheiro::br($lancamentos->sum(fn ($l) => Dinheiro::centavos($l->valor)) / 100) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ $colunas - 1 }}">Total pago</td>
                    <td class="valor">{{ Dinheiro::br($totais['pago']) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <h2>Em aberto</h2>
    @if ($abertos->isEmpty())
        <div class="vazio">Nada em aberto no período.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Referência</th>
                    <th>Conta</th>
                    <th>Categoria</th>
                    @if ($variasPessoas)<th>Pessoa</th>@endif
                    <th class="valor">Valor (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($abertos as $lancamento)
                    <tr>
                        <td class="data">{{ Competencia::rotulo($lancamento->competencia) }}</td>
                        <td>
                            {{ $lancamento->conta->nome }}
                            @if ($lancamento->observacao)<div class="obs">{{ $lancamento->observacao }}</div>@endif
                        </td>
                        <td>{{ $lancamento->conta->categoria?->nome ?? 'Sem categoria' }}</td>
                        @if ($variasPessoas)<td>{{ $lancamento->conta->usuario->nome }}</td>@endif
                        <td class="valor">{{ Dinheiro::br($lancamento->valor) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ $colunas - 1 }}">Total em aberto</td>
                    <td class="valor">{{ Dinheiro::br($totais['aberto']) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($porCategoria->isNotEmpty())
        <h2>Por categoria</h2>
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th class="valor">Pago (R$)</th>
                    <th class="valor">Em aberto (R$)</th>
                    <th class="valor">Total (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($porCategoria as $categoria => $valores)
                    <tr>
                        <td>{{ $categoria }}</td>
                        <td class="valor">{{ Dinheiro::br($valores['pago']) }}</td>
                        <td class="valor">{{ Dinheiro::br($valores['aberto']) }}</td>
                        <td class="valor">{{ Dinheiro::br($valores['pago'] + $valores['aberto']) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="valor">{{ Dinheiro::br($totais['pago']) }}</td>
                    <td class="valor">{{ Dinheiro::br($totais['aberto']) }}</td>
                    <td class="valor">{{ Dinheiro::br($totais['pago'] + $totais['aberto']) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif
</body>
</html>
