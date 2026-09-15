<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('whatsapp_message_templates')) {
            return;
        }

        // Template WhatsApp du rappel d'expiration de contrat (module /contracts)
        DB::table('whatsapp_message_templates')->updateOrInsert(
            ['type' => 'contract_expiry'],
            [
                'label_fr' => "Expiration de contrat",
                'label_ar' => "انتهاء عقد",
                'message_fr' => "📄 Expiration de contrat\n──────────────\n📌 {title}\n👤 Contrepartie : {party}\n📅 Échéance : {end_date} ({due_in})\n🏢 {company_name}",
                'message_ar' => "📄 انتهاء عقد\n──────────────\n📌 {title}\n👤 الطرف الآخر : {party}\n📅 التاريخ : {end_date} ({due_in})\n🏢 {company_name}",
                'variables' => json_encode(['title', 'party', 'end_date', 'due_in', 'company_name']),
                'enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('whatsapp_message_templates')) {
            return;
        }
        DB::table('whatsapp_message_templates')->where('type', 'contract_expiry')->delete();
    }
};
