<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modo_plantao', function (Blueprint $table) {
            $table->id();

            // Relacionamento com usuário
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Estado do plantão
            $table->boolean('ativo')->default(false);

            // Novos timestamps específicos de plantão
            $table->timestamp('inicio_plantao')->nullable();
            $table->timestamp('fim_plantao')->nullable();

            // Timestamps padrão do Laravel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modo_plantao');
    }
};
