<?php

use App\Domains\AI\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Widget du chatbot rendu dans un shell minimal (iframe-isolé, sans layout de l'app)
    Route::get('/ai-chat', function () {
        // Historique stocke en CACHE (24 h, par utilisateur) -> survit aux refreshs/navigations
        // et ne bloque pas la session fichier pendant l'appel IA
        $key = 'ai_chat_history:user:' . auth()->id();
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
        return view('ai.widget-shell', ['chatHistory' => $history]);
    })->name('ai.chat');

    // Endpoint API appelé par le widget (la clé Gemini reste côté serveur)
    Route::post('/api/chatbot', ChatbotController::class)->name('api.chatbot');
    Route::post('/api/chatbot/clear', [ChatbotController::class, 'clear'])->name('api.chatbot.clear');
});
