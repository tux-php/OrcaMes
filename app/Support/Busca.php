<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

final class Busca
{
    /**
     * Filtra pelo trecho digitado em qualquer parte da coluna. "%" e "_" valem como texto comum,
     * e o caractere de escape é declarado porque o padrão muda entre MySQL e SQLite.
     */
    public static function contendo(Builder $query, string $coluna, string $trecho): void
    {
        $trecho = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $trecho);

        $query->whereRaw("{$coluna} like ? escape '!'", ["%{$trecho}%"]);
    }
}
