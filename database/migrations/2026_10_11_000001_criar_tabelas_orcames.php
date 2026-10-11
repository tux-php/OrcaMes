<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('sobrenome')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('perfil', 10)->default('membro');
            $table->boolean('ativo')->default(true);
            $table->boolean('deve_trocar_senha')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->timestamps();
        });

        // Conta é o que se paga todo mês (escola, babá, fatura do cartão).
        Schema::create('contas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('nome');
            $table->boolean('recorrente')->default(true);
            $table->boolean('dedutivel_ir')->default(false);
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });

        // O dono do lançamento é o dono da conta; não há usuario_id aqui de propósito.
        Schema::create('lancamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained('contas')->restrictOnDelete();
            $table->date('competencia');
            $table->decimal('valor', 12, 2);
            $table->date('lancado_em');
            $table->date('pago_em')->nullable();
            $table->string('observacao')->nullable();
            $table->timestamps();

            $table->index(['competencia', 'conta_id']);
        });

        Schema::create('receitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->date('competencia');
            $table->string('tipo', 10);
            $table->string('descricao')->nullable();
            $table->decimal('valor', 12, 2);
            $table->timestamps();

            $table->index(['competencia', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receitas');
        Schema::dropIfExists('lancamentos');
        Schema::dropIfExists('contas');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('usuarios');
    }
};
