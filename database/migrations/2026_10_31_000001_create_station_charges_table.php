<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_charges', function (Blueprint $table) {
            $table->id();
            $table->string('station_name');          // ex : STATION ORAN, STATION SETIF
            $table->string('period', 7);             // label période (Y-m), même convention que la clôture
            $table->decimal('amount', 15, 2);        // commission de la période
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['station_name', 'period']); // 1 commission par station par période
            $table->index('period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_charges');
    }
};
