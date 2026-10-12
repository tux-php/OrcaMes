<?php

namespace Tests\Feature;

use App\Enums\TipoReceita;
use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Receita;
use App\Models\Usuario;
use App\Services\GeradorDeMes;
use App\Services\ResumoMensal;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MesTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario = Usuario::factory()->create();
        $this->actingAs($this->usuario);
    }

    private function conta(array $dados = []): Conta
    {
        return Conta::factory()->create(['usuario_id' => $this->usuario->id, ...$dados]);
    }

    public function test_inclui_lancamento_digitado_em_reais(): void
    {
        $conta = $this->conta();

        $this->post('/lancamentos', ['conta_id' => $conta->id, 'mes' => '2026-10', 'valor' => '1.234,56', 'observacao' => 'fatura'])
            ->assertSessionHasNoErrors();

        $lancamento = Lancamento::query()->sole();
        $this->assertSame('1234.56', $lancamento->valor);
        $this->assertSame('2026-10-01', $lancamento->competencia->toDateString());
        $this->assertFalse($lancamento->estaPago());
    }

    public function test_rejeita_valor_invalido(): void
    {
        $conta = $this->conta();

        foreach (['abc', '-5,00', '1.234', ''] as $valor) {
            $this->post('/lancamentos', ['conta_id' => $conta->id, 'mes' => '2026-10', 'valor' => $valor])
                ->assertSessionHasErrors('valor');
        }

        $this->assertDatabaseCount('lancamentos', 0);
    }

    public function test_paga_e_estorna(): void
    {
        $lancamento = Lancamento::factory()->for($this->conta())->create();

        $this->patch("/lancamentos/{$lancamento->id}/pagar");
        $this->assertSame(CarbonImmutable::today()->toDateString(), $lancamento->refresh()->pago_em->toDateString());

        $this->patch("/lancamentos/{$lancamento->id}/estornar");
        $this->assertNull($lancamento->refresh()->pago_em);
    }

    public function test_edita_lancamento(): void
    {
        $lancamento = Lancamento::factory()->for($this->conta())->create();
        $outraConta = $this->conta();

        $this->put("/lancamentos/{$lancamento->id}", [
            'conta_id' => $outraConta->id, 'mes' => '2026-11', 'valor' => '250,00', 'pago_em' => '2026-11-03',
        ])->assertRedirect(route('painel', ['mes' => '2026-11']));

        $lancamento->refresh();
        $this->assertSame($outraConta->id, $lancamento->conta_id);
        $this->assertSame('250.00', $lancamento->valor);
        $this->assertSame('2026-11-01', $lancamento->competencia->toDateString());
        $this->assertSame('2026-11-03', $lancamento->pago_em->toDateString());
    }

    public function test_resumo_do_mes(): void
    {
        $conta = $this->conta();
        Lancamento::factory()->for($conta)->pago()->create(['valor' => '300.10']);
        Lancamento::factory()->for($conta)->create(['valor' => '99.90']);
        Lancamento::factory()->for($conta)->pago()->create(['valor' => '1000.00', 'competencia' => '2026-09-01']);
        Lancamento::factory()->pago()->create(['valor' => '777.00']);
        Receita::factory()->create(['usuario_id' => $this->usuario->id, 'valor' => '1000.00']);
        Receita::factory()->create(['usuario_id' => $this->usuario->id, 'valor' => '250.00', 'tipo' => TipoReceita::Extra]);

        $this->assertSame([
            'receitas' => 1250.0,
            'pago' => 300.1,
            'aberto' => 99.9,
            'total' => 400.0,
            'sobra' => 949.9,
            'sobra_prevista' => 850.0,
        ], ResumoMensal::calcular([$this->usuario->id], CarbonImmutable::create(2026, 10, 1)));
    }

    public function test_gerar_mes_repete_as_recorrentes_com_o_ultimo_valor(): void
    {
        $escola = $this->conta(['nome' => 'Escola']);
        $avulsa = $this->conta(['nome' => 'Viagem', 'recorrente' => false]);
        $inativa = $this->conta(['nome' => 'Antiga', 'ativa' => false]);
        $nova = $this->conta(['nome' => 'Sem histórico']);
        $alheia = Conta::factory()->create();

        Lancamento::factory()->for($escola)->create(['competencia' => '2026-08-01', 'valor' => '900.00']);
        Lancamento::factory()->for($escola)->pago()->create(['competencia' => '2026-09-01', 'valor' => '950.00']);
        Lancamento::factory()->for($avulsa)->create(['competencia' => '2026-09-01']);
        Lancamento::factory()->for($inativa)->create(['competencia' => '2026-09-01']);
        Lancamento::factory()->for($alheia)->create(['competencia' => '2026-09-01']);
        Receita::factory()->create(['usuario_id' => $this->usuario->id, 'competencia' => '2026-09-01', 'valor' => '5000.00']);
        Receita::factory()->create(['usuario_id' => $this->usuario->id, 'competencia' => '2026-09-01', 'valor' => '800.00', 'tipo' => TipoReceita::Extra]);

        $outubro = CarbonImmutable::create(2026, 10, 1);
        $this->post('/meses/gerar', ['mes' => '2026-10'])->assertSessionHas('ok');

        $gerados = Lancamento::query()->daCompetencia($outubro)->get()->keyBy('conta_id');
        $this->assertEqualsCanonicalizing([$escola->id, $nova->id], $gerados->keys()->all());
        $this->assertSame('950.00', $gerados[$escola->id]->valor);
        $this->assertNull($gerados[$escola->id]->pago_em, 'o mês novo começa em aberto');
        $this->assertSame('0.00', $gerados[$nova->id]->valor);

        $receitas = Receita::query()->daCompetencia($outubro)->get();
        $this->assertCount(1, $receitas, 'só o salário é repetido; extra não');
        $this->assertSame('5000.00', $receitas[0]->valor);
    }

    public function test_gerar_mes_duas_vezes_nao_duplica(): void
    {
        $conta = $this->conta();
        Lancamento::factory()->for($conta)->create(['competencia' => '2026-09-01']);
        Receita::factory()->create(['usuario_id' => $this->usuario->id, 'competencia' => '2026-09-01']);
        $outubro = CarbonImmutable::create(2026, 10, 1);

        GeradorDeMes::gerar($this->usuario, $outubro);
        $segunda = GeradorDeMes::gerar($this->usuario, $outubro);

        $this->assertSame(['lancamentos' => 0, 'receitas' => 0], $segunda);
        $this->assertSame(1, Lancamento::query()->daCompetencia($outubro)->count());
        $this->assertSame(1, Receita::query()->daCompetencia($outubro)->count());
    }

    public function test_conta_com_lancamentos_nao_e_excluida(): void
    {
        $usada = $this->conta();
        $vazia = $this->conta();
        Lancamento::factory()->for($usada)->create();

        $this->delete("/contas/{$usada->id}")->assertSessionHas('erro');
        $this->delete("/contas/{$vazia->id}")->assertSessionHas('ok');

        $this->assertDatabaseHas('contas', ['id' => $usada->id]);
        $this->assertDatabaseMissing('contas', ['id' => $vazia->id]);
    }

    public function test_painel_anual_compara_com_o_mesmo_periodo_do_ano_anterior(): void
    {
        $conta = $this->conta();
        Lancamento::factory()->for($conta)->create(['competencia' => '2026-01-01', 'valor' => '100.00']);
        Lancamento::factory()->for($conta)->pago()->create(['competencia' => '2026-02-01', 'valor' => '200.00']);
        Lancamento::factory()->for($conta)->create(['competencia' => '2025-01-01', 'valor' => '100.00']);
        Lancamento::factory()->for($conta)->create(['competencia' => '2025-02-01', 'valor' => '100.00']);
        Lancamento::factory()->for($conta)->create(['competencia' => '2025-12-01', 'valor' => '5000.00']);

        $resposta = $this->get('/relatorio?ano=2026')->assertOk();

        $this->assertSame(
            ['total' => 300.0, 'pago' => 200.0, 'aberto' => 100.0, 'media' => 150.0, 'variacao' => 50.0],
            $resposta->viewData('indicadores'),
            'dezembro de 2025 fica fora: 2026 só tem janeiro e fevereiro',
        );
        $this->assertSame(5000.0, $resposta->viewData('graficos')['anterior'][11]);
    }

    public function test_painel_anual_junta_as_categorias_menores_em_outras(): void
    {
        foreach (range(1, 10) as $n) {
            $categoria = \App\Models\Categoria::factory()->create(['nome' => "Categoria {$n}"]);
            Lancamento::factory()->for($this->conta(['categoria_id' => $categoria->id]))->create(['valor' => $n * 10]);
        }

        $graficos = $this->get('/relatorio?ano=2026')->viewData('graficos');

        $this->assertCount(8, $graficos['categorias']);
        $this->assertSame('Categoria 10', $graficos['categorias'][0]);
        $this->assertSame('Outras', $graficos['categorias'][7]);
        $this->assertSame(60.0, $graficos['porCategoria'][7], '10 + 20 + 30');
    }

    public function test_listas_de_contas_e_categorias_sao_paginadas(): void
    {
        foreach (range(1, 25) as $n) {
            $this->conta(['nome' => sprintf('Conta %02d', $n)]);
            \App\Models\Categoria::factory()->create(['nome' => sprintf('Categoria %02d', $n)]);
        }

        $this->get('/contas')->assertSee('Conta 10')->assertDontSee('Conta 11')->assertSee('Mostrando');
        $this->get('/contas?page=2')->assertSee('Conta 11')->assertDontSee('Conta 10');

        $this->actingAs(Usuario::factory()->admin()->create());
        $this->get('/categorias')->assertSee('Categoria 10')->assertDontSee('Categoria 11');
        $this->get('/categorias?page=3')->assertSee('Categoria 25');
    }

    public function test_contas_podem_ser_filtradas_por_categoria(): void
    {
        $casa = \App\Models\Categoria::factory()->create(['nome' => 'Casa']);
        $this->conta(['nome' => 'Aluguel', 'categoria_id' => $casa->id]);
        $this->conta(['nome' => 'Academia']);

        $this->get('/contas?categoria='.$casa->id)->assertSee('Aluguel')->assertDontSee('Academia')->assertSee('Ver todas');
        $this->get('/contas')->assertSee('Aluguel')->assertSee('Academia')->assertDontSee('Ver todas');

        $this->actingAs(Usuario::factory()->admin()->create());
        $this->get('/categorias')->assertSee('1 conta')->assertSee('contas?categoria='.$casa->id, false);
    }

    public function test_telas_abrem(): void
    {
        Lancamento::factory()->for($this->conta(['dedutivel_ir' => true]))->pago()->create();

        $this->get('/?mes=2026-10')->assertOk()->assertSee('Outubro/2026')->assertSee('data-reais')->assertSee('js/mascara-reais.js');
        $this->get('/contas')->assertOk();
        $this->get('/contas/create')->assertOk();
        $this->get('/relatorio?ano=2026')->assertOk()->assertSee('Painel de 2026')->assertSee('grafico-meses');
        $this->get('/relatorio?ano=1999')->assertOk()->assertSee('Nenhum lançamento em 1999')->assertDontSee('grafico-meses');
        $this->get('/ir?ano=2026')->assertOk()->assertSee('Contas dedutíveis pagas em 2026');
        $this->get('/senha')->assertOk();
    }
}
