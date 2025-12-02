<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venda_restaurantes', function (Blueprint $table) {
            $table->id();

            // Restaurante (usuário com papel "restaurante")
            $table->foreignId('restaurante_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Identifica se o cliente é um cidadão regular ou temporário
            $table->enum('tipo_cliente', ['cidadao', 'temporario']);

            // FK para cidadão regular
            $table->foreignId('cidadao_id')
                ->nullable()
                ->constrained('cidadaos')
                ->nullOnDelete();

            // FK para cidadão temporário
            $table->foreignId('cidadao_temporario_id')
                ->nullable()
                ->constrained('cidadaos_temporarios')
                ->nullOnDelete();

            // Informações da venda
            $table->unsignedInteger('numero_pratos')->default(1);
            $table->enum('tipo_consumo', ['local', 'retirada']);
            $table->enum('forma_pagamento', ['pix', 'debito', 'credito', 'dinheiro', 'doacao']);

            // Data/hora da venda
            $table->timestamp('data_venda')->useCurrent();

            // Finalização do dia (venda incluída no fechamento ou não)
            $table->boolean('finalizado')->default(false);

            // Se é estudante
            $table->boolean('estudante')->default(false);

            $table->timestamps();

            // Índices úteis para relatórios
            $table->index(['restaurante_id', 'data_venda']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venda_restaurantes');
    }
};
