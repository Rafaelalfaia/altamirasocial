<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('indicacoes_programas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('programa_id')->constrained('programas')->onDelete('cascade');
            $table->foreignId('cidadao_id')->constrained('cidadaos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // assistente

            $table->text('justificativa')->nullable(); // opcional
            $table->timestamp('indicado_em')->useCurrent();

            $table->enum('status', ['pendente', 'aprovada', 'reprovada'])->default('pendente');
            $table->text('resposta_coordenador')->nullable();
            $table->timestamp('avaliado_em')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicacoes_programas');
    }
};
