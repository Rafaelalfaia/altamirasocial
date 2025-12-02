<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergencias', function (Blueprint $table) {
            $table->id();

            // Cidadão que solicitou
            $table->foreignId('cidadao_id')
                ->constrained('cidadaos')
                ->onDelete('cascade');

            // Assistente que irá atender (agora pode ser nulo)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->boolean('modo_plantao')->default(false); // indica se o atendimento foi via plantão

            $table->string('motivo');
            $table->text('descricao')->nullable();
            $table->string('sala'); // nome da sala Jitsi

            $table->enum('status', ['aberto', 'encerrado'])->default('aberto');
            $table->text('conclusao')->nullable();

            $table->boolean('reportado_semed')->default(false); // se foi relatado à SEMED

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergencias');
    }
};
