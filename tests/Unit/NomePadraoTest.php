<?php

namespace Tests\Unit;

use App\Support\NomePadrao;
use PHPUnit\Framework\TestCase;

class NomePadraoTest extends TestCase
{
    public function test_cada_palavra_fica_com_a_primeira_letra_maiuscula(): void
    {
        $this->assertSame('Filho', NomePadrao::aplicar('FILHO'));
        $this->assertSame('Pet', NomePadrao::aplicar('pet'));
        $this->assertSame('Inglês - Yazigi', NomePadrao::aplicar('inglês - yazigi'));
        $this->assertSame('Babá-Sophia', NomePadrao::aplicar('BABÁ-SOPHIA'));
        $this->assertSame('Água', NomePadrao::aplicar('  ÁGUA  '));
        $this->assertSame('Parcela Cama', NomePadrao::aplicar('parcela   cama'));
    }

    public function test_palavras_de_ligacao_ficam_minusculas_menos_no_comeco(): void
    {
        $this->assertSame('Cartão de Crédito', NomePadrao::aplicar('CARTÃO DE CRÉDITO'));
        $this->assertSame('Banco do Brasil', NomePadrao::aplicar('Banco do Brasil'));
        $this->assertSame('De Volta', NomePadrao::aplicar('de volta'));
    }

    public function test_siglas_conhecidas_ficam_maiusculas(): void
    {
        $this->assertSame('IPTU', NomePadrao::aplicar('iptu'));
        $this->assertSame('Cartao_BB', NomePadrao::aplicar('CARTAO_BB'));
        $this->assertSame('Café - BRB', NomePadrao::aplicar('CAFÉ - BRB'));
        $this->assertSame('C&A', NomePadrao::aplicar('c&a'));
        $this->assertSame('ABA Marília', NomePadrao::aplicar('ABA marília'));
    }

    public function test_digitacao_com_caixa_misturada_e_respeitada(): void
    {
        $this->assertSame('Mercado', NomePadrao::aoDigitar('mercado'));
        $this->assertSame('Carro Novo', NomePadrao::aoDigitar('CARRO NOVO'));
        $this->assertSame('Cartão XP', NomePadrao::aoDigitar(' Cartão XP '));
        $this->assertSame('Plano de saúde', NomePadrao::aoDigitar('Plano de saúde'));
        $this->assertNull(NomePadrao::aoDigitar(null));
    }
}
