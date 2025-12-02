<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('publico_alvo')->nullable();
            $table->string('foto_perfil')->nullable();
            $table->string('foto_capa')->nullable();
            $table->enum('status', ['ativado', 'desativado'])->default('ativado');
            $table->unsignedInteger('vagas')->default(0);
            $table->decimal('valor_corte', 10, 2)->default(0);
            $table->json('regioes')->nullable();
            $table->boolean('aceita_menores')->default(false); // ✅ MANTER DIRETAMENTE AQUI
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
