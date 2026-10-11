<?php

namespace Tests\Feature;

use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportarLegadoTest extends TestCase
{
    use RefreshDatabase;

    private const DUMP = __DIR__.'/../fixtures/legado.sql';

    public function test_importa_o_dump_para_o_modelo_novo(): void
    {
        $this->artisan('orcames:importar', ['arquivo' => self::DUMP, '--admin' => 'ana@example.com'])->assertSuccessful();

        $ana = Usuario::query()->where('email', 'ana@example.com')->sole();
        $bia = Usuario::query()->where('email', 'bia@example.com')->sole();

        $this->assertDatabaseCount('usuarios', 2);
        $this->assertTrue($ana->isAdmin());
        $this->assertFalse($bia->isAdmin());
        $this->assertTrue($ana->deve_trocar_senha);

        $this->assertDatabaseCount('categorias', 2);
        $this->assertDatabaseCount('contas', 4);
        $this->assertDatabaseCount('lancamentos', 4);
        $this->assertDatabaseCount('receitas', 3);

        $aluguel = Conta::query()->where('nome', 'Aluguel')->sole();
        $this->assertSame($ana->id, $aluguel->usuario_id);
        $this->assertSame('CASA', $aluguel->categoria->nome);

        $this->assertEquals(3130.0, Lancamento::query()->dosUsuarios([$ana->id])->sum('valor'));
        $this->assertEquals(1500.0, Lancamento::query()->dosUsuarios([$ana->id])->whereNotNull('pago_em')->sum('valor'));
        $this->assertEquals(700.5, Lancamento::query()->dosUsuarios([$bia->id])->sum('valor'));
    }

    public function test_exige_um_admin_valido_e_um_banco_vazio(): void
    {
        $this->artisan('orcames:importar', ['arquivo' => self::DUMP, '--admin' => 'ninguem@example.com'])->assertFailed();
        $this->assertDatabaseCount('usuarios', 0);

        Usuario::factory()->create();
        $this->artisan('orcames:importar', ['arquivo' => self::DUMP, '--admin' => 'ana@example.com'])->assertFailed();
        $this->assertDatabaseCount('lancamentos', 0);
    }
}
