<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Cidadao;
use App\Models\User;

class LimparCidadaosCpfCurtoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Pega todos os cidadãos cujo CPF tem menos de 11 dígitos
            $cidadaosInvalidos = Cidadao::query()
                ->whereRaw('CHAR_LENGTH(cpf) < 11')
                ->get(['id','user_id','cpf']);

            if ($cidadaosInvalidos->isEmpty()) {
                $this->command->info('Nenhum cidadão com CPF curto encontrado.');
                return;
            }

            $userIds = $cidadaosInvalidos->pluck('user_id')->filter()->unique();

            // --- Dependências antes de apagar (se existirem no seu sistema) ---
            // DB::table('vendas')->whereIn('cidadao_id', $cidadaosInvalidos->pluck('id'))->delete();
            // DB::table('cidadao_restaurante')->whereIn('cidadao_id', $cidadaosInvalidos->pluck('id'))->delete();

            // Apaga os cidadãos inválidos
            DB::table('cidadaos')->whereIn('id', $cidadaosInvalidos->pluck('id'))->delete();

            // Remove a role "Cidadao" desses usuários (se usar Spatie/Permission)
            try {
                $roleId = DB::table('roles')->where('name', 'Cidadao')->value('id');
                if ($roleId) {
                    DB::table('model_has_roles')
                        ->where('role_id', $roleId)
                        ->where('model_type', User::class)
                        ->whereIn('model_id', $userIds)
                        ->delete();
                }
            } catch (\Throwable $e) {
                // Ignora se não usa Spatie
            }

            // (Opcional) apagar os usuários que só tinham essa role
            // DB::table('users')->whereIn('id', $userIds)->delete();

            $this->command->info(count($cidadaosInvalidos) . ' cidadãos com CPF curto foram removidos.');
        });
    }
}
