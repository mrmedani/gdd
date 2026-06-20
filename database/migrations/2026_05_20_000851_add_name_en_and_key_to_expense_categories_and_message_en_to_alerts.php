<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name_fr');
            $table->string('key')->nullable()->after('name_en');
        });

        DB::table('expense_categories')->orderBy('id')->each(function ($cat) {
            $names = [
                1 => ['name_en' => 'Salaries', 'key' => 'salaries'],
                2 => ['name_en' => 'Fuel', 'key' => 'fuel'],
                3 => ['name_en' => 'Rent', 'key' => 'rent'],
                4 => ['name_en' => 'Internet', 'key' => 'internet'],
                5 => ['name_en' => 'Electricity', 'key' => 'electricity'],
                6 => ['name_en' => 'Vehicle Maintenance', 'key' => 'vehicle_maintenance'],
                7 => ['name_en' => 'Supplies & Purchases', 'key' => 'supplies'],
                8 => ['name_en' => 'Advertising', 'key' => 'advertising'],
                9 => ['name_en' => 'Other Expenses', 'key' => 'other'],
            ];
            if (isset($names[$cat->id])) {
                DB::table('expense_categories')->where('id', $cat->id)->update($names[$cat->id]);
            }
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->text('message_en')->nullable()->after('message_fr');
        });
    }

    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'key']);
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn('message_en');
        });
    }
};
