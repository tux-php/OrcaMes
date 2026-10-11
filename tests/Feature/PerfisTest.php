<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Receita;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfisTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    private Usuario $membro;

    private Lancamento $doAdmin;

    private Lancamento $doMembro;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Usuario::factory()->admin()->create(['nome' => 'Ana']);
        $this->membro = Usuario::factory()->create(['nome' => 'Bia']);

        $this->doAdmin = Lancamento::factory()
            ->for(Conta::factory()->create(['usuario_id' => $this->admin->id, 'nome' => 'Cartão do Ana']))
            ->create();
        $this->doMembro = Lancamento::factory()
            ->for(Conta::factory()->create(['usuario_id' => $this->membro->id, 'nome' => 'Escola da Bia']))
            ->create();
    }

    public function test_membro_ve_so_os_proprios_lancamentos(): void
    {
        $this->actingAs($this->membro)->get('/?mes=2026-10')
            ->assertOk()
            ->assertSee('Escola da Bia')
            ->assertDontSee('Cartão do Ana');
    }

    public function test_membro_nao_amplia_a_visao_pela_url(): void
    {
        $this->actingAs($this->membro)->get('/?mes=2026-10&usuario='.$this->admin->id)
            ->assertOk()
            ->assertSee('Escola da Bia')
            ->assertDontSee('Cartão do Ana');

        $this->get('/contas?usuario='.$this->admin->id)->assertDontSee('Cartão do Ana');
        $this->get('/relatorio?ano=2026&usuario='.$this->admin->id)->assertOk();
    }

    public function test_admin_ve_todos_e_filtra_por_pessoa(): void
    {
        $this->actingAs($this->admin)->get('/?mes=2026-10')
            ->assertSee('Escola da Bia')
            ->assertSee('Cartão do Ana');

        $this->get('/?mes=2026-10&usuario='.$this->membro->id)
            ->assertSee('Escola da Bia')
            ->assertDontSee('Cartão do Ana');
    }

    public function test_membro_nao_mexe_no_que_e_de_outra_pessoa(): void
    {
        $this->actingAs($this->membro);
        $alheio = $this->doAdmin;

        $this->patch("/lancamentos/{$alheio->id}/pagar")->assertForbidden();
        $this->get("/lancamentos/{$alheio->id}/edit")->assertForbidden();
        $this->put("/lancamentos/{$alheio->id}", ['conta_id' => $alheio->conta_id, 'mes' => '2026-10', 'valor' => '1,00'])->assertForbidden();
        $this->delete("/lancamentos/{$alheio->id}")->assertForbidden();
        $this->get("/contas/{$alheio->conta_id}/edit")->assertForbidden();
        $this->put("/contas/{$alheio->conta_id}", ['nome' => 'Invadida'])->assertForbidden();
        $this->post('/lancamentos', ['conta_id' => $alheio->conta_id, 'mes' => '2026-10', 'valor' => '5,00'])->assertForbidden();

        $receita = Receita::factory()->create(['usuario_id' => $this->admin->id]);
        $this->delete("/receitas/{$receita->id}")->assertForbidden();

        $this->assertNull($alheio->refresh()->pago_em);
        $this->assertSame('100.00', $alheio->valor);
        $this->assertDatabaseCount('lancamentos', 2);
        $this->assertDatabaseHas('receitas', ['id' => $receita->id]);
    }

    public function test_membro_nao_grava_em_nome_de_outra_pessoa(): void
    {
        $this->actingAs($this->membro);

        $this->post('/receitas', ['mes' => '2026-10', 'tipo' => 'extra', 'valor' => '10,00', 'usuario_id' => $this->admin->id]);
        $this->post('/contas', ['nome' => 'Nova', 'ativa' => '1', 'usuario_id' => $this->admin->id]);
        $this->post('/meses/gerar', ['mes' => '2026-11', 'usuario_id' => $this->admin->id]);

        $this->assertDatabaseHas('receitas', ['usuario_id' => $this->membro->id, 'valor' => 10]);
        $this->assertDatabaseHas('contas', ['nome' => 'Nova', 'usuario_id' => $this->membro->id]);
        $this->assertSame(0, Lancamento::query()->dosUsuarios([$this->admin->id])->daCompetencia(now()->setDate(2026, 11, 1))->count());
    }

    public function test_membro_nao_troca_o_lancamento_para_a_conta_de_outra_pessoa(): void
    {
        $this->actingAs($this->membro)
            ->put("/lancamentos/{$this->doMembro->id}", ['conta_id' => $this->doAdmin->conta_id, 'mes' => '2026-10', 'valor' => '1,00'])
            ->assertSessionHasErrors('conta_id');
    }

    public function test_so_admin_gerencia_usuarios_e_categorias(): void
    {
        $categoria = Categoria::factory()->create();

        $this->actingAs($this->membro);
        $this->get('/usuarios')->assertForbidden();
        $this->post('/usuarios', ['nome' => 'X', 'email' => 'x@example.com', 'perfil' => 'admin'])->assertForbidden();
        $this->put("/usuarios/{$this->membro->id}", ['nome' => 'X', 'email' => $this->membro->email, 'perfil' => 'admin'])->assertForbidden();
        $this->get('/categorias')->assertForbidden();
        $this->delete("/categorias/{$categoria->id}")->assertForbidden();
        $this->assertFalse($this->membro->refresh()->isAdmin());

        $this->actingAs($this->admin);
        $this->get('/usuarios')->assertOk();
        $this->get('/categorias')->assertOk();
    }

    public function test_admin_promove_outro_usuario_a_admin(): void
    {
        $this->actingAs($this->admin)->put("/usuarios/{$this->membro->id}", [
            'nome' => 'Bia', 'email' => $this->membro->email, 'perfil' => 'admin', 'ativo' => '1',
        ])->assertRedirect('/usuarios');

        $this->assertTrue($this->membro->refresh()->isAdmin());
    }

    public function test_o_ultimo_admin_nao_pode_ser_rebaixado_nem_desativado(): void
    {
        $this->actingAs($this->admin);
        $dados = ['nome' => 'Ana', 'email' => $this->admin->email];

        $this->put("/usuarios/{$this->admin->id}", [...$dados, 'perfil' => 'membro', 'ativo' => '1'])->assertSessionHasErrors('perfil');
        $this->put("/usuarios/{$this->admin->id}", [...$dados, 'perfil' => 'admin'])->assertSessionHasErrors('perfil');

        $this->assertTrue($this->admin->refresh()->isAdmin());
        $this->assertTrue($this->admin->ativo);
    }

    public function test_usuario_novo_nasce_com_senha_provisoria(): void
    {
        $this->actingAs($this->admin)
            ->post('/usuarios', ['nome' => 'Novo', 'email' => 'novo@example.com', 'perfil' => 'membro'])
            ->assertRedirect('/usuarios')
            ->assertSessionHas('senha');

        $this->assertTrue(Usuario::query()->where('email', 'novo@example.com')->firstOrFail()->deve_trocar_senha);
    }
}
