<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cidadao;

class CidadaosNovoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cidadaos = [
            ['nome' => 'Ana Ilce Acacio da Silva', 'cpf' => '39544273204'] ,
            ['nome' => 'Andrea Ramos da Silva', 'cpf' => '65966627215'] ,
            ['nome' => 'Antonia Maria Gomes Bezerra', 'cpf' => '33329877200'] ,
            ['nome' => 'Bernardino Ferreira dos Santos', 'cpf' => '33343080225'] ,
            ['nome' => 'Carla Paloma da Costa Pereira', 'cpf' => '97045985215'] ,
            ['nome' => 'Dimona Maia Vilela', 'cpf' => '85228621253'] ,
            ['nome' => 'Edina Maria Bertoldo Gomes', 'cpf' => '49432796353'] ,
            ['nome' => 'Francisca Crisostomo Nascimento', 'cpf' => '27880710272'] ,
            ['nome' => 'Frankeslene Umbelino de Araujo', 'cpf' => '52626695220'] ,
            ['nome' => 'Herbeson Monteiro Brandão', 'cpf' => '73962074287'] ,
            ['nome' => 'Ilce Mara Azevedo dos Santos', 'cpf' => '78450080282'] ,
            ['nome' => 'Ivoneide Pereira de Souza', 'cpf' => '27745570204'] ,
            ['nome' => 'Jaciele de Souza Moreira', 'cpf' => '54856370278'] ,
            ['nome' => 'Jeriana Rodrigues Sousa', 'cpf' => '78694388200'] ,
            ['nome' => 'Josefa Acácio de Oliveira', 'cpf' => '90357833287'] ,
            ['nome' => 'Jozelma de Souza Acosta', 'cpf' => '98840126287'] ,
            ['nome' => 'Juliana Gonçalves Muniz', 'cpf' => '54856396234'] ,
            ['nome' => 'Leylane de Araújo Silva', 'cpf' => '94489882220'] ,
            ['nome' => 'Lidiane Lopes Constante', 'cpf' => '97889040253'] ,
            ['nome' => 'Lucicleudi Mota de Sousa', 'cpf' => '95761373220'] ,
            ['nome' => 'Maíssera Angela de Souza Evangelista', 'cpf' => '83864628253'] ,
            ['nome' => 'Maria Adeladia Barros Ferreira do Nascimento', 'cpf' => '62854240200'] ,
            ['nome' => 'Maria Natalina Rodrigues Silva Cezar', 'cpf' => '51559722215'] ,
            ['nome' => 'Marilda Nascimento de Sousa', 'cpf' => '70959916261'] ,
            ['nome' => 'Neurismar de Sousa Monteiro', 'cpf' => '97477486220'] ,
            ['nome' => 'Neusa Aparecida dos Santos Fereira', 'cpf' => '69593787968'] ,
            ['nome' => 'Perpetua dos Santos Freitas', 'cpf' => '33333580268'] ,
            ['nome' => 'Regiane Terezinha Perdosine Barros', 'cpf' => '51777282268'] ,
            ['nome' => 'Rosalina Ferreira de Araújo', 'cpf' => '25300245291'] ,
            ['nome' => 'Rosangela Vanusa Galvão de Oliveira', 'cpf' => '88212505200'] ,
            ['nome' => 'Rosilene Ferreira de Souza', 'cpf' => '82061025234'] ,
            ['nome' => 'Sara Eyla Araújo Almeida', 'cpf' => '62424375380'] ,
            ['nome' => 'Silva Helena da Silva Pessoa', 'cpf' => '86493256291'] ,
            ['nome' => 'Tatiana Costa Cardoso', 'cpf' => '70245826262'] ,
            ['nome' => 'Derismar dos Santos Rodrigues', 'cpf' => '54680425253'] ,
            ['nome' => 'Elciane Ferreira de Oliveira', 'cpf' => '78849829272'] ,
            ['nome' => 'Eliane Pereira Silva', 'cpf' => '98464892268'] ,
            ['nome' => 'Fabiana Almeida Souza dos Santos', 'cpf' => '60159741335'] ,
            ['nome' => 'Ivanilde Damasceno Borges', 'cpf' => '67154867272'] ,
            ['nome' => 'Maria Liduina Santos Viana', 'cpf' => '46306110291'] ,
            ['nome' => 'Maria Machado Silva', 'cpf' => '59000945291'] ,
            ['nome' => 'Paula Rabelo de Souza', 'cpf' => '80782965253'] ,
            ['nome' => 'Silvana Andrade da Silva', 'cpf' => '53377265287'] ,
            ['nome' => 'Sonia Vendramine', 'cpf' => '40738809268'] ,
            ['nome' => 'Suely de Sousa Santos', 'cpf' => '54697727215'] ,
            ['nome' => 'Taiane Pamela Santos', 'cpf' => '70696456192'] ,
            ['nome' => 'Talita Santos de Souza Silva', 'cpf' => '70696461196'] ,
        ];

        foreach ($cidadaos as $c) {
            $user = User::firstOrCreate(
                ['cpf' => $c['cpf']],
                [
                    'name'     => $c['nome'],
                    'email'    => null,
                    'password' => Hash::make($c['cpf']),
                ]
            );

            if ($user->wasRecentlyCreated === false) {
                $user->name = $c['nome'];
                $user->save();
            }

            if (method_exists($user, 'assignRole')) {
                try { $user->assignRole('Cidadao'); } catch (\Throwable $e) { /* ignore */ }
            }

            Cidadao::updateOrCreate(
                ['cpf' => $c['cpf']],
                ['user_id' => $user->id, 'nome' => $c['nome']]
            );
        }
    }
}
