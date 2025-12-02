<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('composicoes_familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acompanhamento_id')->constrained()->onDelete('cascade');

            $table->string('nome');
            $table->date('data_nascimento')->nullable();
            $table->string('parentesco')->nullable();
            $table->string('escolaridade')->nullable();
            $table->string('beneficio')->nullable();
            $table->decimal('valor_beneficio', 10, 2)->nullable();
            $table->string('profissao')->nullable();
            $table->decimal('renda_bruta', 10, 2)->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composicoes_familiares');
    }
};
