<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cidadao;
use App\Models\Programa;
use App\Models\ProgramaInscricao;

class InscreverAltamiraCartaoSolidarioSeeder extends Seeder
{
    public function run(): void
    {
        $programa = Programa::where('nome', 'Cartão Solidário')->first();

        if (!$programa) {
            $this->command->error('Programa "Cartão Solidário" não encontrado.');
            return;
        }

        Cidadao::pluck('id')->each(function ($cidadaoId) use ($programa) {
            ProgramaInscricao::updateOrCreate(
                [
                    'cidadao_id' => $cidadaoId,
                    'programa_id' => $programa->id,
                ],
                [
                    'status' => 'pendente',
                    'regiao' => 'Altamira'
                ]
            );
        });

        $this->command->info('Todos os cidadãos foram inscritos como pendentes no programa "Cartão Solidário" com a região Altamira.');
    }
}
