<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * EMPREINTE UTILISATEUR : toutes les notifications WhatsApp déclenchées par une
 * action utilisateur portent « 👤 Par : {user} » (comme les commissions stations).
 * Ce patch de DONNÉES est prudent :
 *   - ❌ n'écrase AUCUN template personnalisé (regarde si '{user}' est déjà dedans)
 *   - ajoute la ligne uniquement si absente (FR : « 👤 Par : {user} », AR : « 👤 بواسطة : {user} »)
 *   - enregistre 'user' dans variables[] pour l'éditeur de /settings/whatsapp-messages
 * Types des étendues : expense_created/modified/deleted, income_created/modified/deleted,
 * monthly_closure (automated : high_expense, salary_reminder, daily_report, deficit_*
 * et commitment_reminder restent SANS empreinte — ce sont des tâches programmées, pas des
 * opérations utilisateur).
 */
return new class extends Migration
{
    public function up(): void
    {
        $types = [
            'expense_created', 'expense_modified', 'expense_deleted',
            'income_created', 'income_modified', 'income_deleted',
            'monthly_closure',
        ];

        $rows = DB::table('whatsapp_message_templates')->whereIn('type', $types)->get();
        foreach ($rows as $row) {
            $update = [];

            if (str_contains((string) $row->message_fr, '{user}') === false) {
                $update['message_fr'] = rtrim((string) $row->message_fr) . "\n👤 Par : {user}";
            }
            if (str_contains((string) $row->message_ar, '{user}') === false) {
                $update['message_ar'] = rtrim((string) $row->message_ar) . "\n👤 بواسطة : {user}";
            }

            $vars = json_decode((string) $row->variables, true);
            if (is_array($vars) && ! in_array('user', $vars, true)) {
                $vars[] = 'user';
                $update['variables'] = json_encode(array_values($vars));
            }

            if ($update !== []) {
                DB::table('whatsapp_message_templates')->where('id', $row->id)->update($update);
            }
        }
    }

    public function down(): void
    {
        // inverse : retire la ligne {user} + la variable (tests/new installs uniquement)
        $types = [
            'expense_created', 'expense_modified', 'expense_deleted',
            'income_created', 'income_modified', 'income_deleted',
            'monthly_closure',
        ];
        $rows = DB::table('whatsapp_message_templates')->whereIn('type', $types)->get();
        foreach ($rows as $row) {
            $update = [];
            $update['message_fr'] = preg_replace('/\n👤 Par : \{user\}/u', '', (string) $row->message_fr);
            $update['message_ar'] = preg_replace('/\n👤 بواسطة : \{user\}/u', '', (string) $row->message_ar);
            $vars = json_decode((string) $row->variables, true);
            if (is_array($vars)) {
                $update['variables'] = json_encode(array_values(array_diff($vars, ['user'])));
            }
            DB::table('whatsapp_message_templates')->where('id', $row->id)->update($update);
        }
    }
};
