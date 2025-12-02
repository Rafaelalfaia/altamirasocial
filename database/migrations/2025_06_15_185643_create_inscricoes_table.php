<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programa_id')->constrained()->onDelete('cascade');
            $table->foreignId('cidadao_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pendente', 'aprovado', 'reprovado'])->default('pendente');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
};
