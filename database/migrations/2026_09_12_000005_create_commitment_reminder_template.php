<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('whatsapp_message_templates')->updateOrInsert(
            ['type' => 'commitment_reminder'],
            [
                'label_fr' => 'Rappel d\'échéance',
                'label_ar' => 'تذكير بالاستحقاق',
                'message_fr' => "⏰ Rappel d'échéance\n──────────────\n📌 {label}\n📅 Échéance : {due_date} ({due_in})\n💰 Montant habituel : {amount} {currency}\n🏢 {company_name}",
                'message_ar' => "⏰ تذكير بالاستحقاق\n──────────────\n📌 {label}\n📅 الاستحقاق : {due_date} ({due_in})\n💰 المبلغ المعتاد : {amount} {currency}\n🏢 {company_name}",
                'variables' => json_encode(['label', 'due_date', 'due_in', 'amount', 'currency', 'company_name']),
                'enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('whatsapp_message_templates')->where('type', 'commitment_reminder')->delete();
    }
};
