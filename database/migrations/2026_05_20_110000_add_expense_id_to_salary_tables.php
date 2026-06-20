<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_advances', function (Blueprint $table) {
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
        });

        Schema::table('salary_payments', function (Blueprint $table) {
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('salary_advances', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
            $table->dropColumn('expense_id');
        });

        Schema::table('salary_payments', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
            $table->dropColumn('expense_id');
        });
    }
};
