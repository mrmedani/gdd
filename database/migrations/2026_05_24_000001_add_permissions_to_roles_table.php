<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('label_fr');
        });

        $adminPermissions = [
            'dashboard' => true,
            'expenses' => true,
            'treasury' => true,
            'employees' => true,
            'reports' => true,
            'settings' => true,
            'categories' => true,
            'users' => true,
            'roles' => true,
            'audit-logs' => true,
            'email-templates' => true,
        ];

        $accountantPermissions = [
            'dashboard' => true,
            'expenses' => true,
            'treasury' => true,
            'employees' => false,
            'reports' => true,
            'settings' => false,
            'categories' => false,
            'users' => false,
            'roles' => false,
            'audit-logs' => false,
            'email-templates' => false,
        ];

        DB::table('roles')->where('name', 'admin')->update(['permissions' => json_encode($adminPermissions)]);
        DB::table('roles')->where('name', 'accountant')->update(['permissions' => json_encode($accountantPermissions)]);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
