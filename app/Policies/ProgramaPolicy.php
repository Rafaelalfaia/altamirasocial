<?php

namespace App\Policies;

use App\Models\Programa;
use App\Models\User;

class ProgramaPolicy
{
    /**
     * Qualquer usuário autenticado pode listar os programas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Pode ver se for o dono ou se for Admin.
     */
    public function view(User $user, Programa $programa): bool
    {
        return $user->id === $programa->user_id || $user->hasRole('Admin');
    }

    /**
     * Apenas Coordenadores podem criar programas.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Coordenador');
    }

    /**
     * Pode atualizar se for dono ou Admin.
     */
    public function update(User $user, Programa $programa): bool
    {
        return $user->id === $programa->user_id || $user->hasRole('Admin');
    }

    /**
     * Pode deletar se for dono ou Admin.
     */
    public function delete(User $user, Programa $programa): bool
    {
        return $user->id === $programa->user_id || $user->hasRole('Admin');
    }

    /**
     * Restore desativado por padrão.
     */
    public function restore(User $user, Programa $programa): bool
    {
        return false;
    }

    /**
     * Exclusão permanente desabilitada por padrão.
     */
    public function forceDelete(User $user, Programa $programa): bool
    {
        return false;
    }
}
