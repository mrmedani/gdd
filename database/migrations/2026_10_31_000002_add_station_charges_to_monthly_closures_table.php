<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_closures', function (Blueprint $table) {
            // Ligne séparée « Commissions stations » — déduite de la caisse mais
            // PAS enregistrée dans /expenses (choix produit : ne pas polluer les
            // dépenses avec des transfers internes de commissions).
            $table->decimal('station_charges', 15, 2)->default(0)->after('investments');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_closures', function (Blueprint $table) {
            $table->dropColumn('station_charges');
        });
    }
};
