<?php

namespace App\Domains\AI\Support;

use App\Domains\Settings\Models\Setting;

/**
 * Configuration du widget IA lue depuis /settings (modifiable a chaud par le gerant).
 * Un seul point de lecture : le layout (iframe), la route /ai-chat et le shell.
 * Les valeurs "profondes" (temperature, modele, periodes, limites) sont lues ici aussi
 * pour que le ChatbotController et le GeminiService restent configurables sans code.
 */
class WidgetConfig
{
    /** Cache statique 60s : evite 11+ lectures Setting par rendu de page. */
    protected static ?array $cached = null;
    protected static ?int $cachedAt = null;

    public static function get(): array
    {
        // Cache memoire 60s par requete PHP-FPM (le process meurt apres, pas de stalite croisee)
        if (self::$cached !== null && self::$cachedAt !== null && (time() - self::$cachedAt) < 60) {
            return self::$cached;
        }
        self::$cached = [
            'enabled'    => (bool) Setting::get('ai_widget_enabled', true),
            'name'       => trim((string) Setting::get('ai_name', '')),
            'emoji'      => trim((string) Setting::get('ai_emoji', '')) ?: '🤖',
            'greeting'   => trim((string) Setting::get('ai_greeting', '')),
            'suggestions' => array_values(array_filter(array_map(
                'trim',
                explode("\n", str_replace("\r", '', (string) Setting::get('ai_suggestions', '')))
            ))),
            'palette'    => (string) Setting::get('ai_palette', 'indigo'),
            'position'   => (string) Setting::get('ai_position', 'right'),
            'size'       => (string) Setting::get('ai_window_size', 'normal'),
            'autoOpen'   => (bool) Setting::get('ai_auto_open', false),
            'showSuggestions' => (bool) Setting::get('ai_show_suggestions', true),
            'offlineMessage' => trim((string) Setting::get('ai_offline_message', '')),
            // --- Parametres avances (controle profond) ---
            'temperature'    => min(1.0, max(0.0, (float) Setting::get('ai_temperature', 0.2))),
            'model'          => trim((string) Setting::get('gemini_model', '')) ?: 'gemini-flash-latest',
            'historyPeriods' => min(12, max(1, (int) Setting::get('ai_history_periods', 6))),
            'maxExchanges'   => min(50, max(5, (int) Setting::get('ai_max_exchanges', 15))),
            'rateLimit'      => min(60, max(5, (int) Setting::get('ai_rate_limit', 20))),
            'ttlHours'       => min(168, max(1, (int) Setting::get('ai_ttl_hours', 24))),
        ];
        self::$cachedAt = time();
        return self::$cached;
    }

    /** Invalide le cache memoire (appele quand /settings sauvegarde les parametres IA). */
    public static function flush(): void
    {
        self::$cached = null;
        self::$cachedAt = null;
    }
}
