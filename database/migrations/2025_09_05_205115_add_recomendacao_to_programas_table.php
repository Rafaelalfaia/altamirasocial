<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            if (!Schema::hasColumn('programas', 'recomendado')) {
                $table->boolean('recomendado')->default(false)->index();
            }
            if (!Schema::hasColumn('programas', 'recomendacao_ordem')) {
                $table->unsignedInteger('recomendacao_ordem')->nullable()->index();
            }
            if (!Schema::hasColumn('programas', 'recomendado_em')) {
                $table->timestamp('recomendado_em')->nullable()->index();
            }
            if (!Schema::hasColumn('programas', 'recomendado_por')) {
                $table->foreignId('recomendado_por')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            if (Schema::hasColumn('programas', 'recomendado_por')) {
                $table->dropConstrainedForeignId('recomendado_por');
            }
            if (Schema::hasColumn('programas', 'recomendado_em')) {
                $table->dropColumn('recomendado_em');
            }
            if (Schema::hasColumn('programas', 'recomendacao_ordem')) {
                $table->dropColumn('recomendacao_ordem');
            }
            if (Schema::hasColumn('programas', 'recomendado')) {
                $table->dropColumn('recomendado');
            }
        });
    }
};
