<?php

namespace App\Enums;

enum TipoReceita: string
{
    case Salario = 'salario';
    case Extra = 'extra';

    public function rotulo(): string
    {
        return match ($this) {
            self::Salario => 'Salário',
            self::Extra => 'Extra',
        };
    }
}
