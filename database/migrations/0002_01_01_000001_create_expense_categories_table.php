<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_fr');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('expense_categories')->insert([
            ['name_ar' => 'الرواتب', 'name_fr' => 'Salaires'],
            ['name_ar' => 'الوقود', 'name_fr' => 'Carburant'],
            ['name_ar' => 'الإيجار', 'name_fr' => 'Loyer'],
            ['name_ar' => 'الإنترنت', 'name_fr' => 'Internet'],
            ['name_ar' => 'الكهرباء', 'name_fr' => 'Électricité'],
            ['name_ar' => 'صيانة المركبات', 'name_fr' => 'Entretien véhicules'],
            ['name_ar' => 'اللوازم والمشتريات', 'name_fr' => 'Fournitures et achats'],
            ['name_ar' => 'الإعلانات', 'name_fr' => 'Publicité'],
            ['name_ar' => 'مصاريف أخرى', 'name_fr' => 'Autres dépenses'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
