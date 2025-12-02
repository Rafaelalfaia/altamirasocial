<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('venda_restaurantes', function (Blueprint $table) {
            $table->boolean('doacao')->default(false)->after('forma_pagamento');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venda_restaurantes', function (Blueprint $table) {
            //
        });
    }
};
