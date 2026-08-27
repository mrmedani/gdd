<?php

namespace Database\Seeders;

use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Database\Seeder;

class WhatsappMessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'type' => 'expense_created',
                'label_fr' => 'Nouvelle dépense',
                'label_ar' => 'مصروف جديد',
                'message_fr' => "🆕 Nouvelle dépense\n──────────────\n📝 {description}\n💰 Montant : {amount} {currency}\n📂 Catégorie : {category}\n📅 Date : {date}\n💳 Paiement : {payment_method}\n🏢 {company_name}",
                'message_ar' => "🆕 مصروف جديد\n──────────────\n📝 {description}\n💰 المبلغ : {amount} {currency}\n📂 الفئة : {category}\n📅 التاريخ : {date}\n💳 الدفع : {payment_method}\n🏢 {company_name}",
                'variables' => ['description', 'amount', 'currency', 'category', 'date', 'payment_method', 'company_name'],
            ],
            [
                'type' => 'expense_modified',
                'label_fr' => 'Dépense modifiée',
                'label_ar' => 'تم تعديل المصروف',
                'message_fr' => "✏️ Dépense modifiée\n──────────────\n📝 {description}\n💰 Montant : {amount} {currency}\n📂 Catégorie : {category}\n📅 Date : {date}\n💳 Paiement : {payment_method}\n🏢 {company_name}",
                'message_ar' => "✏️ تم تعديل المصروف\n──────────────\n📝 {description}\n💰 المبلغ : {amount} {currency}\n📂 الفئة : {category}\n📅 التاريخ : {date}\n💳 الدفع : {payment_method}\n🏢 {company_name}",
                'variables' => ['description', 'amount', 'currency', 'category', 'date', 'payment_method', 'company_name'],
            ],
            [
                'type' => 'expense_deleted',
                'label_fr' => 'Dépense supprimée',
                'label_ar' => 'تم حذف المصروف',
                'message_fr' => "🗑️ Dépense supprimée\n──────────────\n📝 {description}\n💰 Montant : {amount} {currency}\n📅 Date : {date}\n💳 Paiement : {payment_method}\n🏢 {company_name}",
                'message_ar' => "🗑️ تم حذف المصروف\n──────────────\n📝 {description}\n💰 المبلغ : {amount} {currency}\n📅 التاريخ : {date}\n💳 الدفع : {payment_method}\n🏢 {company_name}",
                'variables' => ['description', 'amount', 'currency', 'date', 'payment_method', 'company_name'],
            ],
            [
                'type' => 'high_expense',
                'label_fr' => 'Dépenses élevées détectées',
                'label_ar' => 'تم اكتشاف مصروفات مرتفعة',
                'message_fr' => "⚠️ Dépenses élevées détectées\n──────────────\n💰 Total : {total} {currency}\n📋 Opérations : {count}\n📅 Date : {date}",
                'message_ar' => "⚠️ تم اكتشاف مصروفات مرتفعة\n──────────────\n💰 الإجمالي : {total} {currency}\n📋 العمليات : {count}\n📅 التاريخ : {date}",
                'variables' => ['total', 'currency', 'count', 'date', 'threshold', 'company_name'],
            ],
            [
                'type' => 'salary_reminder',
                'label_fr' => 'Rappel de paie',
                'label_ar' => 'تذكير بالرواتب',
                'message_fr' => "💰 Rappel de paie\n──────────────\n👥 Employés : {count}\n💵 Total : {total} {currency}\n📅 Date : {date}",
                'message_ar' => "💰 تذكير بالرواتب\n──────────────\n👥 الموظفون : {count}\n💵 الإجمالي : {total} {currency}\n📅 التاريخ : {date}",
                'variables' => ['count', 'total', 'currency', 'date', 'company_name'],
            ],
            [
                'type' => 'monthly_closure',
                'label_fr' => 'Clôture de période',
                'label_ar' => 'إغلاق الفترة',
                'message_fr' => "🔒 Clôture de période\n──────────────\n📆 Période : {period}\n📈 Gains : {gains} {currency}\n📉 Dépenses : {expenses} {currency}\n⚖️ Solde : {balance} {currency}",
                'message_ar' => "🔒 إغلاق الفترة\n──────────────\n📆 الفترة : {period}\n📈 الإيرادات : {gains} {currency}\n📉 المصروفات : {expenses} {currency}\n⚖️ الرصيد : {balance} {currency}",
                'variables' => ['period', 'gains', 'currency', 'expenses', 'balance', 'company_name'],
            ],
            [
                'type' => 'daily_report',
                'label_fr' => 'Rapport périodique',
                'label_ar' => 'تقرير دوري',
                'message_fr' => "📊 Rapport {period_label}\n──────────────\n💵 Dépenses : {total} {currency}\n📋 Opérations : {count}\n📅 {period_start} → {period_end}",
                'message_ar' => "📊 تقرير {period_label}\n──────────────\n💵 المصروفات : {total} {currency}\n📋 العمليات : {count}\n📅 {period_start} → {period_end}",
                'variables' => ['period_label', 'total', 'currency', 'count', 'period_start', 'period_end', 'company_name'],
            ],
            [
                'type' => 'deficit_increased',
                'label_fr' => 'Augmentation du manque en caisse',
                'label_ar' => 'زيادة العجز في الصندوق',
                'message_fr' => "⚠️ Augmentation du manque en caisse\n──────────────\n📆 Période : {period}\n📈 Augmentation : {increase} {currency}\n💰 Nouveau total : {new_total} {currency}",
                'message_ar' => "⚠️ زيادة العجز في الصندوق\n──────────────\n📆 الفترة : {period}\n📈 الزيادة : {increase} {currency}\n💰 الإجمالي الجديد : {new_total} {currency}",
                'variables' => ['period', 'increase', 'currency', 'new_total', 'company_name'],
            ],
            [
                'type' => 'deficit_deducted',
                'label_fr' => 'Réduction du manque en caisse',
                'label_ar' => 'انخفاض العجز في الصندوق',
                'message_fr' => "✅ Réduction du manque en caisse\n──────────────\n📆 Période : {period}\n📉 Déduction : {deduction} {currency}\n💰 Restant : {remaining} {currency}",
                'message_ar' => "✅ انخفاض العجز في الصندوق\n──────────────\n📆 الفترة : {period}\n📉 الخصم : {deduction} {currency}\n💰 المتبقي : {remaining} {currency}",
                'variables' => ['period', 'deduction', 'currency', 'remaining', 'company_name'],
            ],
            [
                'type' => 'deficit_covered',
                'label_fr' => 'Manque en caisse entièrement comblé',
                'label_ar' => 'تم تغطية العجز بالكامل',
                'message_fr' => "🎉 Manque en caisse entièrement comblé !\n──────────────\n📆 Période : {period}\n💰 Le solde du manque en caisse est désormais à zéro.",
                'message_ar' => "🎉 تم تغطية العجز في الصندوق بالكامل!\n──────────────\n📆 الفترة : {period}\n💰 رصيد العجز أصبح صفراً.",
                'variables' => ['period', 'company_name'],
            ],
            [
                'type' => 'income_created',
                'label_fr' => 'Nouvelle entrée d\'argent',
                'label_ar' => 'إيراد جديد',
                'message_fr' => "💰 Nouvelle entrée d'argent\n──────────────\n📝 {source_name}\n🏷️ Type : {source_type}\n💵 Montant : {amount} {currency}\n📅 Date : {date}\n🏢 {company_name}",
                'message_ar' => "💰 إيراد جديد\n──────────────\n📝 {source_name}\n🏷️ النوع : {source_type}\n💵 المبلغ : {amount} {currency}\n📅 التاريخ : {date}\n🏢 {company_name}",
                'variables' => ['source_name', 'source_type', 'amount', 'currency', 'date', 'company_name'],
            ],
            [
                'type' => 'income_modified',
                'label_fr' => 'Entrée d\'argent modifiée',
                'label_ar' => 'تم تعديل الإيراد',
                'message_fr' => "✏️ Entrée d'argent modifiée\n──────────────\n📝 {source_name}\n🏷️ Type : {source_type}\n💵 Montant : {amount} {currency}\n📅 Date : {date}\n🏢 {company_name}",
                'message_ar' => "✏️ تم تعديل الإيراد\n──────────────\n📝 {source_name}\n🏷️ النوع : {source_type}\n💵 المبلغ : {amount} {currency}\n📅 التاريخ : {date}\n🏢 {company_name}",
                'variables' => ['source_name', 'source_type', 'amount', 'currency', 'date', 'company_name'],
            ],
            [
                'type' => 'income_deleted',
                'label_fr' => 'Entrée d\'argent supprimée',
                'label_ar' => 'تم حذف الإيراد',
                'message_fr' => "🗑️ Entrée d'argent supprimée\n──────────────\n📝 {source_name}\n🏷️ Type : {source_type}\n💵 Montant : {amount} {currency}\n📅 Date : {date}\n🏢 {company_name}",
                'message_ar' => "🗑️ تم حذف الإيراد\n──────────────\n📝 {source_name}\n🏷️ النوع : {source_type}\n💵 المبلغ : {amount} {currency}\n📅 التاريخ : {date}\n🏢 {company_name}",
                'variables' => ['source_name', 'source_type', 'amount', 'currency', 'date', 'company_name'],
            ],
        ];

        foreach ($templates as $template) {
            WhatsappMessageTemplate::firstOrCreate(
                ['type' => $template['type']],
                $template
            );
        }
    }
}
