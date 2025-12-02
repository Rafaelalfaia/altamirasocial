<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Admin', 'Coordenador', 'Assistente', 'Cidadao', 'Coordenador Restaurante','Atendente Restaurante'];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role, 'guard_name' => 'web']
            );
        }
    }

}
