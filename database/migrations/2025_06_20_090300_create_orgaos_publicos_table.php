<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orgaos_publicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coordenador_id'); // user_id do coordenador
            $table->string('nome');
            $table->timestamps();

            $table->foreign('coordenador_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('orgaos_publicos');
    }
};;
