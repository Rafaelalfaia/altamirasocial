<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lote_pagamentos', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->foreignId('programa_id')->constrained('programas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Dados do lote
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->enum('status', ['Pendente', 'Aprovado', 'Rejeitado'])->default('Pendente');
            $table->date('data_envio')->nullable();
            $table->date('previsao_pagamento')->nullable(); // Adicionado da migration extra

            // Valores e formato
            $table->decimal('valor_pagamento', 10, 2)->default(0); // Já estava na primeira
            $table->enum('formato_cpf', ['com_pontos', 'sem_pontos'])->default('com_pontos'); // Adicionado da segunda
            $table->string('periodo_pagamento')->nullable();
            $table->string('regiao')->nullable(); // Região filtrada

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_pagamentos');
    }
};
