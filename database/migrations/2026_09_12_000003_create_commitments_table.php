<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commitments', function (Blueprint $table) {
            $table->id();
            $table->string('label'); // ex : Loyer, Internet, ECOTRACK
            $table->unsignedTinyInteger('day'); // jour du mois : 1-31
            $table->decimal('amount', 15, 2)->nullable(); // montant habituel (rappel)
            $table->unsignedTinyInteger('lead_days')->default(3); // alerter N jours avant
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commitments');
    }
};
