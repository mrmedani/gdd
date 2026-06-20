<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('employees', function (Blueprint $table) {
                $table->index('status');
            });
        } catch (\Exception $e) {
            // Index already exists
        }
        try {
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->index('is_active');
            });
        } catch (\Exception $e) {
            // Index already exists
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }
};
