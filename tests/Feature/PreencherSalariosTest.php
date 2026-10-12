<?php

namespace Tests\Feature;

use App\Enums\TipoReceita;
use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Receita;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreencherSalariosTest extends TestCase
{
    use RefreshDatabase;

    private function lancamento(Usuario $usuario, string $competencia): void
    {
        Lancamento::factory()->create([
            'conta_id' => Conta::factory()->create(['usuario_id' => $usuario->id])->id,
            'competencia' => $competencia,
        ]);
    }

    /**
     * @return list<string>
     */
    private function mesesComSalario(Usuario $usuario): array
    {
        return Receita::query()->where('usuario_id', $usuario->id)->where('tipo', TipoReceita::Salario)
            ->get()->map(fn (Receita $r) => $r->competencia->toDateString())->sort()->values()->all();
    }

    public function test_comando_so_mostra_e_com_aplicar_grava(): void
    {
        $usuario = Usuario::factory()->create();
        Receita::factory()->create(['usuario_id' => $usuario->id, 'competencia' => '2026-10-01', 'valor' => '5000.00']);
        Receita::factory()->create(['usuario_id' => $usuario->id, 'competencia' => '2026-08-01', 'valor' => '4000.00']);
        Receita::factory()->create(['usuario_id' => $usuario->id, 'competencia' => '2026-07-01', 'tipo' => TipoReceita::Extra, 'valor' => '300.00']);

        // Julho só tem extra, agosto já tem salário, setembro não tem nada e novembro é posterior ao modelo.
        foreach (['2026-07-01', '2026-08-01', '2026-09-01', '2026-09-01', '2026-10-01', '2026-11-01'] as $competencia) {
            $this->lancamento($usuario, $competencia);
        }

        $this->artisan('orcames:preencher-salarios')
            ->expectsOutputToContain('Nada foi gravado')
            ->assertSuccessful();
        $this->assertSame(['2026-08-01', '2026-10-01'], $this->mesesComSalario($usuario));

        $this->artisan('orcames:preencher-salarios', ['--aplicar' => true])
            ->expectsOutputToContain('2 receitas de salário criadas')
            ->assertSuccessful();
        $this->assertSame(['2026-07-01', '2026-08-01', '2026-09-01', '2026-10-01'], $this->mesesComSalario($usuario));

        $setembro = Receita::query()->whereDate('competencia', '2026-09-01')->sole();
        $this->assertSame('5000.00', $setembro->valor);
        $this->assertSame('Salário (estimado)', $setembro->descricao);
        $this->assertSame('4000.00', Receita::query()->whereDate('competencia', '2026-08-01')->sole()->valor);

        $this->artisan('orcames:preencher-salarios', ['--aplicar' => true])
            ->expectsOutputToContain('Nenhum mês a preencher')
            ->assertSuccessful();
        $this->assertDatabaseCount('receitas', 5);
    }

    public function test_cada_usuario_recebe_o_proprio_salario_e_quem_nao_tem_fica_de_fora(): void
    {
        $comSalario = Usuario::factory()->create();
        $semSalario = Usuario::factory()->create();
        Receita::factory()->create(['usuario_id' => $comSalario->id, 'competencia' => '2026-10-01', 'valor' => '5000.00']);

        $this->lancamento($comSalario, '2026-09-01');
        $this->lancamento($semSalario, '2026-08-01');

        $this->artisan('orcames:preencher-salarios', ['--aplicar' => true])->assertSuccessful();

        $this->assertSame(['2026-09-01', '2026-10-01'], $this->mesesComSalario($comSalario));
        $this->assertSame([], $this->mesesComSalario($semSalario));
    }
}
