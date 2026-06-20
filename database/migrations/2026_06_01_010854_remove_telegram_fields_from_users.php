<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['telegram_chat_id', 'notify_telegram', 'telegram_link_token'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_chat_id', 50)->nullable()->after('notify_whatsapp');
            $table->boolean('notify_telegram')->default(false)->after('telegram_chat_id');
            $table->string('telegram_link_token', 64)->unique()->nullable()->after('notify_telegram');
        });
    }
};
