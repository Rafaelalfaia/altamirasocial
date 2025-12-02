<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CidadesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cidades')->insert([
            'id' => 1,
            'nome' => 'Altamira',
            'estado_id' => 1, // Garanta que o estado 1 também exista!
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
