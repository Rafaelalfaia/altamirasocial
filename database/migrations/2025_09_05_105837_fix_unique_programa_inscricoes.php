<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // (0) Garante que dependente_id aceita NULL (titular)
        try {
            DB::statement("ALTER TABLE programa_inscricoes MODIFY dependente_id BIGINT UNSIGNED NULL");
        } catch (\Throwable $e) {}

        // (1) Drop de índices antigos (por colunas e por nome, se existir)
        try {
            Schema::table('programa_inscricoes', function (Blueprint $table) {
                $table->dropUnique(['programa_id', 'cidadao_id']);
            });
        } catch (\Throwable $e) {}
        try {
            DB::statement("ALTER TABLE programa_inscricoes DROP INDEX programa_inscricoes_programa_id_cidadao_id_unique");
        } catch (\Throwable $e) {}
        try {
            DB::statement("ALTER TABLE programa_inscricoes DROP INDEX ux_prog_cid_dep_guard");
        } catch (\Throwable $e) {}
        try {
            DB::statement("ALTER TABLE programa_inscricoes DROP INDEX ux_prog_cid_dep_fn");
        } catch (\Throwable $e) {}

        // (2) Preferível: ÍNDICE FUNCIONAL (MySQL 8+/MariaDB com suporte)
        $functionalOk = false;
        try {
            DB::statement("
                CREATE UNIQUE INDEX ux_prog_cid_dep_fn
                ON programa_inscricoes (programa_id, cidadao_id, (COALESCE(dependente_id, 0)))
            ");
            $functionalOk = true;
        } catch (\Throwable $e) {
            $functionalOk = false;
        }

        if ($functionalOk) {
            return; // terminou aqui, sem coluna auxiliar
        }

        // (3) Fallback: tentar COLUNA GERADA (MySQL/MariaDB)
        $colCreated = false;
        if (!Schema::hasColumn('programa_inscricoes', 'dep_guard')) {
            try {
                // MySQL
                DB::statement("
                    ALTER TABLE programa_inscricoes
                    ADD COLUMN dep_guard BIGINT UNSIGNED
                    GENERATED ALWAYS AS (COALESCE(dependente_id, 0)) STORED
                ");
                $colCreated = true;
            } catch (\Throwable $e1) {
                try {
                    // MariaDB
                    DB::statement("
                        ALTER TABLE programa_inscricoes
                        ADD COLUMN dep_guard BIGINT
                        AS (IFNULL(dependente_id, 0)) PERSISTENT
                    ");
                    $colCreated = true;
                } catch (\Throwable $e2) {
                    // (4) Último fallback: COLUNA FÍSICA + TRIGGERS
                    Schema::table('programa_inscricoes', function (Blueprint $table) {
                        $table->unsignedBigInteger('dep_guard')->nullable()->after('dependente_id');
                    });
                    DB::statement("UPDATE programa_inscricoes SET dep_guard = COALESCE(dependente_id, 0)");
                    DB::statement("ALTER TABLE programa_inscricoes MODIFY dep_guard BIGINT UNSIGNED NOT NULL");

                    // Gatilhos para manter dep_guard sincronizado
                    DB::unprepared("
                        CREATE TRIGGER bi_programa_inscricoes_dep_guard
                        BEFORE INSERT ON programa_inscricoes
                        FOR EACH ROW
                        SET NEW.dep_guard = COALESCE(NEW.dependente_id, 0)
                    ");
                    DB::unprepared("
                        CREATE TRIGGER bu_programa_inscricoes_dep_guard
                        BEFORE UPDATE ON programa_inscricoes
                        FOR EACH ROW
                        SET NEW.dep_guard = COALESCE(NEW.dependente_id, 0)
                    ");

                    $colCreated = true;
                }
            }
        }

        // (5) Índice único final usando dep_guard
        if ($colCreated) {
            try {
                DB::statement("
                    CREATE UNIQUE INDEX ux_prog_cid_dep_guard
                    ON programa_inscricoes (programa_id, cidadao_id, dep_guard)
                ");
            } catch (\Throwable $e) {}
        }
    }

    public function down(): void
    {
        // Drop índices novos
        try { DB::statement("DROP INDEX ux_prog_cid_dep_fn ON programa_inscricoes"); } catch (\Throwable $e) {}
        try { DB::statement("DROP INDEX ux_prog_cid_dep_guard ON programa_inscricoes"); } catch (\Throwable $e) {}

        // Drop triggers (se existirem)
        try { DB::unprepared("DROP TRIGGER IF EXISTS bi_programa_inscricoes_dep_guard"); } catch (\Throwable $e) {}
        try { DB::unprepared("DROP TRIGGER IF EXISTS bu_programa_inscricoes_dep_guard"); } catch (\Throwable $e) {}

        // Drop coluna auxiliar
        if (Schema::hasColumn('programa_inscricoes', 'dep_guard')) {
            Schema::table('programa_inscricoes', function (Blueprint $table) {
                $table->dropColumn('dep_guard');
            });
        }

        // Recria o índice antigo (programa_id, cidadao_id)
        try {
            Schema::table('programa_inscricoes', function (Blueprint $table) {
                $table->unique(['programa_id', 'cidadao_id'], 'programa_inscricoes_programa_id_cidadao_id_unique');
            });
        } catch (\Throwable $e) {}
    }
};
