<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('cidadaos', function (Blueprint $table) {
        $table->boolean('pcd')->default(false)->after('escolaridade'); // ou qualquer coluna existente
    });
}

public function down()
{
    Schema::table('cidadaos', function (Blueprint $table) {
        $table->dropColumn('pcd');
    });
}

};
