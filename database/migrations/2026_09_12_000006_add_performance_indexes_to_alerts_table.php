<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Comptage des non-lues + tri antéchronologique du dashboard (sondé toutes les 60s)
        try { Schema::table('alerts', fn (Blueprint $t) => $t->index(['is_read', 'created_at'])); } catch (\Exception) {}
        // Filtres par type dans le dashboard et les préférences utilisateurs
        try { Schema::table('alerts', fn (Blueprint $t) => $t->index('type')); } catch (\Exception) {}
    }

    public function down(): void
    {
        try { Schema::table('alerts', fn (Blueprint $t) => $t->dropIndex(['is_read', 'created_at'])); } catch (\Exception) {}
        try { Schema::table('alerts', fn (Blueprint $t) => $t->dropIndex(['type'])); } catch (\Exception) {}
    }
};
