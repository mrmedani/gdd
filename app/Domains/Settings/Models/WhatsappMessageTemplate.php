<?php

namespace App\Domains\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessageTemplate extends Model
{
    protected $fillable = [
        'type',
        'label_fr',
        'label_ar',
        'message_fr',
        'message_ar',
        'variables',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'enabled' => 'boolean',
        ];
    }

    public static function forType(string $type): ?self
    {
        return static::where('type', $type)->where('enabled', true)->first();
    }

    public function format(array $data, string $locale = 'fr'): string
    {
        $message = $locale === 'ar' ? $this->message_ar : $this->message_fr;

        $replacements = [];
        foreach ($data as $key => $value) {
            $replacements['{' . $key . '}'] = (string) $value;
        }

        return strtr($message, $replacements);
    }

    /**
     * NORMALISE un template : la liste « variables » doit refléter EXACTEMENT
     * les placeholders réellement présents dans les messages (sinon la page
     * /settings/whatsapp-messages crie « variables absentes » à tort et les
     * chips mentent) + le branding Société/seuil quand listé mais oublié.
     */
    public static function normalize(array $t): array
    {
        $fr = (string) ($t['message_fr'] ?? '');
        $ar = (string) ($t['message_ar'] ?? '');
        $vars = array_values((array) ($t['variables'] ?? []));

        preg_match_all('/\{([a-z_]+)\}/', $fr, $mFr);
        preg_match_all('/\{([a-z_]+)\}/', $ar, $mAr);
        $used = array_values(array_unique(array_merge($mFr[1], $mAr[1])));

        // Lignes de branding listées mais oubliées dans le texte
        if (in_array('company_name', $vars, true) && ! in_array('company_name', $used, true)) {
            $fr = rtrim($fr) . "\n🏢 {company_name}";
            $ar = rtrim($ar) . "\n🏢 {company_name}";
            $used[] = 'company_name';
        }
        if (in_array('user', $vars, true) && ! in_array('user', $used, true) && ! str_contains($fr, "{user}")) {
            // action utilisateur : l'empreinte est exigée (cohérence « 👤 Par »)
            $fr = rtrim($fr) . "\n👤 Par : {user}";
            $ar = rtrim($ar) . "\n👤 بواسطة : {user}";
            $used[] = 'user';
        }
        if (in_array('threshold', $vars, true) && ! in_array('threshold', $used, true)) {
            $fr = rtrim($fr) . "\n🔔 Seuil : {threshold} {currency}";
            $ar = rtrim($ar) . "\n🔔 حد التنبيه : {threshold} {currency}";
            $used[] = 'threshold';
        }

        // Liste finale = ordre d'origine des variables utilisées + toute placeholder
        // du message qui ne serait pas listée (auto-ajout, chips toujours vraies)
        $ordered = [];
        foreach ($vars as $v) {
            if (in_array($v, $used, true) && ! in_array($v, $ordered, true)) {
                $ordered[] = $v;
            }
        }
        foreach ($used as $v) {
            if (! in_array($v, $ordered, true)) {
                $ordered[] = $v;
            }
        }

        return array_merge($t, [
            'message_fr' => $fr,
            'message_ar' => $ar,
            'variables' => $ordered,
        ]);
    }
}
