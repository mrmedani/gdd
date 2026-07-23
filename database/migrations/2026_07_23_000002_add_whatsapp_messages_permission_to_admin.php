<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $admin = DB::table('roles')->where('name', 'admin')->first();
        if ($admin && $admin->permissions) {
            $perms = json_decode($admin->permissions, true);
            if (is_array($perms)) {
                $perms['whatsapp-messages'] = true;
                DB::table('roles')->where('name', 'admin')->update(['permissions' => json_encode($perms)]);
            }
        }
    }

    public function down(): void
    {
        $admin = DB::table('roles')->where('name', 'admin')->first();
        if ($admin && $admin->permissions) {
            $perms = json_decode($admin->permissions, true);
            if (is_array($perms)) {
                unset($perms['whatsapp-messages']);
                DB::table('roles')->where('name', 'admin')->update(['permissions' => json_encode($perms)]);
            }
        }
    }
};
