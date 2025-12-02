<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cidadao;

class CidadaosCasteloECachoeiraSeeder extends Seeder
{
    public function run(): void
    {
        $cidadaos = [
            ['nome' => 'Ana Lucia de Souza Barros', 'cpf' => '29796941260', 'telefone' => '93981142795'],
            ['nome' => 'Ana Paula Masson', 'cpf' => '10987622900', 'telefone' => '66981170180'],
            ['nome' => 'Antonia Maria de Jesus', 'cpf' => '18575701460', 'telefone' => '93981136754'],
            ['nome' => 'Brenda Karla da Costa Oliveira', 'cpf' => '78537983500', 'telefone' => '93981313381'],
            ['nome' => 'Carla Mitie Alves dos Santos', 'cpf' => '21997992800', 'telefone' => '93981197649'],
            ['nome' => 'Claudiane Brilhante Moura', 'cpf' => '58848562450', 'telefone' => '93981264994'],
            ['nome' => 'Cristiane Teixeira da Silva', 'cpf' => '22848063920', 'telefone' => '93981220167'],
            ['nome' => 'Debora Alves Souza', 'cpf' => '41907451110', 'telefone' => '93981091242'],
            ['nome' => 'Debora Silva de Lima', 'cpf' => '13874392430', 'telefone' => '93981272225'],
            ['nome' => 'Derlane Nascimento Berger', 'cpf' => '33521572600', 'telefone' => '93981151640'],
            ['nome' => 'Elaine da Silva Abreu', 'cpf' => '29309582510', 'telefone' => '93981132787'],
            ['nome' => 'Eliane de Andrade', 'cpf' => '91012551180', 'telefone' => '93981299103'],
            ['nome' => 'Emilly Samilly Akay Silva', 'cpf' => '82338952960', 'telefone' => '93981179409'],
            ['nome' => 'Emilly Vitória Almeida Santos', 'cpf' => '30080172660', 'telefone' => '93981179020'],
            ['nome' => 'Erica Dal Aquia de Castello', 'cpf' => '61169631290', 'telefone' => '93981129728'],
            ['nome' => 'Geane dos Santos Vilarim', 'cpf' => '21575372010', 'telefone' => '93981292063'],
            ['nome' => 'Gracieli Bezerra Ribeiro', 'cpf' => '16887532220', 'telefone' => '93981196108'],
            ['nome' => 'Iorrana Conceição Coelho', 'cpf' => '80787483370', 'telefone' => '93981297323'],
            ['nome' => 'Joaquim Silva dos Santos', 'cpf' => '80791882200', 'telefone' => null],
            ['nome' => 'Julia Andrade dos Santos', 'cpf' => '78795402020', 'telefone' => '66996714565'],
            ['nome' => 'Keila Calixtro do Amaral', 'cpf' => '30421071400', 'telefone' => '93981035220'],
            ['nome' => 'Margelanha dos Santos Silva', 'cpf' => '30713272260', 'telefone' => '93981157632'],
            ['nome' => 'Maria Daniele Uchôa Alves', 'cpf' => '76617142640', 'telefone' => '93981095700'],
            ['nome' => 'Maria Josélia Conceição dos Santos', 'cpf' => '23499123290', 'telefone' => '93981303637'],
            ['nome' => 'Maria Zilma de Sa', 'cpf' => '29818263790', 'telefone' => '93981136178'],
            ['nome' => 'Marineide dos Santos Magno', 'cpf' => '41645282570', 'telefone' => null],
            ['nome' => 'Marlene Cristina Ogeda', 'cpf' => '42389731430', 'telefone' => '93981035302'],
            ['nome' => 'Mercia Pauliana Alves Lima', 'cpf' => '15171961670', 'telefone' => null],
            ['nome' => 'Neila Patricia Braga Lopes', 'cpf' => '11467332240', 'telefone' => '93981237263'],
            ['nome' => 'Nubia Caroline Araujo Cruz', 'cpf' => '62579341250', 'telefone' => '9381182595'],
            ['nome' => 'Sirlene da Costa Marinho', 'cpf' => '80161064180', 'telefone' => '93981120619'],
            ['nome' => 'Solange Pereira da Silva', 'cpf' => '30040412350', 'telefone' => '66998345038'],
            ['nome' => 'Suelen da Silva Regis', 'cpf' => '44153852570', 'telefone' => '93981172082'],
            ['nome' => 'Sueli Oliveira da Silva', 'cpf' => '32663091030', 'telefone' => '9393002839'],
            ['nome' => 'Tatiane Maria da Conceição', 'cpf' => '11201212480', 'telefone' => '93981156937'],
            ['nome' => 'Tatiane Ribeiro Mernitzki', 'cpf' => '10877592500', 'telefone' => '93981312185'],
            ['nome' => 'Valnessa de Carvalho Almeida', 'cpf' => '40294462300', 'telefone' => '93981170483'],
            ['nome' => 'Vauleide Almeida Silva', 'cpf' => '41641672110', 'telefone' => '66981395244'],
            ['nome' => 'Vilerio Feitosa de Andrade', 'cpf' => '22301631400', 'telefone' => null],
            ['nome' => 'Wuslane Natieli Silva de Freitas', 'cpf' => '53562351860', 'telefone' => null],
            ['nome' => 'Ângela Maria Vaz', 'cpf' => '41859851770', 'telefone' => '66997238908'],
            ['nome' => 'Beatriz Rodrigues Pinto', 'cpf' => '44000322990', 'telefone' => '66981311422'],
            ['nome' => 'Claudilene Ferreira Ferraz', 'cpf' => '29979792930', 'telefone' => '93981030570'],
            ['nome' => 'Daniele da Silva', 'cpf' => '82669619800', 'telefone' => '93981303714'],
            ['nome' => 'Debora Ramos Mendes Costa', 'cpf' => '58803611010', 'telefone' => '67998963775'],
            ['nome' => 'Dileuza Pinheiro Nunes', 'cpf' => '40970952260', 'telefone' => '66981011066'],
            ['nome' => 'Eleonora Oliveira dos Santos', 'cpf' => '23080212500', 'telefone' => '93981033729'],
            ['nome' => 'Eliza Aparecida Rogelin', 'cpf' => '33641031000', 'telefone' => '93981162915'],
            ['nome' => 'Fernanda Aparecida da Silva', 'cpf' => '73634981210', 'telefone' => '66999122110'],
            ['nome' => 'Fernanda Gois da Silva', 'cpf' => '51952992850', 'telefone' => '97981108254'],
            ['nome' => 'Francisca Pereira Lima', 'cpf' => '28838001030', 'telefone' => '66996783445'],
            ['nome' => 'Giliane Fernandes Dias', 'cpf' => '24118111950', 'telefone' => '93981004840'],
            ['nome' => 'Heleni Souza de Moura', 'cpf' => '17290351320', 'telefone' => '66992363843'],
            ['nome' => 'Hortencia dos Santos Liones', 'cpf' => '48146561810', 'telefone' => '93981118197'],
            ['nome' => 'Iara de Jesus Pereira', 'cpf' => '43582312410', 'telefone' => '66996963903'],
            ['nome' => 'Kamila Regina Parente Oliveira', 'cpf' => '13889392660', 'telefone' => '66981175426'],
            ['nome' => 'Lindaci Cseslikoski', 'cpf' => '60284671460', 'telefone' => '66997227176'],
            ['nome' => 'Luciana de Sousa', 'cpf' => '29571691690', 'telefone' => '66999658589'],
            ['nome' => 'Maria Marciana de Souza Pereira', 'cpf' => '20516812440', 'telefone' => '91991398184'],
            ['nome' => 'Marinalva Conceição', 'cpf' => '10623612100', 'telefone' => '66981319173'],
            ['nome' => 'Naiara Leite do Nascimento', 'cpf' => '42910302660', 'telefone' => '66981291678'],
        ];

        foreach ($cidadaos as $c) {
            $cpf = preg_replace('/\D/', '', $c['cpf']);
            if (strlen($cpf) !== 11) {
                continue;
            }

            $telefone = $c['telefone'];
            if ($telefone !== null) {
                $telefone = preg_replace('/\D/', '', $telefone);
                $telefone = substr($telefone, 0, 15);
                if (strlen($telefone) < 10) {
                    $telefone = null;
                }
            }

            if (User::where('cpf', $cpf)->exists()) {
                continue;
            }

            $user = User::create([
                'name' => $c['nome'],
                'cpf' => $cpf,
                'telefone' => $telefone,
                'password' => Hash::make($cpf),
            ]);

            $user->assignRole('Cidadao');

            Cidadao::create([
                'user_id' => $user->id,
                'nome' => $c['nome'],
                'cpf' => $cpf,
                'telefone' => $telefone,
            ]);
        }
    }
}
