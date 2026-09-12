<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_closures', function (Blueprint $table) {
            $table->decimal('investments', 15, 2)->default(0)->after('expenses');
        });

        // Octroie la permission 'investments' au rôle admin
        $admin = DB::table('roles')->where('name', 'admin')->first();
        if ($admin && $admin->permissions) {
            $perms = json_decode($admin->permissions, true);
            if (is_array($perms)) {
                $perms['investments'] = true;
                DB::table('roles')->where('name', 'admin')->update(['permissions' => json_encode($perms)]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('monthly_closures', function (Blueprint $table) {
            $table->dropColumn('investments');
        });

        $admin = DB::table('roles')->where('name', 'admin')->first();
        if ($admin && $admin->permissions) {
            $perms = json_decode($admin->permissions, true);
            if (is_array($perms)) {
                unset($perms['investments']);
                DB::table('roles')->where('name', 'admin')->update(['permissions' => json_encode($perms)]);
            }
        }
    }
};
