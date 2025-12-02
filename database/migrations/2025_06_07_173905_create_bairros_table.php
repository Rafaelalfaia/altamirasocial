<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bairros', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('nome');
            $table->foreignId('cidade_id')
                  ->nullable()
                  ->constrained('cidades') // explicitamente referencia a tabela 'cidades'
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bairros');
    }
};
