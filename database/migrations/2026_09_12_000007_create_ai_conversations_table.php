<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Titre auto = premier message user tronque ; editable plus tard si besoin
            $table->string('title')->nullable();
            // Conversation complète : [{role, content}, ...] — une session = une ligne
            $table->json('messages');
            $table->timestamp('last_activity')->index();
            $table->timestamps();

            $table->index(['user_id', 'last_activity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
