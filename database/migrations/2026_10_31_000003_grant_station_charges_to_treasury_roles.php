<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Bascule RBAC : la page « Commissions stations » passe de la permission
 * 'treasury' à la permission dédiée 'station-charges'.
 * Migration de données : chaque rôle qui avait treasury=true reçoit
 * station-charges=true — personne ne perd ni ne gagne d'accès par ce découplage.
 * Après, l'admin peut ajuster finement par rôle dans /settings/roles.
 */
return new class extends Migration
{
    public function up(): void
    {
        $roles = DB::table('roles')->get(['id', 'permissions']);
        foreach ($roles as $role) {
            $permissions = $role->permissions !== null ? json_decode($role->permissions, true) : null;
            if (!is_array($permissions)) {
                continue; // JSON null = accès total (admin par défaut) : rien à faire
            }
            $permissions['station-charges'] = ($permissions['treasury'] ?? false) === true
                || ($permissions['station-charges'] ?? false) === true;
            DB::table('roles')->where('id', $role->id)->update([
                'permissions' => json_encode($permissions),
            ]);
        }
    }

    public function down(): void
    {
        $roles = DB::table('roles')->get(['id', 'permissions']);
        foreach ($roles as $role) {
            $permissions = $role->permissions !== null ? json_decode($role->permissions, true) : null;
            if (!is_array($permissions) || !array_key_exists('station-charges', $permissions)) {
                continue;
            }
            unset($permissions['station-charges']);
            DB::table('roles')->where('id', $role->id)->update([
                'permissions' => json_encode($permissions),
            ]);
        }
    }
};
