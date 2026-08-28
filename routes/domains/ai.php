<?php

use App\Domains\AI\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// Widget du chatbot rendu dans un shell minimal (iframe-isolé, sans layout de l'app).
// PAS de middleware 'auth' sur cette route : le middleware session/StartSession peut lever
// une MissingAppKeyException sur un cookie chiffre avec une ancienne APP_KEY (500 fige dans
// le cache PWA, impossible a purger pour l'utilisateur). La garde auth est faite DANS le
// closure ; en cas d'echec, on rend le shell en mode degrade (200) plutot qu'un 500.
Route::get('/ai-chat', function () {
    try {
        $userId = auth()->id();
        if (!$userId) {
            // Non authentifie : shell en mode degrade (le JS affichera l'invite login)
            return response()->view('ai.widget-shell', [
                'chatHistory' => [],
                'greeting' => __('ai.greeting'),
                'assistantName' => __('ai.title'),
                'authRequired' => true,
            ], 200);
        }

        // Historique stocke en CACHE (24 h, par utilisateur) -> survit aux refreshs/navigations
        $key = 'ai_chat_history:user:' . $userId;
        $history = (array) \Illuminate\Support\Facades\Cache::get($key, []);
        // Migration douce : si une ancienne conversation existe encore en session, on la transpose
        if (empty($history)) {
            $old = (array) session()->get('ai_chat_history', []);
            if (!empty($old)) {
                $history = $old;
                \Illuminate\Support\Facades\Cache::put($key, $history, 86400);
                session()->forget('ai_chat_history');
            }
        }

        // Salutation refletant la PERSONNALITE ACTIVE : le nom est extrait du prompt
        // (defaut "Djafer", ou le nom defini dans /settings). Utilise le prenom du gerant.
        $assistantName = ChatbotController::assistantName();
        $userName = explode(' ', trim(auth()->user()->name ?? ''))[0];
        $greeting = __('ai.greeting_named', ['name' => $assistantName, 'user' => $userName]);
        $authRequired = false;
    } catch (\Throwable $e) {
        // Degrade proprement : log complet + shell minimal au lieu du 500
        \Illuminate\Support\Facades\Log::error('AI widget degraded', [
            'error' => $e->getMessage(),
            'file'  => $e->getFile() . ':' . $e->getLine(),
        ]);
        $history = [];
        $greeting = __('ai.greeting');
        $assistantName = __('ai.title');
        $authRequired = true;
    }

    return view('ai.widget-shell', [
        'chatHistory' => $history,
        'assistantName' => $assistantName,
        'greeting' => $greeting,
        'authRequired' => $authRequired,
    ]);
})->name('ai.chat');

// Endpoint API appelé par le widget (la clé Gemini reste côté serveur)
Route::middleware(['auth'])->group(function () {
    Route::post('/api/chatbot', [ChatbotController::class, '__invoke'])->name('api.chatbot');
    Route::post('/api/chatbot/clear', [ChatbotController::class, 'clear'])->name('api.chatbot.clear');
});
