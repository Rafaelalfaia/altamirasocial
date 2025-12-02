<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cidades', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <-- CORRETO: logo no início

            $table->id();
            $table->string('nome');
            $table->foreignId('estado_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cidades');
    }
};
