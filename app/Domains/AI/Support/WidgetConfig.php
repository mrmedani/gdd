<?php

namespace App\Domains\AI\Support;

use App\Domains\Settings\Models\Setting;

/**
 * Configuration du widget IA lue depuis /settings (modifiable a chaud par le gerant).
 * Un seul point de lecture : le layout (iframe), la route /ai-chat et le shell.
 */
class WidgetConfig
{
    public static function get(): array
    {
        return [
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
        ];
    }
}
