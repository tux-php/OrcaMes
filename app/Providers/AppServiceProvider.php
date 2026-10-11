<?php

namespace App\Providers;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::define('admin', fn (Usuario $usuario) => $usuario->isAdmin());

        // Vale para Conta, Lancamento e Receita: todos expõem donoId().
        Gate::define('gerenciar', fn (Usuario $usuario, Model $registro) => $usuario->isAdmin()
            || $registro->donoId() === $usuario->id);
    }
}
