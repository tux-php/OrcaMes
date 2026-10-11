<?php

namespace Tests\Unit;

use App\Support\Dinheiro;
use PHPUnit\Framework\TestCase;

class DinheiroTest extends TestCase
{
    public function test_converte_a_digitacao_brasileira(): void
    {
        $this->assertSame('1234.56', Dinheiro::normalizar('1.234,56'));
        $this->assertSame('1234.56', Dinheiro::normalizar('R$ 1.234,56'));
        $this->assertSame('12.5', Dinheiro::normalizar('12,5'));
        $this->assertSame('1234.56', Dinheiro::normalizar('1234.56'));
        $this->assertNull(Dinheiro::normalizar(null));
    }

    public function test_formata_em_reais(): void
    {
        $this->assertSame('1.234,50', Dinheiro::br('1234.5'));
        $this->assertSame('-10,00', Dinheiro::br(-10));
    }

    public function test_centavos_nao_sofrem_com_ponto_flutuante(): void
    {
        $this->assertSame(1999, Dinheiro::centavos('19.99'));
        $this->assertSame(30, Dinheiro::centavos(0.1 + 0.2));
    }
}
