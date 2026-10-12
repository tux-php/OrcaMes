<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NomesTest extends TestCase
{
    use RefreshDatabase;

    public function test_formularios_padronizam_o_nome_digitado(): void
    {
        $this->actingAs($admin = Usuario::factory()->admin()->create());

        $this->post('/categorias', ['nome' => 'CARTÃO DE CRÉDITO']);
        $this->post('/contas', ['nome' => 'creche', 'ativa' => '1', 'usuario_id' => $admin->id]);
        $this->post('/contas', ['nome' => 'Cartão XP', 'ativa' => '1', 'usuario_id' => $admin->id]);

        $this->assertDatabaseHas('categorias', ['nome' => 'Cartão de Crédito']);
        $this->assertDatabaseHas('contas', ['nome' => 'Creche']);
        $this->assertDatabaseHas('contas', ['nome' => 'Cartão XP']);

        $conta = Conta::query()->where('nome', 'Creche')->sole();
        $this->put("/contas/{$conta->id}", ['nome' => 'ESCOLA', 'ativa' => '1']);
        $this->assertSame('Escola', $conta->refresh()->nome);
    }

    public function test_comando_so_mostra_e_com_aplicar_grava(): void
    {
        $categoria = Categoria::factory()->create(['nome' => 'DESPESA']);
        $conta = Conta::factory()->create(['nome' => 'CARTAO_BB']);
        $certa = Conta::factory()->create(['nome' => 'Vc Telecom']);

        $this->artisan('orcames:padronizar-nomes')
            ->expectsOutputToContain('Nada foi gravado')
            ->assertSuccessful();
        $this->assertSame('DESPESA', $categoria->refresh()->nome);
        $this->assertSame('CARTAO_BB', $conta->refresh()->nome);

        $this->artisan('orcames:padronizar-nomes', ['--aplicar' => true])
            ->expectsOutputToContain('1 categorias e 1 contas')
            ->assertSuccessful();
        $this->assertSame('Despesa', $categoria->refresh()->nome);
        $this->assertSame('Cartao_BB', $conta->refresh()->nome);
        $this->assertSame('Vc Telecom', $certa->refresh()->nome);
    }

    public function test_comando_nao_cria_categorias_com_nome_repetido(): void
    {
        // No SQLite dos testes a unicidade diferencia maiúsculas; no MySQL de produção, não.
        $maiuscula = Categoria::factory()->create(['nome' => 'CASA']);
        $pronta = Categoria::factory()->create(['nome' => 'Casa']);

        $this->artisan('orcames:padronizar-nomes', ['--aplicar' => true])
            ->expectsOutputToContain('não foi alterada')
            ->assertSuccessful();

        $this->assertSame('CASA', $maiuscula->refresh()->nome);
        $this->assertSame('Casa', $pronta->refresh()->nome);
    }
}
