<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            if (!Schema::hasColumn('alerts', 'action_url')) {
                $table->string('action_url')->nullable()->after('data');
            }
            if (!Schema::hasColumn('alerts', 'action_label')) {
                $table->string('action_label')->nullable()->after('action_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn(['action_url', 'action_label']);
        });
    }
};
