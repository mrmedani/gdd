<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label_ar');
            $table->string('label_fr');
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'admin', 'label_ar' => 'مدير النظام', 'label_fr' => 'Administrateur'],
            ['name' => 'accountant', 'label_ar' => 'محاسب', 'label_fr' => 'Comptable'],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles');
        });

        DB::update("UPDATE users SET role_id = (SELECT id FROM roles WHERE name = 'admin') WHERE role = 'admin'");
        DB::update("UPDATE users SET role_id = (SELECT id FROM roles WHERE name = 'accountant') WHERE role = 'accountant'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->after('id')->nullable();
        });

        DB::update("UPDATE users SET role = (SELECT name FROM roles WHERE id = role_id)");

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
