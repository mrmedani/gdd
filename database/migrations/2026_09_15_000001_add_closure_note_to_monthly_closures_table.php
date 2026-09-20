<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_closures', function (Blueprint $table) {
            $table->text('closure_note')->nullable()->after('closed_by');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_closures', function (Blueprint $table) {
            $table->dropColumn('closure_note');
        });
    }
};
