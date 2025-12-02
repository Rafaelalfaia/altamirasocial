<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cidadaos_temporarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf')->nullable()->unique();
            $table->string('motivo')->nullable();              // <- adicionado
            $table->integer('prazo_validade')->nullable();      // <- adicionado
            $table->dateTime('inicio_validez')->nullable();     // <- atualizado
            $table->dateTime('fim_validez')->nullable();        // <- atualizado
            $table->string('tipo_ajuda')->nullable();
            $table->enum('status', ['ativo', 'expirado'])->default('ativo');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // <- quem criou
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cidadaos_temporarios');
    }
};
