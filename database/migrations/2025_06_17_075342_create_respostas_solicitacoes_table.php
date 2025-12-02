<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('respostas_solicitacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitacao_id')->constrained('solicitacoes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Quem respondeu
            $table->text('resposta')->nullable();
            $table->boolean('concluida')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respostas_solicitacoes');
    }
};
