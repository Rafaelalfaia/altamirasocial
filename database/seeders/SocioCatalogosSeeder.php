<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocioCatalogosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('equipamentos')->upsert([
            ['id'=>1,'nome'=>'Quadra de esportes'],
            ['id'=>2,'nome'=>'Escola'],
            ['id'=>3,'nome'=>'Posto de saúde'],
            ['id'=>4,'nome'=>'Praça'],
            ['id'=>5,'nome'=>'Igreja'],
            ['id'=>6,'nome'=>'Creche'],
            ['id'=>7,'nome'=>'Centro comunitário'],
            ['id'=>99,'nome'=>'Outros'],
        ], ['id'], ['nome']);

        DB::table('deficiencias')->upsert([
            ['id'=>1,'nome'=>'Deficiência visual'],
            ['id'=>2,'nome'=>'Deficiência auditiva'],
            ['id'=>3,'nome'=>'Deficiência mental/intelectual'],
            ['id'=>4,'nome'=>'Síndrome de Down'],
            ['id'=>5,'nome'=>'Deficiências múltiplas'],
            ['id'=>6,'nome'=>'Deficiência física'],
            ['id'=>7,'nome'=>'Transtorno/Doença mental'],
            ['id'=>99,'nome'=>'Outras'],
        ], ['id'], ['nome']);
    }
}
