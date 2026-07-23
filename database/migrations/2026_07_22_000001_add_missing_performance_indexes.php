<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try { Schema::table('expenses', fn(Blueprint $t) => $t->index('date')); } catch (\Exception) {}
        try { Schema::table('expenses', fn(Blueprint $t) => $t->index('category_key')); } catch (\Exception) {}
        try { Schema::table('salary_advances', fn(Blueprint $t) => $t->index('status')); } catch (\Exception) {}
        try { Schema::table('salary_payments', fn(Blueprint $t) => $t->index('month')); } catch (\Exception) {}
        try { Schema::table('salary_payments', fn(Blueprint $t) => $t->index('year')); } catch (\Exception) {}
    }

    public function down(): void
    {
        Schema::table('expenses', fn(Blueprint $t) => $t->dropIndex(['date']));
        Schema::table('expenses', fn(Blueprint $t) => $t->dropIndex(['category_key']));
        Schema::table('salary_advances', fn(Blueprint $t) => $t->dropIndex(['status']));
        Schema::table('salary_payments', fn(Blueprint $t) => $t->dropIndex(['month']));
        Schema::table('salary_payments', fn(Blueprint $t) => $t->dropIndex(['year']));
    }
};
