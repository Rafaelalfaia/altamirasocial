<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('programa_inscricoes', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->foreignId('programa_id')->constrained()->onDelete('cascade');
            $table->foreignId('cidadao_id')->constrained()->onDelete('cascade');

            // Status da inscrição
            $table->enum('status', ['pendente', 'aprovado', 'reprovado'])->default('pendente');
            $table->text('mensagem_reprovacao')->nullable();

            // Região de origem do cidadão no momento da inscrição
            $table->string('regiao_origem')->nullable();

            $table->timestamps();

            // Restringe duplicação de inscrições
            $table->unique(['programa_id', 'cidadao_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programa_inscricoes');
    }
};
