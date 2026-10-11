<?php

namespace Tests\Unit;

use App\Importacao\DumpLegado;
use App\Importacao\MapeadorLegado;
use PHPUnit\Framework\TestCase;

class ImportacaoTest extends TestCase
{
    private function dump(): array
    {
        return DumpLegado::ler(__DIR__.'/../fixtures/legado.sql');
    }

    public function test_le_textos_com_aspas_nulos_e_hexadecimal(): void
    {
        $dump = $this->dump();

        $this->assertCount(6, $dump['pagamentos']);
        $this->assertSame("Filho d'água; (teste)", $dump['tipo_despesa'][2]['descricao']);
        $this->assertNull($dump['pagamentos'][1]['data_processamento']);
        $this->assertSame('oi.jpg', $dump['imposto_renda'][0]['imagem']);
        $this->assertNull($dump['imposto_renda'][0]['valor']);
    }

    public function test_importa_so_usuarios_ativos_com_pagamentos(): void
    {
        $plano = MapeadorLegado::mapear($this->dump());

        $this->assertSame([1, 2], array_keys($plano['usuarios']));
        $this->assertSame(['nome' => 'Ana', 'sobrenome' => 'Silva', 'email' => 'ana@example.com'], $plano['usuarios'][1]);
        $this->assertCount(1, $plano['avisos']);
    }

    public function test_ignora_excluidos_e_so_marca_pago_quando_o_status_e_pg(): void
    {
        $plano = MapeadorLegado::mapear($this->dump());
        $porValor = array_column($plano['lancamentos'], null, 'valor');

        $this->assertCount(4, $plano['lancamentos']);
        $this->assertArrayNotHasKey('9999.00', $porValor);
        $this->assertSame('2026-08-05', $porValor['1500.00']['pago_em']);
        $this->assertSame('2026-08-01', $porValor['1500.00']['competencia']);
        $this->assertNull($porValor['1550.00']['pago_em']);
        $this->assertNull($porValor['80.00']['pago_em'], 'estorno mantém a data antiga, mas não está pago');
    }

    public function test_monta_contas_e_categorias(): void
    {
        $plano = MapeadorLegado::mapear($this->dump());

        // "CASA", "casa" e "Cása" viram uma só, como o MySQL as compara.
        $this->assertSame(['casa' => 'CASA', "filho d'agua; (teste)" => "Filho d'água; (teste)"], $plano['categorias']);
        $this->assertSame(['1:1', '3:1', '2:2', '4:2'], array_keys($plano['contas']));

        $this->assertTrue($plano['contas']['1:1']['recorrente'], 'aparece no último mês da Ana');
        $this->assertFalse($plano['contas']['3:1']['recorrente']);
        $this->assertFalse($plano['contas']['3:1']['ativa'], 'tipo excluído com histórico entra inativo');
        $this->assertSame('Escola', $plano['contas']['2:2']['nome']);
        $this->assertTrue($plano['contas']['2:2']['recorrente'], 'o último mês da Bia é agosto');
        $this->assertSame('casa', $plano['contas']['4:2']['categoria']);
    }

    public function test_receitas_usam_o_ultimo_extra_e_o_salario_do_cadastro(): void
    {
        $plano = MapeadorLegado::mapear($this->dump());

        $this->assertSame([
            ['usuario' => 1, 'competencia' => '2026-08-01', 'tipo' => 'extra', 'descricao' => 'Extra (importado)', 'valor' => '300.00'],
            ['usuario' => 1, 'competencia' => '2026-09-01', 'tipo' => 'salario', 'descricao' => 'Salário', 'valor' => '5000.00'],
            ['usuario' => 2, 'competencia' => '2026-08-01', 'tipo' => 'salario', 'descricao' => 'Salário', 'valor' => '4000.00'],
        ], $plano['receitas']);
    }
}
