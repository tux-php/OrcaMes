<?php

namespace App\Importacao;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Traduz as tabelas do OrcaMes antigo para o modelo novo, sem tocar no banco.
 *
 * Regras combinadas:
 * - só entram usuários não excluídos que têm pagamentos;
 * - pagamentos excluídos (d_e_l_e_t_e) ficam de fora;
 * - só status "PG" conta como pago; estornos mantêm a data antiga e são ignorados;
 * - de pagamento_extra vale a última linha de cada usuário/mês;
 * - o salário do cadastro vira a receita do mês mais recente de cada usuário;
 * - a conta é recorrente se apareceu nesse mês mais recente.
 */
final class MapeadorLegado
{
    private const MESES = [
        'JAN' => 1, 'FEV' => 2, 'MAR' => 3, 'ABR' => 4, 'MAI' => 5, 'JUN' => 6,
        'JUL' => 7, 'AGO' => 8, 'SET' => 9, 'OUT' => 10, 'NOV' => 11, 'DEZ' => 12,
    ];

    /**
     * @param  array<string, list<array<string, string|null>>>  $dump
     * @return array{
     *     usuarios: array<int, array{nome: string, sobrenome: ?string, email: string}>,
     *     categorias: array<string, string>,
     *     contas: array<string, array{usuario: int, nome: string, categoria: ?string, ativa: bool, recorrente: bool}>,
     *     lancamentos: list<array{conta: string, competencia: string, valor: string, lancado_em: string, pago_em: ?string}>,
     *     receitas: list<array{usuario: int, competencia: string, tipo: string, descricao: string, valor: string}>,
     *     avisos: list<string>
     * }
     */
    public static function mapear(array $dump): array
    {
        $avisos = [];
        $ativo = fn (array $linha) => $linha['d_e_l_e_t_e'] === null;

        $competencias = [];
        foreach ($dump['mes_referencia'] ?? [] as $mes) {
            $competencias[(int) $mes['id_mes_referencia']] = self::competencia($mes['chave']);
        }

        $statusPago = [];
        foreach ($dump['status_pagamento'] ?? [] as $status) {
            if (trim((string) $status['chave']) === 'PG') {
                $statusPago[(int) $status['id_status_pagamento']] = true;
            }
        }

        $emails = [];
        foreach (array_filter($dump['autenticacao_user'] ?? [], $ativo) as $autenticacao) {
            $emails[(int) $autenticacao['id_usuario']] = mb_strtolower(trim($autenticacao['email']));
        }

        $pagamentosAtivos = array_filter($dump['pagamentos'] ?? [], $ativo);
        $comPagamento = array_flip(array_map(fn (array $p) => (int) $p['id_usuario'], $pagamentosAtivos));

        $usuarios = [];
        $salarios = [];
        foreach (array_filter($dump['usuario'] ?? [], $ativo) as $usuario) {
            $id = (int) $usuario['id_usuario'];

            if (! isset($comPagamento[$id])) {
                $avisos[] = "Usuário {$usuario['nome']} (id {$id}) ignorado: não tem pagamentos.";

                continue;
            }

            if (! isset($emails[$id])) {
                $avisos[] = "Usuário {$usuario['nome']} (id {$id}) ignorado: sem e-mail de acesso ativo.";

                continue;
            }

            $usuarios[$id] = [
                'nome' => self::titulo($usuario['nome']),
                'sobrenome' => $usuario['sobrenome'] !== null ? self::titulo($usuario['sobrenome']) : null,
                'email' => $emails[$id],
            ];
            $salarios[$id] = $usuario['salario'];
        }

        $nomesDeCategoria = [];
        $categorias = [];
        foreach (array_filter($dump['tipo_despesa'] ?? [], $ativo) as $tipoDespesa) {
            $nome = trim($tipoDespesa['descricao']);
            $chave = self::chaveDeCategoria($nome);
            $nomesDeCategoria[(int) $tipoDespesa['id_tipo_despesa']] = $chave;

            if (isset($usuarios[(int) $tipoDespesa['id_usuario']])) {
                $categorias[$chave] ??= $nome;
            }
        }
        $nomeOriginal = [];
        foreach ($dump['tipo_despesa'] ?? [] as $tipoDespesa) {
            $nomeOriginal[self::chaveDeCategoria($tipoDespesa['descricao'])] ??= trim($tipoDespesa['descricao']);
        }

        $tipos = [];
        foreach ($dump['tipo_pagamento'] ?? [] as $tipo) {
            $tipos[(int) $tipo['id_tipo_pagamento']] = $tipo;
        }

        $contas = [];
        $registrarConta = function (int $tipoId, int $usuarioId) use (&$contas, &$categorias, $tipos, $nomesDeCategoria, $nomeOriginal): string {
            $chave = "{$tipoId}:{$usuarioId}";

            if (! isset($contas[$chave])) {
                $tipo = $tipos[$tipoId] ?? throw new RuntimeException("Pagamento aponta para tipo_pagamento {$tipoId}, que não existe.");
                $categoria = $nomesDeCategoria[(int) $tipo['id_tipo_despesa']] ?? null;

                if ($categoria !== null) {
                    $categorias[$categoria] ??= $nomeOriginal[$categoria];
                }

                $contas[$chave] = [
                    'usuario' => $usuarioId,
                    'nome' => trim($tipo['descricao']),
                    'categoria' => $categoria,
                    'ativa' => $tipo['d_e_l_e_t_e'] === null,
                    'recorrente' => false,
                ];
            }

            return $chave;
        };

        $lancamentos = [];
        $ultimaCompetencia = [];
        foreach ($pagamentosAtivos as $pagamento) {
            $usuarioId = (int) $pagamento['id_usuario'];

            if (! isset($usuarios[$usuarioId])) {
                continue;
            }

            $competencia = $competencias[(int) $pagamento['id_mes_referencia']]
                ?? throw new RuntimeException("Pagamento {$pagamento['id_pagamento']} sem mês de referência válido.");
            $pago = isset($statusPago[(int) $pagamento['id_status_pagamento']]);

            $lancamentos[] = [
                'conta' => $registrarConta((int) $pagamento['id_tipo_pagamento'], $usuarioId),
                'competencia' => $competencia,
                'valor' => $pagamento['valor_pagamento'],
                'lancado_em' => $pagamento['data_lancamento'],
                'pago_em' => $pago ? ($pagamento['data_processamento'] ?? $pagamento['data_lancamento']) : null,
            ];

            $ultimaCompetencia[$usuarioId] = max($ultimaCompetencia[$usuarioId] ?? '', $competencia);
        }

        foreach ($lancamentos as $lancamento) {
            $dono = $contas[$lancamento['conta']]['usuario'];

            if ($lancamento['competencia'] === $ultimaCompetencia[$dono]) {
                $contas[$lancamento['conta']]['recorrente'] = true;
            }
        }

        // Contas cadastradas e ainda não usadas também migram.
        foreach ($dump['usuario_tipo_pagamento'] ?? [] as $vinculo) {
            $usuarioId = (int) $vinculo['id_usuario'];
            $tipo = $tipos[(int) $vinculo['id_tipo_pagamento']] ?? null;

            if (isset($usuarios[$usuarioId]) && $tipo !== null && $tipo['d_e_l_e_t_e'] === null) {
                $registrarConta((int) $vinculo['id_tipo_pagamento'], $usuarioId);
            }
        }

        $extras = [];
        foreach ($dump['pagamento_extra'] ?? [] as $extra) {
            $usuarioId = (int) $extra['id_usuario'];
            $chave = $usuarioId.':'.$extra['id_mes_referencia'];

            if (isset($usuarios[$usuarioId]) && (int) $extra['id_pag_extra'] > (int) ($extras[$chave]['id_pag_extra'] ?? 0)) {
                $extras[$chave] = $extra;
            }
        }

        $receitas = [];
        foreach ($extras as $extra) {
            if ((float) $extra['valor_extra'] > 0) {
                $receitas[] = [
                    'usuario' => (int) $extra['id_usuario'],
                    'competencia' => $competencias[(int) $extra['id_mes_referencia']],
                    'tipo' => 'extra',
                    'descricao' => 'Extra (importado)',
                    'valor' => $extra['valor_extra'],
                ];
            }
        }

        foreach ($salarios as $usuarioId => $salario) {
            if ((float) $salario > 0 && isset($ultimaCompetencia[$usuarioId])) {
                $receitas[] = [
                    'usuario' => $usuarioId,
                    'competencia' => $ultimaCompetencia[$usuarioId],
                    'tipo' => 'salario',
                    'descricao' => 'Salário',
                    'valor' => $salario,
                ];
            }
        }

        return compact('usuarios', 'categorias', 'contas', 'lancamentos', 'receitas', 'avisos');
    }

    /**
     * "OUT/2026" → "2026-10-01"
     */
    private static function competencia(string $chave): string
    {
        [$mes, $ano] = array_pad(explode('/', trim($chave)), 2, '');

        if (! isset(self::MESES[$mes]) || ! ctype_digit($ano)) {
            throw new RuntimeException("Mês de referência não reconhecido: {$chave}");
        }

        return sprintf('%04d-%02d-01', $ano, self::MESES[$mes]);
    }

    /**
     * "EMPRÉSTIMO" e "emprestimo" são a mesma categoria: o MySQL compara sem
     * diferenciar maiúsculas nem acentos, e o nome é único.
     */
    private static function chaveDeCategoria(string $nome): string
    {
        return mb_strtolower(Str::ascii(trim($nome)));
    }

    private static function titulo(string $texto): string
    {
        return mb_convert_case(trim($texto), MB_CASE_TITLE);
    }
}
