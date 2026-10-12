<?php

namespace App\Support;

final class NomePadrao
{
    /** Ficam em maiúsculas; qualquer outra palavra vira "Assim". */
    private const SIGLAS = ['ABA', 'BB', 'BRB', 'C&A', 'GEAP', 'IPTU', 'IPVA', 'ITA', 'LTDA', 'MCP', 'PA', 'TI', 'TV', 'USA'];

    /** Ficam em minúsculas, menos no começo do nome. */
    private const LIGACOES = ['da', 'das', 'de', 'do', 'dos', 'e'];

    /**
     * Deixa cada palavra com a primeira letra maiúscula ("CARTÃO DE CRÉDITO" → "Cartão de Crédito"),
     * preservando as siglas conhecidas. Espaço, hífen e sublinhado separam palavras.
     */
    public static function aplicar(string $nome): string
    {
        $partes = preg_split('/([\s_-]+)/u', self::limpar($nome), -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($partes as $i => $parte) {
            // Os índices ímpares são os separadores capturados.
            if ($i % 2 === 0) {
                $partes[$i] = self::palavra($parte, $i === 0);
            }
        }

        return implode('', $partes);
    }

    /**
     * Para o que vem dos formulários: padroniza o que foi digitado todo em maiúsculas ou todo em
     * minúsculas e respeita quem misturou de propósito ("Cartão BB", "iPhone").
     */
    public static function aoDigitar(mixed $nome): mixed
    {
        if (! is_string($nome)) {
            return $nome;
        }

        $nome = self::limpar($nome);
        $semCaixaDefinida = $nome === mb_strtoupper($nome) || $nome === mb_strtolower($nome);

        return $semCaixaDefinida ? self::aplicar($nome) : $nome;
    }

    private static function limpar(string $nome): string
    {
        return trim(preg_replace('/\s+/u', ' ', $nome));
    }

    private static function palavra(string $palavra, bool $primeira): string
    {
        if (in_array(mb_strtoupper($palavra), self::SIGLAS, true)) {
            return mb_strtoupper($palavra);
        }

        $minuscula = mb_strtolower($palavra);

        if (! $primeira && in_array($minuscula, self::LIGACOES, true)) {
            return $minuscula;
        }

        return mb_strtoupper(mb_substr($minuscula, 0, 1)).mb_substr($minuscula, 1);
    }
}
