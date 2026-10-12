@extends('layouts.app')

@php
    use App\Support\Competencia;
    use App\Support\Dinheiro;

    $filtro = $filtroUsuario ? ['usuario' => $filtroUsuario] : [];
    $colunas = ['receitas' => 'Receitas', 'pago' => 'Pago', 'aberto' => 'Em aberto', 'sobra_prevista' => 'Sobra prevista'];
    $variacao = $indicadores['variacao'];
@endphp

@section('titulo', "Painel de {$ano} — OrcaMes")

@push('estilos')
    <style>
        .painel { --tinta: #0b0b0b; --tinta-2: #52514e; --tinta-3: #898781; --serie-1: #2a78d6; --serie-2: #eb6834; --serie-apoio: #898781; }
        .painel .indicador-rotulo { color: var(--tinta-2); font-size: .875rem; }
        .painel .indicador-valor { color: var(--tinta); font-size: 1.5rem; font-weight: 600; line-height: 1.2; }
        .painel .indicador-principal .indicador-valor { font-size: 2.25rem; }
        .painel .indicador-nota { color: var(--tinta-2); font-size: .8125rem; }
        .painel .subiu { color: #b42318; }
        .painel .desceu { color: #006300; }
        .painel .grafico { position: relative; height: 300px; }
        .painel .grafico-titulo { color: var(--tinta); font-weight: 600; }
        .painel .grafico-nota { color: var(--tinta-2); font-size: .8125rem; }
        .painel table.numeros td, .painel table.numeros th { font-variant-numeric: tabular-nums; }
    </style>
@endpush

@section('conteudo')
<div class="painel">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
        <a class="btn btn-outline-secondary" href="{{ route('relatorio', ['ano' => $ano - 1] + $filtro) }}" aria-label="Ano anterior">&laquo;</a>
        <h1 class="h4 mb-0">Painel de {{ $ano }}</h1>
        <a class="btn btn-outline-secondary" href="{{ route('relatorio', ['ano' => $ano + 1] + $filtro) }}" aria-label="Próximo ano">&raquo;</a>
        @can('admin')
            <form method="GET" action="{{ route('relatorio') }}" class="ms-2">
                <input type="hidden" name="ano" value="{{ $ano }}">
                <select class="form-select" name="usuario" onchange="this.form.submit()" aria-label="Pessoa">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" @selected($filtroUsuario === $usuario->id)>{{ $usuario->nome }}</option>
                    @endforeach
                </select>
            </form>
        @endcan
    </div>

    @if ($indicadores['total'] <= 0)
        <div class="card card-body text-center text-muted py-5">Nenhum lançamento em {{ $ano }}.</div>
    @else
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-5">
                <div class="card h-100 indicador-principal">
                    <div class="card-body">
                        <div class="indicador-rotulo">Despesas lançadas no ano</div>
                        <div class="indicador-valor contagem" data-contagem="{{ $indicadores['total'] }}">R$ {{ Dinheiro::br($indicadores['total']) }}</div>
                        @if ($variacao !== null)
                            <div class="indicador-nota">
                                <span class="{{ $variacao > 0 ? 'subiu' : 'desceu' }} fw-semibold">{{ $variacao > 0 ? '▲' : '▼' }} {{ number_format(abs($variacao), 1, ',', '.') }}%</span>
                                {{ $variacao > 0 ? 'a mais' : 'a menos' }} que no mesmo período de {{ $ano - 1 }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @foreach ([
                ['Pago', $indicadores['pago'], null],
                ['Em aberto', $indicadores['aberto'], null],
                ['Média por mês', $indicadores['media'], 'nos meses com lançamentos'],
            ] as [$rotulo, $valor, $nota])
                <div class="col-6 col-lg">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="indicador-rotulo">{{ $rotulo }}</div>
                            <div class="indicador-valor contagem" data-contagem="{{ $valor }}">R$ {{ Dinheiro::br($valor) }}</div>
                            @if ($nota)<div class="indicador-nota">{{ $nota }}</div>@endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-xl-7">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="grafico-titulo">Despesas por mês</div>
                        <div class="grafico-nota mb-2">Pago e em aberto, em reais</div>
                        <div class="grafico"><canvas id="grafico-meses" role="img" aria-label="Colunas empilhadas com o valor pago e em aberto em cada mês de {{ $ano }}. Os números estão na tabela ao fim da página."></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-5">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="grafico-titulo">{{ $graficos['temAnterior'] ? "{$ano} comparado a ".($ano - 1) : "Evolução em {$ano}" }}</div>
                        <div class="grafico-nota mb-2">Total lançado por mês, em reais</div>
                        <div class="grafico"><canvas id="grafico-comparativo" role="img" aria-label="Linhas com o total lançado por mês em {{ $ano }} e em {{ $ano - 1 }}."></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="grafico-titulo">Para onde foi o dinheiro</div>
                <div class="grafico-nota mb-2">Total lançado por categoria em {{ $ano }}</div>
                <div class="grafico" style="height: {{ 48 + 34 * count($graficos['categorias']) }}px"><canvas id="grafico-categorias" role="img" aria-label="Barras com o total lançado em cada categoria em {{ $ano }}; o valor aparece na ponta de cada barra."></canvas></div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><strong>Mês a mês</strong><span class="lista-nota">valores em reais</span></div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mês</th>
                        @foreach ($colunas as $rotulo)
                            <th class="text-end @if($loop->last) pe-3 @endif">{{ $rotulo }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($meses as $numero => $resumo)
                        <tr>
                            <td><a href="{{ route('painel', ['mes' => sprintf('%d-%02d', $ano, $numero)] + $filtro) }}">{{ Competencia::nomeDoMes($numero) }}</a></td>
                            @foreach ($colunas as $chave => $rotulo)
                                <td class="text-end text-nowrap @if($loop->last) pe-3 @endif @if($resumo[$chave] < 0) text-danger @endif">{{ Dinheiro::br($resumo[$chave]) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-semibold">
                        <td>Total</td>
                        @foreach ($colunas as $chave => $rotulo)
                            <td class="text-end text-nowrap @if($loop->last) pe-3 @endif">{{ Dinheiro::br(collect($meses)->sum($chave)) }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@if ($indicadores['total'] > 0)
    @push('scripts')
        <script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
        <script>
            (() => {
                const dados = {{ Js::from($graficos) }};
                const MESES = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
                const cor = { tinta: '#0b0b0b', tinta2: '#52514e', tinta3: '#898781', grade: '#e1e0d9', eixo: '#c3c2b7', fundo: '#ffffff', serie1: '#2a78d6', serie2: '#eb6834', apoio: '#898781' };
                const reais = (v) => v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const compacto = (v) => v >= 1000 ? (v / 1000).toLocaleString('pt-BR', { maximumFractionDigits: 1 }) + ' mil' : v.toLocaleString('pt-BR');

                Chart.defaults.font.family = 'system-ui, -apple-system, "Segoe UI", sans-serif';
                Chart.defaults.color = cor.tinta2;
                Chart.defaults.maintainAspectRatio = false;

                const eixoDeValor = { beginAtZero: true, border: { display: false }, grid: { color: cor.grade }, ticks: { color: cor.tinta3, maxTicksLimit: 6, callback: compacto } };
                const eixoDeMes = { grid: { display: false }, border: { color: cor.eixo }, ticks: { color: cor.tinta3 } };
                const dica = { backgroundColor: cor.fundo, titleColor: cor.tinta2, bodyColor: cor.tinta, footerColor: cor.tinta, borderColor: cor.grade, borderWidth: 1, padding: 10, boxPadding: 4, usePointStyle: true };
                const legenda = (estilo) => ({ position: 'top', align: 'start', labels: { color: cor.tinta2, usePointStyle: true, pointStyle: estilo, boxWidth: 10, boxHeight: 10 } });

                // Linha vertical no mês apontado: mira-se na data, não na linha de 2 px.
                const mira = {
                    id: 'mira',
                    afterDatasetsDraw(chart) {
                        const ativos = chart.tooltip?.getActiveElements() ?? [];
                        if (!ativos.length) return;
                        const { ctx, chartArea } = chart;
                        ctx.save();
                        ctx.strokeStyle = cor.eixo;
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(ativos[0].element.x, chartArea.top);
                        ctx.lineTo(ativos[0].element.x, chartArea.bottom);
                        ctx.stroke();
                        ctx.restore();
                    },
                };

                // Valor na ponta de cada barra, na cor do texto e nunca na da série.
                const valorNaPonta = {
                    id: 'valorNaPonta',
                    afterDatasetsDraw(chart) {
                        const { ctx } = chart;
                        ctx.save();
                        ctx.fillStyle = cor.tinta;
                        ctx.font = '12px ' + Chart.defaults.font.family;
                        ctx.textBaseline = 'middle';
                        chart.getDatasetMeta(0).data.forEach((barra, i) => ctx.fillText(reais(chart.data.datasets[0].data[i]), barra.x + 8, barra.y));
                        ctx.restore();
                    },
                };

                const topo = { topLeft: 4, topRight: 4 };
                new Chart(document.getElementById('grafico-meses'), {
                    type: 'bar',
                    data: {
                        labels: MESES,
                        datasets: [
                            { label: 'Pago', data: dados.pago, backgroundColor: cor.serie1, maxBarThickness: 24, borderRadius: (c) => dados.aberto[c.dataIndex] > 0 ? 0 : topo },
                            { label: 'Em aberto', data: dados.aberto, backgroundColor: cor.serie2, maxBarThickness: 24, borderRadius: topo, borderSkipped: false, borderColor: cor.fundo, borderWidth: { top: 0, right: 0, bottom: 2, left: 0 } },
                        ],
                    },
                    options: {
                        interaction: { mode: 'index', intersect: false },
                        scales: { x: { ...eixoDeMes, stacked: true }, y: { ...eixoDeValor, stacked: true } },
                        plugins: {
                            legend: legenda('rect'),
                            tooltip: { ...dica, callbacks: {
                                title: (itens) => MESES[itens[0].dataIndex] + '/' + dados.ano,
                                label: (item) => ' ' + reais(item.parsed.y) + '  ' + item.dataset.label,
                                footer: (itens) => 'Total ' + reais(dados.total[itens[0].dataIndex]),
                            } },
                        },
                    },
                });

                const ultimoMes = dados.total.reduce((ultimo, valor, i) => valor > 0 ? i : ultimo, -1);
                const linha = (rotulo, valores, corDaLinha) => ({ label: rotulo, data: valores, borderColor: corDaLinha, backgroundColor: corDaLinha, borderWidth: 2, tension: 0, pointRadius: 0, pointHoverRadius: 5, pointHoverBorderColor: cor.fundo, pointHoverBorderWidth: 2 });
                new Chart(document.getElementById('grafico-comparativo'), {
                    type: 'line',
                    data: {
                        labels: MESES,
                        datasets: [
                            linha(String(dados.ano), dados.total.map((v, i) => i > ultimoMes ? null : v), cor.serie1),
                            ...(dados.temAnterior ? [linha(String(dados.ano - 1), dados.anterior, cor.apoio)] : []),
                        ],
                    },
                    options: {
                        interaction: { mode: 'index', intersect: false },
                        scales: { x: eixoDeMes, y: eixoDeValor },
                        plugins: {
                            legend: { ...legenda('line'), display: dados.temAnterior },
                            tooltip: { ...dica, callbacks: { label: (item) => ' ' + reais(item.parsed.y) + '  ' + item.dataset.label } },
                        },
                    },
                    plugins: [mira],
                });

                new Chart(document.getElementById('grafico-categorias'), {
                    type: 'bar',
                    data: {
                        labels: dados.categorias,
                        datasets: [{ label: 'Total lançado', data: dados.porCategoria, backgroundColor: cor.serie1, maxBarThickness: 18, borderRadius: { topRight: 4, bottomRight: 4 } }],
                    },
                    options: {
                        indexAxis: 'y',
                        layout: { padding: { right: 110 } },
                        scales: {
                            x: { ...eixoDeValor, ticks: { ...eixoDeValor.ticks, maxTicksLimit: 5 } },
                            y: { grid: { display: false }, border: { color: cor.eixo }, ticks: { color: cor.tinta2 } },
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: { ...dica, displayColors: false, callbacks: { label: (item) => reais(item.parsed.x) } },
                        },
                    },
                    plugins: [valorNaPonta],
                });
            })();
        </script>
    @endpush
@endif
