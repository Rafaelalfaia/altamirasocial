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
        Schema::create('cidadaos', function (Blueprint $table) {
            $table->id();

            // Relação com usuário que faz login
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Responsável (Assistente)
            $table->foreignId('user_id_responsavel')->nullable()->constrained('users')->onDelete('set null');

            // Identificação básica
            $table->string('nome');
            $table->string('cpf')->unique();
            $table->string('status')->default('pendente');

            // Localização
            $table->unsignedBigInteger('bairro_id')->nullable();
            $table->foreign('bairro_id')->references('id')->on('bairros')->onDelete('set null');
            $table->string('regiao')->nullable();

            // Dados pessoais
            $table->date('data_nascimento')->nullable();
            $table->enum('sexo', ['Masculino', 'Feminino', 'Outro'])->nullable();
            $table->string('genero')->nullable(); // campo adicional vindo da segunda migration
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('cep')->nullable();
            $table->string('rua')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('foto')->nullable();

            // Moradia
            $table->string('tipo_moradia')->nullable();
            $table->boolean('tem_esgoto')->default(false);
            $table->boolean('tem_agua_encanada')->default(false);
            $table->boolean('tem_coleta_lixo')->default(false);
            $table->boolean('tem_energia')->default(false);

            // Família e trabalho
            $table->integer('pessoas_na_residencia')->nullable();
            $table->string('ocupacao')->nullable();
            $table->string('grau_parentesco')->nullable();
            $table->string('escolaridade')->nullable();

            // Renda
            $table->decimal('renda', 8, 2)->nullable();
            $table->decimal('renda_total_familiar', 10, 2)->nullable();

            // Animais
            $table->boolean('possui_animais')->default(false);
            $table->integer('numero_animais')->nullable();

            // Documentos
            $table->string('cor_raca')->nullable();
            $table->string('nis')->nullable();
            $table->string('rg')->nullable();
            $table->string('orgao_emissor')->nullable();
            $table->date('data_emissao_rg')->nullable();
            $table->string('titulo_eleitor')->nullable();
            $table->string('zona')->nullable();
            $table->string('secao')->nullable();
            $table->string('codigo_cadunico')->nullable();

            // Acessibilidade
            $table->boolean('possui_deficiencia')->nullable();
            $table->string('tipo_deficiencia')->nullable();
            $table->string('cid')->nullable();

            // Observações
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cidadaos');
    }
};
