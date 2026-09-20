<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_closures', function (Blueprint $table) {
            $table->decimal('incomes', 15, 2)->default(0)->after('gains');
        });

        // Rattrapage : recalcule les entrées des clôtures existantes depuis la balance
        // (balance = gains + incomes - expenses - investments  =>  incomes = balance - gains + expenses + investments)
        $closures = \Illuminate\Support\Facades\DB::table('monthly_closures')->get();
        foreach ($closures as $c) {
            $incomes = (float) $c->balance - (float) $c->gains + (float) $c->expenses + (float) ($c->investments ?? 0);
            \Illuminate\Support\Facades\DB::table('monthly_closures')->where('id', $c->id)->update(['incomes' => round(max(0, $incomes), 2)]);
        }
    }

    public function down(): void
    {
        Schema::table('monthly_closures', function (Blueprint $table) {
            $table->dropColumn('incomes');
        });
    }
};
