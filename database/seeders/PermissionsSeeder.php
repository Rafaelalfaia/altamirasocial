<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa cache interno do Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        // Permissão macro (mantida)
        $principais = [
            'programas.admin',
        ];

        // Permissões granulares do módulo admin (mantidas)
        $granularesAdmin = [
            'programas.admin.index',
            'programas.admin.show',
            'programas.admin.destacar',
            'programas.admin.ordenar',
            'programas.admin.informacoes',
            'programas.admin.relatorios',
            'programas.admin.destroy',
        ];


        $crudPublico = [
            'programas.listar',
            'programas.criar',
            'programas.editar',
            'programas.excluir',
            'programas.recomendar',
        ];

        $todasNomes = array_values(array_unique(array_merge(
            $principais,
            $granularesAdmin,
            $crudPublico
        )));

        // Cria todas as permissões (sem duplicar)
        foreach ($todasNomes as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard]
            );
        }

        // Roles padrão
        $roles = [
            'Admin',
            'Coordenador',
            'Tecnico',
            'Assistente',
            'Atendente',
            'Cidadao',
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => $guard]);
        }

        // Admin recebe TODAS as permissões acima
        $roleAdmin = Role::where('name', 'Admin')->where('guard_name', $guard)->first();
        if ($roleAdmin) {
            $todas = Permission::whereIn('name', $todasNomes)->where('guard_name', $guard)->get();
            $roleAdmin->syncPermissions($todas);
        }



        // Recarrega cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
