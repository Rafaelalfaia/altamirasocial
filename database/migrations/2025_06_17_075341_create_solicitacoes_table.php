<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('solicitacoes', function (Blueprint $table) {
            $table->id();

            // Criador (Coordenador)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Destinatário (pode ser assistente ou cidadão)
            $table->unsignedBigInteger('destinatario_id')->nullable();
            $table->string('destinatario_tipo')->nullable(); // Cidadao ou Assistente
            $table->foreign('destinatario_id')->references('id')->on('users')->onDelete('cascade');

            // Título e Mensagem
            $table->string('titulo');
            $table->text('mensagem')->nullable();

            // Resposta do destinatário
            $table->text('resposta')->nullable();

            // Para quem foi enviada (público-alvo)
            $table->enum('tipo_destinatario', ['cidadao', 'assistente', 'ambos']);

            // Status geral
            $table->string('status')->default('pendente');

            // Status de envio (pode ser cancelado sem excluir do sistema)
            $table->enum('status_envio', ['enviado', 'cancelado'])->default('enviado');

            // Se foi encerrada (não editável)
            $table->boolean('encerrada')->default(false);

            // Soft delete (ocultada do coordenador ou destinatário)
            $table->softDeletes();

            $table->timestamps();
        });

        // Tabela de solicitações ocultas (por usuários)
        Schema::create('solicitacao_ocultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('solicitacao_id');
            $table->foreign('solicitacao_id')->references('id')->on('solicitacoes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacao_ocultas');
        Schema::dropIfExists('solicitacoes');
    }
};
