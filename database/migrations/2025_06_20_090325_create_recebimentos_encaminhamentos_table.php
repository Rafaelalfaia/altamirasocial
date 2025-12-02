<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('recebimentos_encaminhamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coordenador_id'); // quem está fazendo o registro
            $table->unsignedBigInteger('orgao_publico_id');
            $table->enum('tipo', ['recebimento', 'encaminhamento']);
            $table->unsignedBigInteger('cidadao_id')->nullable(); // se for cidadão do sistema
            $table->string('nome_cidadao'); // preenchido sempre, mesmo que exista no sistema
            $table->unsignedBigInteger('programa_social_id')->nullable();
            $table->text('descricao')->nullable();
            $table->timestamps();

            $table->foreign('coordenador_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('orgao_publico_id')->references('id')->on('orgaos_publicos')->onDelete('cascade');
            $table->foreign('cidadao_id')->references('id')->on('cidadaos')->onDelete('set null');
            $table->foreign('programa_social_id')->references('id')->on('programas')->onDelete('set null');

        });
    }

    public function down(): void {
        Schema::dropIfExists('recebimentos_encaminhamentos');
    }
};
