<?php

namespace Tests\Feature;

use App\Models\ComprovanteIr;
use App\Models\Conta;
use App\Models\DeclaracaoIr;
use App\Models\Lancamento;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImpostoRendaTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    private Usuario $membro;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(ComprovanteIr::DISCO);
        $this->admin = Usuario::factory()->admin()->create(['nome' => 'Ana']);
        $this->membro = Usuario::factory()->create(['nome' => 'Bia']);
    }

    public function test_registra_e_substitui_o_resultado_da_declaracao(): void
    {
        $this->actingAs($this->membro);

        $this->post('/ir/declaracao', ['ano_base' => 2025, 'resultado' => 'a_restituir', 'valor' => '1.850,40', 'observacao' => '3º lote'])
            ->assertSessionHasNoErrors();
        $this->post('/ir/declaracao', ['ano_base' => 2025, 'resultado' => 'a_pagar', 'valor' => '320,00'])
            ->assertSessionHasNoErrors();

        $declaracao = DeclaracaoIr::query()->sole();
        $this->assertSame($this->membro->id, $declaracao->usuario_id);
        $this->assertSame('a_pagar', $declaracao->resultado->value);
        $this->assertSame('320.00', $declaracao->valor);

        $this->get('/ir?ano=2025')->assertOk()->assertSee('Imposto a pagar')->assertSee('320,00');
    }

    public function test_anexa_baixa_e_exclui_comprovante(): void
    {
        $this->actingAs($this->membro);

        $this->post('/ir/comprovantes', [
            'ano_base' => 2025,
            'descricao' => 'Recibo do dentista',
            'valor' => '450,00',
            'arquivo' => UploadedFile::fake()->create('recibo.pdf', 200, 'application/pdf'),
        ])->assertSessionHasNoErrors();

        $comprovante = ComprovanteIr::query()->sole();
        $this->assertSame('recibo.pdf', $comprovante->nome_original);
        $this->assertSame('450.00', $comprovante->valor);
        $this->assertStringStartsWith("comprovantes-ir/{$this->membro->id}/2025/", $comprovante->arquivo);
        Storage::disk(ComprovanteIr::DISCO)->assertExists($comprovante->arquivo);

        $this->get('/ir?ano=2025')->assertSee('Recibo do dentista')->assertSee('recibo.pdf');
        $this->get("/ir/comprovantes/{$comprovante->id}")->assertOk()->assertDownload('recibo.pdf');

        $this->delete("/ir/comprovantes/{$comprovante->id}")->assertSessionHas('ok');
        Storage::disk(ComprovanteIr::DISCO)->assertMissing($comprovante->arquivo);
        $this->assertDatabaseCount('comprovantes_ir', 0);
    }

    public function test_recusa_arquivo_de_tipo_ou_tamanho_invalido(): void
    {
        $this->actingAs($this->membro);
        $dados = ['ano_base' => 2025, 'descricao' => 'Teste'];

        $this->post('/ir/comprovantes', [...$dados, 'arquivo' => UploadedFile::fake()->create('virus.php', 10, 'text/x-php')])
            ->assertSessionHasErrors('arquivo');
        $this->post('/ir/comprovantes', [...$dados, 'arquivo' => UploadedFile::fake()->create('grande.pdf', 6000, 'application/pdf')])
            ->assertSessionHasErrors('arquivo');
        $this->post('/ir/comprovantes', $dados)->assertSessionHasErrors('arquivo');

        $this->assertDatabaseCount('comprovantes_ir', 0);
    }

    public function test_membro_nao_ve_nem_mexe_no_ir_de_outra_pessoa(): void
    {
        $declaracao = DeclaracaoIr::factory()->create(['usuario_id' => $this->admin->id, 'observacao' => 'segredo do Ana']);
        $comprovante = ComprovanteIr::factory()->create(['usuario_id' => $this->admin->id, 'descricao' => 'Informe do Ana']);
        Storage::disk(ComprovanteIr::DISCO)->put($comprovante->arquivo, 'conteudo');

        $this->actingAs($this->membro);

        $this->get('/ir?ano=2025&usuario='.$this->admin->id)->assertOk()
            ->assertDontSee('segredo do Ana')
            ->assertDontSee('Informe do Ana');
        $this->get("/ir/comprovantes/{$comprovante->id}")->assertForbidden();
        $this->delete("/ir/comprovantes/{$comprovante->id}")->assertForbidden();
        $this->delete("/ir/declaracao/{$declaracao->id}")->assertForbidden();

        $this->post('/ir/declaracao', ['ano_base' => 2025, 'resultado' => 'a_pagar', 'valor' => '1,00', 'usuario_id' => $this->admin->id]);
        $this->assertSame('1000.00', $declaracao->refresh()->valor, 'a declaração do admin não foi sobrescrita');
        $this->assertDatabaseHas('declaracoes_ir', ['usuario_id' => $this->membro->id]);

        Storage::disk(ComprovanteIr::DISCO)->assertExists($comprovante->arquivo);
    }

    public function test_admin_ve_tudo_e_lanca_em_nome_de_outra_pessoa(): void
    {
        ComprovanteIr::factory()->create(['usuario_id' => $this->membro->id, 'descricao' => 'Recibo da Bia']);

        $this->actingAs($this->admin);
        $this->get('/ir?ano=2025')->assertSee('Recibo da Bia');

        $this->post('/ir/declaracao', ['ano_base' => 2025, 'resultado' => 'a_restituir', 'valor' => '10,00', 'usuario_id' => $this->membro->id]);
        $this->assertDatabaseHas('declaracoes_ir', ['usuario_id' => $this->membro->id, 'ano_base' => 2025]);
    }

    public function test_lista_as_contas_dedutiveis_pagas_no_ano_base(): void
    {
        $plano = Conta::factory()->create(['usuario_id' => $this->membro->id, 'nome' => 'Plano de saúde', 'dedutivel_ir' => true]);
        $mercado = Conta::factory()->create(['usuario_id' => $this->membro->id, 'nome' => 'Mercado']);
        Lancamento::factory()->for($plano)->pago('2025-03-05')->create(['competencia' => '2025-03-01', 'valor' => '500.00']);
        Lancamento::factory()->for($plano)->create(['competencia' => '2025-04-01', 'valor' => '999.00']);
        Lancamento::factory()->for($mercado)->pago('2025-03-05')->create(['competencia' => '2025-03-01']);

        $dedutiveis = $this->actingAs($this->membro)->get('/ir?ano=2025')->viewData('dedutiveis');

        $this->assertSame(['Plano de saúde' => 500.0], $dedutiveis->all());
    }
}
