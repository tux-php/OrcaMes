<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SenhaLocal extends Command
{
    protected $signature = 'orcames:senha-local
        {senha=senha : Senha que todos os usuários passam a ter}';

    protected $description = 'Depois de restaurar um backup de produção, troca a senha de todos os usuários pela mesma senha (só fora de produção)';

    public function handle(): int
    {
        // As senhas de produção não devem valer numa cópia local do banco; em produção este comando nunca roda.
        if (! app()->environment('local', 'testing')) {
            $this->error('Este comando só roda com APP_ENV=local.');

            return self::FAILURE;
        }

        $senha = (string) $this->argument('senha');

        $total = DB::transaction(function () use ($senha) {
            $usuarios = Usuario::query()->get();

            foreach ($usuarios as $usuario) {
                $usuario->forceFill([
                    'password' => $senha,
                    'deve_trocar_senha' => false,
                    'remember_token' => null,
                ])->save();
            }

            return $usuarios->count();
        });

        $this->info(sprintf('Senha de %d usuários trocada para "%s".', $total, $senha));

        return self::SUCCESS;
    }
}
