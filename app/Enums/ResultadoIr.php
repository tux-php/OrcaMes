<?php

namespace App\Enums;

enum ResultadoIr: string
{
    case APagar = 'a_pagar';
    case ARestituir = 'a_restituir';

    public function rotulo(): string
    {
        return match ($this) {
            self::APagar => 'Imposto a pagar',
            self::ARestituir => 'Restituição a receber',
        };
    }
}
