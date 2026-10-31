<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('station_charges', function (Blueprint $table) {
            // Statut de règlement par station/période : null = pas encore payée,
            // timestamp = payée à cette date (toggle bascule les deux états).
            $table->timestamp('paid_at')->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('station_charges', function (Blueprint $table) {
            $table->dropColumn('paid_at');
        });
    }
};
