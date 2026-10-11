<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A declaração é feita no programa da Receita; aqui fica só o resultado dela.
        Schema::create('declaracoes_ir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->unsignedSmallInteger('ano_base');
            $table->string('resultado', 12);
            $table->decimal('valor', 12, 2);
            $table->string('observacao')->nullable();
            $table->timestamps();

            $table->unique(['usuario_id', 'ano_base']);
        });

        Schema::create('comprovantes_ir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->unsignedSmallInteger('ano_base');
            $table->string('descricao');
            $table->decimal('valor', 12, 2)->nullable();
            $table->string('arquivo');
            $table->string('nome_original');
            $table->timestamps();

            $table->index(['ano_base', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprovantes_ir');
        Schema::dropIfExists('declaracoes_ir');
    }
};
