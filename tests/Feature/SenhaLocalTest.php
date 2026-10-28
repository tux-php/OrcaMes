<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SenhaLocalTest extends TestCase
{
    use RefreshDatabase;

    public function test_todos_ficam_com_a_mesma_senha_sem_troca_obrigatoria(): void
    {
        $usuarios = [
            Usuario::factory()->create(['password' => 'de-producao-1', 'remember_token' => 'abc']),
            Usuario::factory()->create(['password' => 'de-producao-2', 'deve_trocar_senha' => true]),
        ];

        $this->artisan('orcames:senha-local')->assertSuccessful();

        foreach ($usuarios as $usuario) {
            $usuario->refresh();
            $this->assertTrue(Hash::check('senha', $usuario->password));
            $this->assertFalse($usuario->deve_trocar_senha);
            $this->assertNull($usuario->remember_token);
        }
    }

    public function test_nao_roda_em_producao(): void
    {
        $usuario = Usuario::factory()->create(['password' => 'de-producao']);
        $this->app->detectEnvironment(fn () => 'production');

        $this->artisan('orcames:senha-local')->assertFailed();

        $this->assertTrue(Hash::check('de-producao', $usuario->refresh()->password));
    }
}
