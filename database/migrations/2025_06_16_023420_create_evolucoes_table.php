<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evolucoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('acompanhamento_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Assistente responsável

            $table->string('titulo');
            $table->text('resumo')->nullable();
            $table->string('local_atendimento')->nullable();

            // Campos para casos emergenciais
            $table->enum('caso_emergencial', [
                'violencia_domestica',
                'violencia_sexual',
                'problemas_saude',
                'pobreza_extrema',
                'tentativa_homicidio'
            ])->nullable();

            $table->text('descricao_emergencial')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evolucoes');
    }
};
