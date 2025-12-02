<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('denuncia_programas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('programa_id')->constrained()->onDelete('cascade');
            $table->foreignId('cidadao_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // assistente que denunciou

            $table->text('motivo');
            $table->timestamp('denunciado_em')->useCurrent();

            // Avaliação do Coordenador
            $table->enum('status', ['pendente', 'aprovada', 'reprovada'])->default('pendente');
            $table->text('resposta_coordenador')->nullable();
            $table->timestamp('avaliado_em')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denuncia_programas');
    }
};
