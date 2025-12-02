<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cidadaos', function (Blueprint $table) {
            // torna o cpf anulável; preserva o unique já existente
            $table->string('cpf', 14)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cidadaos', function (Blueprint $table) {
            $table->string('cpf', 14)->nullable(false)->change();
        });
    }
};
