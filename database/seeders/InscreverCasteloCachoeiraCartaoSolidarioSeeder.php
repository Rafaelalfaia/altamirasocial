<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Programa;
use App\Models\Cidadao;
use App\Models\ProgramaInscricao;

class InscreverCasteloCachoeiraCartaoSolidarioSeeder extends Seeder
{
    public function run(): void
    {
        $programa = Programa::where('nome', 'Cartão Solidário')->first();

        if (! $programa) {
            $this->command->error('Programa "Cartão Solidário" não encontrado.');
            return;
        }

        // Lista de CPFs dos cidadãos incluídos no seeder anterior
        $cpfs = [
            '29796941260',
            '10987622900',
            '18575701460',
            '78537983500',
            '21997992800',
            '58848562450',
            '22848063920',
            '41907451110',
            '13874392430',
            '33521572600',
            '29309582510',
            '91012551180',
            '82338952960',
            '30080172660',
            '61169631290',
            '21575372010',
            '16887532220',
            '80787483370',
            '80791882200',
            '78795402020',
            '30421071400',
            '30713272260',
            '76617142640',
            '23499123290',
            '29818263790',
            '41645282570',
            '42389731430',
            '15171961670',
            '11467332240',
            '62579341250',
            '80161064180',
            '30040412350',
            '44153852570',
            '32663091030',
            '11201212480',
            '10877592500',
            '40294462300',
            '41641672110',
            '22301631400',
            '53562351860',
            '41859851770',
            '44000322990',
            '29979792930',
            '82669619800',
            '58803611010',
            '40970952260',
            '23080212500',
            '33641031000',
            '73634981210',
            '51952992850',
            '28838001030',
            '24118111950',
            '17290351320',
            '48146561810',
            '43582312410',
            '13889392660',
            '60284671460',
            '29571691690',
            '20516812440',
            '10623612100',
            '42910302660',
        ];

        Cidadao::whereIn('cpf', $cpfs)->pluck('id')->each(function ($cidadaoId) use ($programa) {
            ProgramaInscricao::updateOrCreate(
                [
                    'cidadao_id' => $cidadaoId,
                    'programa_id' => $programa->id,
                ],
                [
                    'status' => 'pendente',
                    'regiao' => 'Castelo dos Sonhos e Cachoeira da Serra',
                ]
            );
        });

        $this->command->info('Inscrições criadas como pendentes para a região "Castelo dos Sonhos e Cachoeira da Serra".');
    }
}
