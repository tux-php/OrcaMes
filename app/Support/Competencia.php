<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class Competencia
{
    private const MESES = [
        1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
    ];

    /**
     * Aceita "AAAA-MM"; qualquer outra coisa vira o mês atual.
     */
    public static function deTexto(?string $mes): CarbonImmutable
    {
        if ($mes !== null && preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $mes, $partes)) {
            return CarbonImmutable::create((int) $partes[1], (int) $partes[2], 1)->startOfDay();
        }

        return CarbonImmutable::now()->startOfMonth();
    }

    public static function rotulo(CarbonInterface $competencia): string
    {
        return self::MESES[$competencia->month].'/'.$competencia->year;
    }

    public static function nomeDoMes(int $mes): string
    {
        return self::MESES[$mes];
    }
}
