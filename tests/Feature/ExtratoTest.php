<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class ExtratoTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    private Usuario $membro;

    /** @var array<string, mixed>|null */
    private ?array $dados = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Usuario::factory()->admin()->create(['nome' => 'Ana']);
        $this->membro = Usuario::factory()->create(['nome' => 'Bia']);

        // O PDF sai comprimido; os testes conferem o que a view recebeu.
        View::composer('relatorios.extrato-pagamentos', function ($view) {
            $this->dados = $view->getData();
        });
    }

    private function lancamento(Usuario $dono, string $competencia, string $valor, ?string $pagoEm = null, ?Categoria $categoria = null): Lancamento
    {
        $conta = Conta::factory()->create(['usuario_id' => $dono->id, 'categoria_id' => $categoria?->id]);

        return Lancamento::factory()->for($conta)->create([
            'competencia' => $competencia, 'valor' => $valor, 'pago_em' => $pagoEm,
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function idsNoExtrato(): array
    {
        return $this->dados['pagosPorMes']->collapse()->merge($this->dados['abertos'])->pluck('id')->sort()->values()->all();
    }

    public function test_baixa_pdf_do_periodo(): void
    {
        $this->lancamento($this->membro, '2026-03-01', '100.00', '2026-03-05');

        $this->actingAs($this->membro)
            ->get('/relatorio/pagamentos?de=2026-01&ate=2026-06')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('extrato-pagamentos-2026-01-a-2026-06.pdf');
    }

    public function test_separa_pago_de_aberto_e_respeita_o_periodo(): void
    {
        $categoria = Categoria::factory()->create(['nome' => 'Casa']);
        $pagoMarco = $this->lancamento($this->membro, '2026-03-01', '100.10', '2026-03-05', $categoria);
        $pagoAbril = $this->lancamento($this->membro, '2026-04-01', '50.20', '2026-04-02', $categoria);
        $aberto = $this->lancamento($this->membro, '2026-04-01', '30.00');
        $this->lancamento($this->membro, '2026-02-01', '999.00', '2026-02-10');
        $this->lancamento($this->membro, '2026-05-01', '999.00');

        $this->actingAs($this->membro)->get('/relatorio/pagamentos?de=2026-03&ate=2026-04')->assertOk();

        $this->assertSame(['2026-03', '2026-04'], $this->dados['pagosPorMes']->keys()->all());
        $this->assertSame([$pagoMarco->id], $this->dados['pagosPorMes']['2026-03']->pluck('id')->all());
        $this->assertSame([$pagoAbril->id], $this->dados['pagosPorMes']['2026-04']->pluck('id')->all());
        $this->assertSame([$aberto->id], $this->dados['abertos']->pluck('id')->all());
        $this->assertSame(['pago' => 150.3, 'aberto' => 30.0], $this->dados['totais']);
        $this->assertSame(['pago' => 150.3, 'aberto' => 0.0], $this->dados['porCategoria']['Casa']);
        $this->assertSame(['pago' => 0.0, 'aberto' => 30.0], $this->dados['porCategoria']['Sem categoria']);
        $this->assertSame('Março/2026 a Abril/2026', $this->dados['periodo']);
    }

    public function test_membro_ve_so_os_proprios_mesmo_pedindo_outro_usuario(): void
    {
        $doMembro = $this->lancamento($this->membro, '2026-03-01', '10.00', '2026-03-01');
        $this->lancamento($this->admin, '2026-03-01', '20.00', '2026-03-01');

        $this->actingAs($this->membro)
            ->get("/relatorio/pagamentos?de=2026-03&ate=2026-03&usuario={$this->admin->id}")
            ->assertOk();

        $this->assertSame([$doMembro->id], $this->idsNoExtrato());
        $this->assertSame('Bia', $this->dados['pessoa']);
        $this->assertFalse($this->dados['variasPessoas']);
    }

    public function test_admin_ve_todos_ou_filtra_um_usuario(): void
    {
        $doMembro = $this->lancamento($this->membro, '2026-03-01', '10.00', '2026-03-01');
        $doAdmin = $this->lancamento($this->admin, '2026-03-01', '20.00');

        $this->actingAs($this->admin)->get('/relatorio/pagamentos?de=2026-03&ate=2026-03')->assertOk();
        $this->assertSame([$doMembro->id, $doAdmin->id], $this->idsNoExtrato());
        $this->assertSame('Todos', $this->dados['pessoa']);
        $this->assertTrue($this->dados['variasPessoas']);

        $this->actingAs($this->admin)->get("/relatorio/pagamentos?de=2026-03&ate=2026-03&usuario={$this->membro->id}")->assertOk();
        $this->assertSame([$doMembro->id], $this->idsNoExtrato());
        $this->assertSame('Bia', $this->dados['pessoa']);
    }

    public function test_rejeita_periodo_invalido(): void
    {
        $this->actingAs($this->membro);

        $this->get('/relatorio/pagamentos?de=2026-05&ate=2026-03')->assertSessionHasErrors('ate');
        $this->get('/relatorio/pagamentos?de=abc&ate=2026-03')->assertSessionHasErrors('de');
        $this->get('/relatorio/pagamentos?de=2024-01&ate=2026-01')->assertSessionHasErrors('ate');
        $this->get('/relatorio/pagamentos?de=2024-02&ate=2026-01')->assertOk();
    }

    public function test_telas_levam_ao_pdf(): void
    {
        $this->actingAs($this->membro);

        $this->get('/?mes=2026-03')->assertOk()
            ->assertSee(route('relatorio.pagamentos', ['de' => '2026-03', 'ate' => '2026-03']));
        $this->get('/relatorio?ano=2025')->assertOk()
            ->assertSee('Extrato de pagamentos em PDF')
            ->assertSee('value="2025-01"', false)
            ->assertSee('value="2025-12"', false);
    }

    public function test_exige_login(): void
    {
        $this->get('/relatorio/pagamentos?de=2026-03&ate=2026-03')->assertRedirect('/login');
    }
}
