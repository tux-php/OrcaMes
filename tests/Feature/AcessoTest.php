<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AcessoTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_e_mandado_para_o_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/contas')->assertRedirect('/login');
    }

    public function test_entra_com_email_e_senha(): void
    {
        $usuario = Usuario::factory()->create(['password' => 'segredo-123']);

        $this->post('/login', ['email' => $usuario->email, 'password' => 'segredo-123'])->assertRedirect('/');
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_senha_errada_e_usuario_inativo_nao_entram(): void
    {
        $ativo = Usuario::factory()->create(['password' => 'segredo-123']);
        $inativo = Usuario::factory()->create(['password' => 'segredo-123', 'ativo' => false]);

        $this->post('/login', ['email' => $ativo->email, 'password' => 'errada'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $inativo->email, 'password' => 'segredo-123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_tentativa_de_sql_injection_no_login_nao_entra(): void
    {
        Usuario::factory()->create();

        $this->post('/login', ['email' => "x' OR 1=1 -- ", 'password' => 'qualquer'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_usuario_desativado_perde_a_sessao(): void
    {
        $usuario = Usuario::factory()->create();
        $this->actingAs($usuario)->get('/')->assertOk();

        $usuario->update(['ativo' => false]);

        $this->get('/')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_senha_provisoria_obriga_a_troca(): void
    {
        $usuario = Usuario::factory()->create(['password' => 'provisoria-1', 'deve_trocar_senha' => true]);

        $this->actingAs($usuario)->get('/')->assertRedirect('/senha');
        $this->get('/contas')->assertRedirect('/senha');
        $this->get('/senha')->assertOk();

        $this->put('/senha', [
            'senha_atual' => 'provisoria-1',
            'password' => 'definitiva-123',
            'password_confirmation' => 'definitiva-123',
        ])->assertRedirect('/');

        $usuario->refresh();
        $this->assertFalse($usuario->deve_trocar_senha);
        $this->assertTrue(Hash::check('definitiva-123', $usuario->password));
        $this->get('/')->assertOk();
    }

    public function test_troca_de_senha_exige_a_senha_atual(): void
    {
        $usuario = Usuario::factory()->create(['password' => 'atual-12345']);

        $this->actingAs($usuario)->put('/senha', [
            'senha_atual' => 'chute',
            'password' => 'definitiva-123',
            'password_confirmation' => 'definitiva-123',
        ])->assertSessionHasErrors('senha_atual');

        $this->assertTrue(Hash::check('atual-12345', $usuario->refresh()->password));
    }
}
