<?php

namespace App\Console\Commands;

use App\Enums\TipoReceita;
use App\Models\Lancamento;
use App\Models\Receita;
use App\Models\Usuario;
use App\Support\Competencia;
use App\Support\Dinheiro;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PreencherSalarios extends Command
{
    public const SUFIXO = ' (estimado)';

    protected $signature = 'orcames:preencher-salarios
        {--aplicar : Grava as receitas; sem esta opção o comando só mostra o que seria criado}';

    protected $description = 'Repete o salário mais recente de cada usuário nos meses anteriores que têm lançamentos e nenhum salário';

    public function handle(): int
    {
        $planos = Usuario::query()->orderBy('nome')->get()
            ->map(fn (Usuario $usuario) => $this->plano($usuario))
            ->filter()
            ->values();

        if ($planos->isEmpty()) {
            $this->info('Nenhum mês a preencher.');

            return self::SUCCESS;
        }

        $this->table(['Usuário', 'Salário (R$)', 'Meses a preencher', 'De', 'Até'], $planos->map(fn (array $p) => [
            $p['usuario']->nome,
            Dinheiro::br($p['modelos']->sum('valor')),
            $p['meses']->count(),
            Competencia::rotulo(CarbonImmutable::parse($p['meses']->first())),
            Competencia::rotulo(CarbonImmutable::parse($p['meses']->last())),
        ])->all());

        if (! $this->option('aplicar')) {
            $this->info('Nada foi gravado. Para gravar, rode de novo com --aplicar.');

            return self::SUCCESS;
        }

        $criadas = DB::transaction(function () use ($planos) {
            $criadas = 0;

            foreach ($planos as $plano) {
                foreach ($plano['meses'] as $mes) {
                    foreach ($plano['modelos'] as $modelo) {
                        Receita::create([
                            'usuario_id' => $plano['usuario']->id,
                            'competencia' => $mes,
                            'tipo' => TipoReceita::Salario,
                            'descricao' => $modelo->descricao.self::SUFIXO,
                            'valor' => $modelo->valor,
                        ]);
                        $criadas++;
                    }
                }
            }

            return $criadas;
        });

        $this->info(sprintf('Gravado: %d receitas de salário criadas.', $criadas));

        return self::SUCCESS;
    }

    /**
     * O modelo é o salário do mês mais recente que tem salário; só os meses
     * anteriores a ele são preenchidos.
     *
     * @return array{usuario: Usuario, modelos: Collection<int, Receita>, meses: Collection<int, string>}|null
     */
    private function plano(Usuario $usuario): ?array
    {
        $salarios = Receita::query()->where('usuario_id', $usuario->id)->where('tipo', TipoReceita::Salario)->get();

        if ($salarios->isEmpty()) {
            return null;
        }

        $comSalario = $salarios->map(fn (Receita $r) => $r->competencia->toDateString())->unique();
        $ultimo = $comSalario->max();

        $meses = Lancamento::query()->dosUsuarios([$usuario->id])->distinct()->pluck('competencia')
            ->map(fn ($competencia) => CarbonImmutable::parse($competencia)->toDateString())
            ->unique()
            ->filter(fn (string $mes) => $mes < $ultimo && ! $comSalario->contains($mes))
            ->sort()
            ->values();

        if ($meses->isEmpty()) {
            return null;
        }

        return [
            'usuario' => $usuario,
            'modelos' => $salarios->filter(fn (Receita $r) => $r->competencia->toDateString() === $ultimo)->values(),
            'meses' => $meses,
        ];
    }
}
