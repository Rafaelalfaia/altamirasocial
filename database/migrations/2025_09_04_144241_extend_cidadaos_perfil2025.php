<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cidadaos', function (Blueprint $t) {
            // Identificação/complementos
            $t->string('unidade_cadastradora')->nullable()->after('status');
            $t->string('responsavel_familiar')->nullable()->after('unidade_cadastradora');
            $t->string('apelido')->nullable()->after('nome');
            $t->string('naturalidade')->nullable()->after('data_nascimento');

            // Endereço/complementos
            $t->string('pontos_referencia')->nullable()->after('complemento');

            // Perfil civil
            $t->string('situacao_civil', 30)->nullable()->after('genero');

            // Território (checkboxes)
            $t->json('equipamentos_proximos')->nullable()->after('situacao_civil');

            // Moradia detalhada
            $t->string('tempo_reside', 30)->nullable()->after('tipo_moradia');
            $t->unsignedTinyInteger('qtde_comodos')->nullable()->after('tempo_reside');
            $t->string('tipo_construcao', 30)->nullable()->after('qtde_comodos');
            $t->string('tipo_via', 30)->nullable()->after('tipo_construcao');

            // Água/Energia qualitativo (mantém booleans existentes)
            $t->string('abastecimento_agua', 30)->nullable()->after('tem_agua_encanada'); // rede, poço, carro pipa...
            $t->string('energia_tipo', 30)->nullable()->after('tem_energia');             // medidor, improvisado, não possui

            // Situações na família
            $t->boolean('ha_gravida')->nullable()->after('pessoas_na_residencia');
            $t->string('nome_gravida')->nullable()->after('ha_gravida');
            $t->boolean('ha_idoso')->nullable()->after('nome_gravida');
            $t->string('nome_idoso')->nullable()->after('ha_idoso');

            // Trabalho
            $t->string('situacao_profissional', 40)->nullable()->after('ocupacao');

            // Acessibilidade detalhada (além do seu tipo_deficiencia varchar)
            $t->json('tipos_deficiencia')->nullable()->after('tipo_deficiencia');

            // Formalização/assinaturas
            $t->text('observacoes_entrevistador')->nullable()->after('observacoes');
            $t->date('data_declaracao')->nullable()->after('observacoes_entrevistador');
            $t->string('nome_declarente')->nullable()->after('data_declaracao');
            $t->string('nome_entrevistador')->nullable()->after('nome_declarente');

            // Contato extra
            $t->string('whatsapp')->nullable()->after('telefone');
        });
    }

    public function down(): void
    {
        Schema::table('cidadaos', function (Blueprint $t) {
            $t->dropColumn([
                'unidade_cadastradora','responsavel_familiar','apelido','naturalidade',
                'pontos_referencia','situacao_civil','equipamentos_proximos',
                'tempo_reside','qtde_comodos','tipo_construcao','tipo_via',
                'abastecimento_agua','energia_tipo',
                'ha_gravida','nome_gravida','ha_idoso','nome_idoso',
                'situacao_profissional','tipos_deficiencia',
                'observacoes_entrevistador','data_declaracao','nome_declarente','nome_entrevistador',
                'whatsapp'
            ]);
        });
    }
};
