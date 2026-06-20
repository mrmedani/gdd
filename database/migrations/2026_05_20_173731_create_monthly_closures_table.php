<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monthly_closures', function (Blueprint $table) {
            $table->id();
            $table->string('month', 7)->unique(); // YYYY-MM
            $table->decimal('gains', 15, 2)->default(0);
            $table->decimal('expenses', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->foreignId('closed_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_closures');
    }
};
