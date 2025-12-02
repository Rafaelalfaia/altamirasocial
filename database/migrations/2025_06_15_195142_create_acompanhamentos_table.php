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
        Schema::create('acompanhamentos', function (Blueprint $table) {
            $table->id();

            // Relacionamento
            $table->foreignId('cidadao_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // assistente que preencheu

            // Identificação do responsável
            $table->string('nome_unidade')->nullable();
            $table->string('nome_responsavel')->nullable();
            $table->string('apelido')->nullable();
            $table->enum('sexo', ['Feminino', 'Masculino'])->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('naturalidade')->nullable();
            $table->string('cpf')->nullable();
            $table->string('nis')->nullable();
            $table->string('rg')->nullable();
            $table->string('orgao_emissor')->nullable();
            $table->date('data_emissao')->nullable();
            $table->string('titulo_eleitor')->nullable();
            $table->string('zona')->nullable();
            $table->string('secao')->nullable();
            $table->string('codigo_cadunico')->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('ponto_referencia')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('whatsapp')->nullable();

            // Perguntas
            $table->string('cor')->nullable();
            $table->json('equipamentos_comunitarios')->nullable();
            $table->string('situacao_moradia')->nullable();
            $table->string('tempo_residencia')->nullable();
            $table->string('quantidade_comodos')->nullable();
            $table->string('tipo_construcao')->nullable();
            $table->string('energia')->nullable();
            $table->string('agua')->nullable();
            $table->string('esgoto')->nullable();
            $table->string('lixo')->nullable();
            $table->string('tipo_rua')->nullable();
            $table->boolean('possui_gravida')->nullable();
            $table->string('nome_gravida')->nullable();
            $table->boolean('possui_idoso')->nullable();
            $table->string('nome_idoso')->nullable();
            $table->string('situacao_profissional')->nullable();
            $table->boolean('possui_deficiencia')->nullable();
            $table->json('tipos_deficiencia')->nullable();

            // Observações gerais
            $table->text('observacoes')->nullable();

            // Composição familiar (registraremos em outra tabela, mais limpa)
            // $table->json('composicao_familiar')->nullable(); // OU criar tabela separada

            $table->date('data')->default(now());
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acompanhamentos', function (Blueprint $table) {
            //
        });
    }
};
