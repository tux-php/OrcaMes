<?php

namespace App\Importacao;

use RuntimeException;

/**
 * Lê os INSERTs de um dump do phpMyAdmin sem precisar de um MySQL para carregá-lo.
 */
final class DumpLegado
{
    /**
     * @return array<string, list<array<string, string|null>>> linhas por tabela
     */
    public static function ler(string $caminho): array
    {
        $sql = @file_get_contents($caminho);

        if ($sql === false) {
            throw new RuntimeException("Não foi possível ler o arquivo {$caminho}.");
        }

        return self::interpretar($sql);
    }

    /**
     * @return array<string, list<array<string, string|null>>>
     */
    public static function interpretar(string $sql): array
    {
        $tabelas = [];
        $posicao = 0;

        while (preg_match('/INSERT INTO `(\w+)` \(([^)]*)\) VALUES/', $sql, $achado, PREG_OFFSET_CAPTURE, $posicao)) {
            $tabela = $achado[1][0];
            $colunas = array_map(fn (string $coluna) => trim($coluna, " `\n\r\t"), explode(',', $achado[2][0]));
            $posicao = $achado[0][1] + strlen($achado[0][0]);

            foreach (self::lerTuplas($sql, $posicao) as $valores) {
                if (count($valores) !== count($colunas)) {
                    throw new RuntimeException("Linha de `{$tabela}` com número de colunas inesperado.");
                }

                $tabelas[$tabela][] = array_combine($colunas, $valores);
            }
        }

        return $tabelas;
    }

    /**
     * @return list<list<string|null>>
     */
    private static function lerTuplas(string $sql, int &$posicao): array
    {
        $tuplas = [];
        $tamanho = strlen($sql);

        while ($posicao < $tamanho) {
            $caractere = $sql[$posicao];

            if ($caractere === ';') {
                $posicao++;
                break;
            }

            if ($caractere !== '(') {
                $posicao++;

                continue;
            }

            $posicao++;
            $valores = [];

            while (true) {
                while ($sql[$posicao] === ' ') {
                    $posicao++;
                }

                $valores[] = $sql[$posicao] === "'"
                    ? self::lerTexto($sql, $posicao)
                    : self::lerLiteral($sql, $posicao);

                while ($sql[$posicao] === ' ') {
                    $posicao++;
                }

                if ($sql[$posicao++] === ')') {
                    break;
                }
            }

            $tuplas[] = $valores;
        }

        return $tuplas;
    }

    private static function lerTexto(string $sql, int &$posicao): string
    {
        $texto = '';
        $posicao++;

        while (true) {
            $caractere = $sql[$posicao++];

            if ($caractere === '\\') {
                $texto .= match ($escapado = $sql[$posicao++]) {
                    'n' => "\n",
                    'r' => "\r",
                    't' => "\t",
                    '0' => "\0",
                    default => $escapado,
                };
            } elseif ($caractere === "'") {
                if (($sql[$posicao] ?? '') !== "'") {
                    return $texto;
                }

                $texto .= "'";
                $posicao++;
            } else {
                $texto .= $caractere;
            }
        }
    }

    private static function lerLiteral(string $sql, int &$posicao): ?string
    {
        $fim = $posicao + strcspn($sql, ',)', $posicao);
        $literal = trim(substr($sql, $posicao, $fim - $posicao));
        $posicao = $fim;

        if (strcasecmp($literal, 'NULL') === 0) {
            return null;
        }

        if (str_starts_with($literal, '0x')) {
            return (string) hex2bin(substr($literal, 2));
        }

        return $literal;
    }
}
