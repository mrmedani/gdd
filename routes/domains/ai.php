<?php

use App\Domains\AI\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// Widget du chatbot rendu dans un shell minimal (iframe-isolé, sans layout de l'app).
// PAS de middleware 'auth' sur cette route : le middleware session/StartSession peut lever
// une MissingAppKeyException sur un cookie chiffre avec une ancienne APP_KEY (500 fige dans
// le cache PWA, impossible a purger pour l'utilisateur). La garde auth est faite DANS le
// closure ; en cas d'echec, on rend le shell en mode degrade (200) plutot qu'un 500.
Route::get('/ai-chat', function () {
    // Kill-switch : widget desactive -> 404 propre (pas seulement masque dans le layout)
    if (!\App\Domains\AI\Support\WidgetConfig::get()['enabled']) {
        abort(404);
    }
    try {
        $userId = auth()->id();
        if (!$userId) {
            // Non authentifie : shell en mode degrade (le JS affichera l'invite login)
            return response()->view('ai.widget-shell', [
                'chatHistory'   => [],
                'greeting'      => __('ai.greeting'),
                'assistantName' => __('ai.title'),
                'authRequired'  => true,
                'aiCfg'         => \App\Domains\AI\Support\WidgetConfig::get(),
            ], 200);
        }

        // SESSIONS : la conversation active vit dans ai_conversations (via AiSessionService),
        // MÊME source que ChatbotController::append — sinon le widget-shell chargée le
        // vieux format cache et créait une nouvelle session à chaque navigation de page.
        $history = \App\Domains\AI\Support\AiSessionService::activeMessages($userId);
        $activeId = \App\Domains\AI\Support\AiSessionService::activeId($userId);

        // Nom : le Setting ai_name a PRIORITE (garanti), sinon extraction du prompt de personnalite
        $cfg = \App\Domains\AI\Support\WidgetConfig::get();
        $assistantName = $cfg['name'] !== '' ? $cfg['name'] : ChatbotController::assistantName();
        $userName = explode(' ', trim(auth()->user()->name ?? ''))[0];

        // Salutation : montrée UNIQUEMENT si la session active est VIDE,
        // sinon la conversation active reprend telle quelle.
        $greeting = '';
        if (empty($history)) {
            $greeting = $cfg['greeting'] !== ''
                ? str_replace([':user', ':name'], [$userName, $assistantName], $cfg['greeting'])
                : __('ai.greeting_named', ['name' => $assistantName, 'user' => $userName]);
        }

        return view('ai.widget-shell', [
            'chatHistory'   => $history,
            'assistantName' => $assistantName,
            'greeting'      => $greeting,
            'authRequired'  => false,
            'aiCfg'         => $cfg,
        ]);
    } catch (\Throwable $e) {
        // Degrade proprement : log complet + shell minimal au lieu du 500
        \Illuminate\Support\Facades\Log::error('AI widget degraded', [
            'error' => $e->getMessage(),
            'file'  => $e->getFile() . ':' . $e->getLine(),
        ]);
        return response()->view('ai.widget-shell', [
            'chatHistory'   => [],
            'greeting'      => __('ai.greeting'),
            'assistantName' => __('ai.title'),
            'authRequired'  => true,
            'aiCfg'         => \App\Domains\AI\Support\WidgetConfig::get(),
        ], 200);
    }
})->name('ai.chat');

// Endpoint API appelé par le widget (la clé Gemini reste côté serveur)
// Kill-switch : widget desactive -> 404 (l'API ne reste pas appelable en direct)
Route::middleware(['auth', \App\Domains\AI\Http\Middleware\EnsureAiWidgetEnabled::class])->group(function () {
    Route::post('/api/chatbot', [ChatbotController::class, '__invoke'])->name('api.chatbot');
    Route::post('/api/chatbot/clear', [ChatbotController::class, 'clear'])->name('api.chatbot.clear');
    Route::post('/api/chatbot/new-session', [ChatbotController::class, 'newSession'])->name('api.chatbot.new');
    Route::get('/api/chatbot/sessions', [ChatbotController::class, 'listSessions'])->name('api.chatbot.sessions');
    Route::post('/api/chatbot/sessions/{id}/open', [ChatbotController::class, 'openSession'])->whereNumber('id')->name('api.chatbot.open');
    Route::delete('/api/chatbot/sessions/{id}', [ChatbotController::class, 'deleteSession'])->whereNumber('id')->name('api.chatbot.delete');
});
