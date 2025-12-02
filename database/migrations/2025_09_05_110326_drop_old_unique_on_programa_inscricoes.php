<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // remove o UNIQUE antigo (programa_id, cidadao_id)
        try {
            DB::statement("
                ALTER TABLE programa_inscricoes
                DROP INDEX programa_inscricoes_programa_id_cidadao_id_unique
            ");
        } catch (\Throwable $e) {
            // já removido? segue o baile
        }
    }

    public function down(): void
    {
        // recria o UNIQUE antigo (se for preciso fazer rollback)
        try {
            DB::statement("
                ALTER TABLE programa_inscricoes
                ADD UNIQUE INDEX programa_inscricoes_programa_id_cidadao_id_unique (programa_id, cidadao_id)
            ");
        } catch (\Throwable $e) {}
    }
};
