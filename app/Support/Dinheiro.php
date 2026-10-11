<?php

namespace App\Support;

final class Dinheiro
{
    /**
     * Converte a digitação brasileira ("1.234,56") para o formato decimal ("1234.56").
     * Sem vírgula o valor passa como veio, para a validação decidir.
     */
    public static function normalizar(mixed $valor): mixed
    {
        if (! is_string($valor)) {
            return $valor;
        }

        $valor = trim(str_replace(['R$', ' '], '', $valor));

        if (str_contains($valor, ',')) {
            $valor = str_replace(',', '.', str_replace('.', '', $valor));
        }

        return $valor;
    }

    public static function br(float|int|string|null $valor): string
    {
        return number_format((float) $valor, 2, ',', '.');
    }

    public static function centavos(float|int|string|null $valor): int
    {
        return (int) round(((float) $valor) * 100);
    }
}
