<?php

namespace App\Domains\AI\Http\Middleware;

use App\Domains\AI\Support\WidgetConfig;
use Closure;
use Illuminate\Http\Request;

/**
 * Kill-switch du widget IA : si ai_widget_enabled = 0, /ai-chat et /api/chatbot
 * repondent 404 (la coupure d'urgence coupe VRAIMENT, pas seulement le masquage iframe).
 */
class EnsureAiWidgetEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if (!WidgetConfig::get()['enabled']) {
            abort(404);
        }
        return $next($request);
    }
}
