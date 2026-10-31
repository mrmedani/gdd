<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commitments', function (Blueprint $table) {
            // 'monthly' = comportement historique (répète chaque mois au jour X) ;
            // 'once' = alerte PONCTUELLE à une date précise (ex : 31/10/2027).
            $table->string('recurrence', 10)->default('monthly')->after('day');
            $table->date('once_date')->nullable()->after('recurrence');
        });
    }

    public function down(): void
    {
        Schema::table('commitments', function (Blueprint $table) {
            $table->dropColumn(['recurrence', 'once_date']);
        });
    }
};
