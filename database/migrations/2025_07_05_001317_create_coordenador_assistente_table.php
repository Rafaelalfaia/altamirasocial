<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coordenador_assistente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coordenador_id');
            $table->unsignedBigInteger('assistente_id');
            $table->timestamps();

            $table->foreign('coordenador_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assistente_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['coordenador_id', 'assistente_id']); // evita duplicações
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coordenador_assistente');
    }
};
