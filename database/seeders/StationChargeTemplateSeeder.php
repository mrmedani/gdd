<?php

namespace Database\Seeders;

use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Database\Seeder;

/**
 * UNIVERS TEMPLATES COMMISSIONS STATIONS : ajoute les 3 templates pris en
 * charge par StationChargeNotification s'ils ne sont pas encore en base
 * (idempotent — en prod, un run du seeder via db:seed --class suffit).
 */
class StationChargeTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'type' => 'station_charge_created',
                'label_fr' => 'Nouvelle commission station',
                'label_ar' => 'عمولة محطة جديدة',
                'message_fr' => "🆕 Nouvelle commission station\n──────────────\n🏢 Station : {station}\n📆 Période : {period}\n💰 Montant : {amount} {currency}\n👤 Par : {user}\n🏢 {company_name}",
                'message_ar' => "🆕 عمولة محطة جديدة\n──────────────\n🏢 المحطة : {station}\n📆 الفترة : {period}\n💰 المبلغ : {amount} {currency}\n👤 بواسطة : {user}\n🏢 {company_name}",
                'variables' => ['station', 'period', 'amount', 'currency', 'user', 'company_name'],
            ],
            [
                'type' => 'station_charge_updated',
                'label_fr' => 'Commission station modifiée',
                'label_ar' => 'تم تعديل عمولة محطة',
                'message_fr' => "✏️ Commission station modifiée\n──────────────\n🏢 Station : {station}\n📆 Période : {period}\n💰 Montant : {amount} {currency}\n👤 Par : {user}\n🏢 {company_name}",
                'message_ar' => "✏️ تم تعديل عمولة محطة\n──────────────\n🏢 المحطة : {station}\n📆 الفترة : {period}\n💰 المبلغ : {amount} {currency}\n👤 بواسطة : {user}\n🏢 {company_name}",
                'variables' => ['station', 'period', 'amount', 'currency', 'user', 'company_name'],
            ],
            [
                'type' => 'station_charge_deleted',
                'label_fr' => 'Commission station supprimée',
                'label_ar' => 'تم حذف عمولة محطة',
                'message_fr' => "🗑️ Commission station supprimée\n──────────────\n🏢 Station : {station}\n📆 Période : {period}\n💰 Montant : {amount} {currency}\n👤 Par : {user}\n🏢 {company_name}",
                'message_ar' => "🗑️ تم حذف عمولة محطة\n──────────────\n🏢 المحطة : {station}\n📆 الفترة : {period}\n💰 المبلغ : {amount} {currency}\n👤 بواسطة : {user}\n🏢 {company_name}",
                'variables' => ['station', 'period', 'amount', 'currency', 'user', 'company_name'],
            ],
        ];

        foreach ($templates as $t) {
            if (WhatsappMessageTemplate::where('type', $t['type'])->exists()) {
                continue; // idempotent : ne jamais écraser un template personnalisé en prod
            }
            WhatsappMessageTemplate::create($t);
        }
    }
}
