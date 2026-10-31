<?php

namespace Database\Seeders;

use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Database\Seeder;

/**
 * TEMPLATES COMMISSIONS STATIONS (5 états : created / updated / deleted /
 * paid / unpaid). Idempotent — n'écrase jamais un template personnalisé en prod.
 */
class StationChargeTemplateSeeder extends Seeder
{
    public static function defaults(): array
    {
        $states = [
            'created' => ['🆕 Nouvelle commission station', 'عمولة محطة جديدة'],
            'updated' => ['✏️ Commission station modifiée', 'تم تعديل عمولة محطة'],
            'deleted' => ['🗑️ Commission station supprimée', 'تم حذف عمولة محطة'],
            'paid'    => ['💰 Commission station réglée', 'تم تسجيل عمولة المحطة'],
            'unpaid'  => ['↩️ Commission station remise en impayé', 'أُعيدت عمولة المحطة إلى غير مدفوعة'],
        ];

        $vars = ['station', 'period', 'amount', 'currency', 'user', 'company_name'];

        $out = [];
        foreach ($states as $state => [$lfr, $lar]) {
            $prefix = match ($state) {
                'created' => '🆕 Nouvelle commission station',
                'updated' => '✏️ Commission station modifiée',
                'deleted' => '🗑️ Commission station supprimée',
                'paid' => '💰 Commission station réglée',
                default => '↩️ Commission station remise en impayé',
            };
            $prefixAr = match ($state) {
                'created' => '🆕 عمولة محطة جديدة',
                'updated' => '✏️ تم تعديل عمولة محطة',
                'deleted' => '🗑️ تم حذف عمولة محطة',
                'paid' => '💰 تم تسجيل عمولة المحطة',
                default => '↩️ أُعيدت عمولة المحطة إلى غير مدفوعة',
            };
            $out[] = [
                'type' => 'station_charge_' . $state,
                'label_fr' => $lfr,
                'label_ar' => $lar,
                'message_fr' => $prefix . "\n──────────────\n🏢 Station : {station}\n📆 Période : {period}\n💰 Montant : {amount} {currency}\n👤 Par : {user}\n🏢 {company_name}",
                'message_ar' => $prefixAr . "\n──────────────\n🏢 المحطة : {station}\n📆 الفترة : {period}\n💰 المبلغ : {amount} {currency}\n👤 بواسطة : {user}\n🏢 {company_name}",
                'variables' => $vars,
            ];
        }
        return $out;
    }

    public function run(): void
    {
        foreach (self::defaults() as $template) {
            $template = WhatsappMessageTemplate::normalize($template);
            WhatsappMessageTemplate::firstOrCreate(
                ['type' => $template['type']],
                $template
            );
        }
    }
}
