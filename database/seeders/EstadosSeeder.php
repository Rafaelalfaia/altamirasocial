<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estados')->insert([
            'id' => 1,
            'nome' => 'Pará',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
