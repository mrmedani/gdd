<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = DB::table('roles')->get();
        foreach ($roles as $role) {
            $perms = json_decode($role->permissions ?? '{}', true);
            if (!isset($perms['delete-closure']) && $role->name === 'admin') {
                $perms['delete-closure'] = true;
                DB::table('roles')->where('id', $role->id)->update(['permissions' => json_encode($perms)]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
