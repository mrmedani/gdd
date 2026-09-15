<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // ex : Bail local bureaux
            $table->string('party')->nullable(); // contrepartie : propriétaire, fournisseur…
            $table->date('start_date');
            $table->date('end_date'); // déclenche l'alerte J-3
            $table->text('notes')->nullable();
            // Alerte déjà envoyée ? (dedup par ligne : le cron quotidien ne renvoie
            // pas le même rappel si deux exécutions tombent le même jour)
            $table->boolean('reminder_sent_at')->default(false);
            $table->foreignId('created_by')->constrained('users'); // destinataire des alertes
            $table->timestamps();

            $table->index(['end_date', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
