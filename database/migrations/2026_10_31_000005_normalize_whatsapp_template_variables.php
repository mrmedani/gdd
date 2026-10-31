<?php

use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * FIX « Variables absentes » : les listes variables[] divergeaient des
 * placeholders réellement présents dans les messages (ex : high_expense
 * listait {threshold}/{company_name} non utilisés). Conséquences :
 *   - la page /settings/whatsapp-messages criait à tort le badge orange
 *   - les chips proposaient des variables inexistantes dans le texte
 * Ce patch applique WhatsappMessageTemplate::normalize() (source de vérité
 * unique) sur chaque ligne : label Société/seuil ajoutés aux messages listés
 * mais oubliés, listes variables[] alignées sur les placeholders réels.
 * Idempotent : re-passer ne change plus rien.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('whatsapp_message_templates')->get();
        foreach ($rows as $row) {
            $normalized = WhatsappMessageTemplate::normalize([
                'type' => $row->type,
                'message_fr' => $row->message_fr,
                'message_ar' => $row->message_ar,
                'variables' => json_decode($row->variables ?? '[]', true) ?: [],
            ]);

            DB::table('whatsapp_message_templates')->where('id', $row->id)->update([
                'message_fr' => $normalized['message_fr'],
                'message_ar' => $normalized['message_ar'],
                'variables' => json_encode($normalized['variables']),
            ]);
        }
    }

    public function down(): void
    {
        // Irreversible de façon saine : on ne retire pas le branding ajouté.
    }
};
