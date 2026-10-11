<?php

namespace App\Console\Commands;

use App\Enums\Perfil;
use App\Importacao\DumpLegado;
use App\Importacao\MapeadorLegado;
use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Receita;
use App\Models\Usuario;
use App\Support\Dinheiro;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportarLegado extends Command
{
    protected $signature = 'orcames:importar
        {arquivo : Caminho do dump .sql do OrcaMes antigo}
        {--admin= : E-mail do usuário que será o administrador}';

    protected $description = 'Importa usuários, contas, lançamentos e receitas do dump do OrcaMes antigo';

    public function handle(): int
    {
        if (Usuario::query()->exists()) {
            $this->error('O banco já tem usuários. A importação só roda em um banco vazio (php artisan migrate:fresh).');

            return self::FAILURE;
        }

        $plano = MapeadorLegado::mapear(DumpLegado::ler($this->argument('arquivo')));
        $admin = mb_strtolower(trim((string) $this->option('admin')));

        if (! in_array($admin, array_column($plano['usuarios'], 'email'), true)) {
            $this->error('Informe em --admin o e-mail de um destes usuários: '.implode(', ', array_column($plano['usuarios'], 'email')));

            return self::FAILURE;
        }

        $senhas = DB::transaction(fn () => $this->gravar($plano, $admin));

        foreach ($plano['avisos'] as $aviso) {
            $this->warn($aviso);
        }

        $this->info(sprintf(
            'Importados: %d usuários, %d categorias, %d contas, %d lançamentos e %d receitas.',
            count($plano['usuarios']),
            count($plano['categorias']),
            count($plano['contas']),
            count($plano['lancamentos']),
            count($plano['receitas']),
        ));

        $this->table(['Usuário', 'Lançamentos', 'Total lançado (R$)', 'Total pago (R$)'], $this->conferencia());

        $this->newLine();
        $this->warn('Senhas provisórias (anote agora; a troca é exigida no primeiro acesso):');
        $this->table(['E-mail', 'Perfil', 'Senha provisória'], $senhas);

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $plano
     * @return list<array{string, string, string}>
     */
    private function gravar(array $plano, string $admin): array
    {
        $senhas = [];
        $usuarios = [];
        foreach ($plano['usuarios'] as $idAntigo => $dados) {
            $senha = Str::password(12, symbols: false);
            $perfil = $dados['email'] === $admin ? Perfil::Admin : Perfil::Membro;

            $usuarios[$idAntigo] = Usuario::create([
                ...$dados,
                'password' => $senha,
                'perfil' => $perfil,
                'deve_trocar_senha' => true,
            ])->id;
            $senhas[] = [$dados['email'], $perfil->rotulo(), $senha];
        }

        $categorias = [];
        foreach ($plano['categorias'] as $chave => $nome) {
            $categorias[$chave] = Categoria::create(['nome' => $nome])->id;
        }

        $contas = [];
        foreach ($plano['contas'] as $chave => $dados) {
            $contas[$chave] = Conta::create([
                'usuario_id' => $usuarios[$dados['usuario']],
                'categoria_id' => $dados['categoria'] !== null ? $categorias[$dados['categoria']] : null,
                'nome' => $dados['nome'],
                'recorrente' => $dados['recorrente'],
                'ativa' => $dados['ativa'],
            ])->id;
        }

        $agora = now();

        foreach (array_chunk($plano['lancamentos'], 500) as $lote) {
            Lancamento::insert(array_map(fn (array $l) => [
                'conta_id' => $contas[$l['conta']],
                'competencia' => $l['competencia'],
                'valor' => $l['valor'],
                'lancado_em' => $l['lancado_em'],
                'pago_em' => $l['pago_em'],
                'created_at' => $agora,
                'updated_at' => $agora,
            ], $lote));
        }

        foreach ($plano['receitas'] as $receita) {
            Receita::create([
                'usuario_id' => $usuarios[$receita['usuario']],
                'competencia' => $receita['competencia'],
                'tipo' => $receita['tipo'],
                'descricao' => $receita['descricao'],
                'valor' => $receita['valor'],
            ]);
        }

        return $senhas;
    }

    /**
     * @return list<array{string, int, string, string}>
     */
    private function conferencia(): array
    {
        return Usuario::query()->orderBy('nome')->get()->map(function (Usuario $usuario) {
            $lancamentos = Lancamento::query()->dosUsuarios([$usuario->id]);

            return [
                $usuario->nome,
                (clone $lancamentos)->count(),
                Dinheiro::br((clone $lancamentos)->sum('valor')),
                Dinheiro::br((clone $lancamentos)->whereNotNull('pago_em')->sum('valor')),
            ];
        })->all();
    }
}
