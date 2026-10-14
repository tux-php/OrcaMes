<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\ExtratoController;
use App\Http\Controllers\ImpostoRendaController;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\ReceitaController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\SenhaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'formulario'])->name('login');
    Route::post('/login', [AuthController::class, 'entrar'])->middleware('throttle:10,1');
});

Route::middleware(['auth', 'acesso'])->group(function () {
    Route::post('/logout', [AuthController::class, 'sair'])->name('logout');

    Route::get('/senha', [SenhaController::class, 'edit'])->name('senha.edit');
    Route::put('/senha', [SenhaController::class, 'update'])->name('senha.update');

    Route::get('/', [PainelController::class, 'index'])->name('painel');
    Route::get('/relatorio', [RelatorioController::class, 'index'])->name('relatorio');
    Route::get('/relatorio/pagamentos', [ExtratoController::class, 'pagamentos'])->name('relatorio.pagamentos');

    Route::post('/meses/gerar', [LancamentoController::class, 'gerar'])->name('meses.gerar');

    Route::resource('lancamentos', LancamentoController::class)
        ->only(['store', 'edit', 'update', 'destroy'])
        ->parameters(['lancamentos' => 'lancamento']);
    Route::patch('/lancamentos/{lancamento}/pagar', [LancamentoController::class, 'pagar'])->name('lancamentos.pagar');
    Route::patch('/lancamentos/{lancamento}/estornar', [LancamentoController::class, 'estornar'])->name('lancamentos.estornar');

    Route::resource('receitas', ReceitaController::class)
        ->only(['store', 'destroy'])
        ->parameters(['receitas' => 'receita']);

    Route::resource('contas', ContaController::class)
        ->except(['show'])
        ->parameters(['contas' => 'conta']);

    Route::get('/ir', [ImpostoRendaController::class, 'index'])->name('ir.index');
    Route::post('/ir/declaracao', [ImpostoRendaController::class, 'salvarDeclaracao'])->name('ir.declaracao.salvar');
    Route::delete('/ir/declaracao/{declaracao}', [ImpostoRendaController::class, 'excluirDeclaracao'])->name('ir.declaracao.excluir');
    Route::post('/ir/comprovantes', [ImpostoRendaController::class, 'anexarComprovante'])->name('ir.comprovantes.anexar');
    Route::get('/ir/comprovantes/{comprovante}', [ImpostoRendaController::class, 'baixarComprovante'])->name('ir.comprovantes.baixar');
    Route::delete('/ir/comprovantes/{comprovante}', [ImpostoRendaController::class, 'excluirComprovante'])->name('ir.comprovantes.excluir');

    Route::middleware('can:admin')->group(function () {
        Route::resource('categorias', CategoriaController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['categorias' => 'categoria']);

        Route::resource('usuarios', UsuarioController::class)
            ->except(['show', 'destroy'])
            ->parameters(['usuarios' => 'usuario']);
    });
});
