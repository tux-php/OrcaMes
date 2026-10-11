<?php

namespace App\Enums;

enum Perfil: string
{
    case Admin = 'admin';
    case Membro = 'membro';

    public function rotulo(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Membro => 'Membro',
        };
    }
}
