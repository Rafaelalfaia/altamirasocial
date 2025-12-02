<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Relacionamento com o Coordenador
            $table->unsignedBigInteger('coordenador_id')->nullable();

            // Dados principais
            $table->string('name');
            $table->string('email')->nullable()->unique(); // e-mail opcional
            $table->string('cpf', 14)->unique(); // CPF com pontuação
            $table->string('telefone', 20)->nullable();

            // Extras
            $table->string('foto')->nullable();
            $table->boolean('modo_plantao')->default(false); // Atende a emergência

            // Segurança
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->timestamps();

            // Chave estrangeira para coordenador
            $table->foreign('coordenador_id', 'fk_users_coordenador')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
