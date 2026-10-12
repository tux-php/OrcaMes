<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Conta;
use App\Support\NomePadrao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PadronizarNomes extends Command
{
    protected $signature = 'orcames:padronizar-nomes
        {--aplicar : Grava as mudanças; sem esta opção o comando só mostra o que mudaria}';

    protected $description = 'Padroniza maiúsculas e minúsculas nos nomes de categorias e contas já cadastradas';

    public function handle(): int
    {
        $categorias = $this->mudancas(Categoria::class);
        $contas = $this->mudancas(Conta::class);

        // O nome da categoria é único: duas que ficariam iguais não são alteradas.
        $nomesFinais = Categoria::query()->pluck('nome', 'id')->replace(array_column($categorias, 'depois', 'id'));
        $repetidos = $nomesFinais->map(fn (string $nome) => mb_strtolower($nome))->duplicates()->unique();
        [$conflitos, $categorias] = collect($categorias)
            ->partition(fn (array $m) => $repetidos->contains(mb_strtolower($m['depois'])))
            ->map->values()
            ->all();

        $this->mostrar('Categorias', $categorias->all());
        $this->mostrar('Contas', $contas);

        foreach ($conflitos as $conflito) {
            $this->warn("Categoria \"{$conflito['antes']}\" não foi alterada: já existe outra com o nome \"{$conflito['depois']}\".");
        }

        if (! $this->option('aplicar')) {
            $this->info('Nada foi gravado. Para gravar, rode de novo com --aplicar.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($categorias, $contas) {
            foreach ($categorias as $mudanca) {
                Categoria::query()->whereKey($mudanca['id'])->update(['nome' => $mudanca['depois']]);
            }
            foreach ($contas as $mudanca) {
                Conta::query()->whereKey($mudanca['id'])->update(['nome' => $mudanca['depois']]);
            }
        });

        $this->info(sprintf('Gravado: %d categorias e %d contas renomeadas.', count($categorias), count($contas)));

        return self::SUCCESS;
    }

    /**
     * @param  class-string<Categoria|Conta>  $modelo
     * @return list<array{id: int, antes: string, depois: string}>
     */
    private function mudancas(string $modelo): array
    {
        return $modelo::query()->orderBy('nome')->get(['id', 'nome'])
            ->map(fn ($registro) => ['id' => $registro->id, 'antes' => $registro->nome, 'depois' => NomePadrao::aplicar($registro->nome)])
            ->filter(fn (array $m) => $m['antes'] !== $m['depois'])
            ->values()
            ->all();
    }

    /**
     * @param  list<array{id: int, antes: string, depois: string}>  $mudancas
     */
    private function mostrar(string $titulo, array $mudancas): void
    {
        $this->line(sprintf('%s: %d a renomear', $titulo, count($mudancas)));

        if ($mudancas !== []) {
            $this->table(['Antes', 'Depois'], array_map(fn (array $m) => [$m['antes'], $m['depois']], $mudancas));
        }
    }
}
