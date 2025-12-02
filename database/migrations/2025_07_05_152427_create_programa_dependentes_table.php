<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidadao_id')->constrained()->onDelete('cascade');
            $table->string('nome');
            $table->date('data_nascimento');
            $table->string('cpf')->nullable()->unique();
            $table->string('grau_parentesco');
            $table->enum('sexo', ['masculino', 'feminino', 'outro'])->nullable();
            $table->string('escolaridade')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dependentes');
    }
};
