<?php

namespace App\Domains\AI\Http\Controllers;

use App\Domains\AI\Tools\ExpenseTools;
use App\Domains\Settings\Models\Setting;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;

class ChatbotController
{
    /** Nombre max de messages conserves (15 echanges). */
    protected int $maxHistory = 30;

    /** Duree de conservation de la conversation (24 h). */
    protected int $ttlSeconds = 86400;

    /** Longueur max d'un message utilisateur. */
    protected int $maxMessageLength = 2000;

    public function historyKey(Request $request): string
    {
        return 'ai_chat_history:user:' . ($request->user()?->id ?? $request->ip());
    }

    /**
     * Personnalite active (prompt editable via /settings, fallback Djafer par defaut).
     * Partagee entre le ChatbotController et la route du widget (/ai-chat).
     */
    public static function activePersonality(): string
    {
        return trim((string) Setting::get('ai_personality', '')) !== ''
            ? (string) Setting::get('ai_personality')
            : static::defaultPersonality();
    }

    public static function defaultPersonality(): string
    {
        return <<< 'TXT'
Tu t'appelles Djafer, l'assistant financier de Chronorex Express.
Ton caractère : professionnel, chaleureux et concis. Tu vouvoies le gérant.
Tu es proactif : si tu remarques une anomalie dans les données (déficit, hausse anormale d'une catégorie), tu la signales brièvement.
Tu utilises au maximum 1 emoji par réponse, jamais dans les tableaux.
Tu ne donnes jamais d'opinion sur les décisions business : tu présentes les faits et les chiffres.
TXT;
    }

    /**
     * Nom de l'assistant extrait du prompt de personnalite ("Tu t'appelles X" / "Je m'appelle X").
     * Sert a la salutation du widget pour qu'elle reflete TOUJOURS la personnalite active.
     */
    public static function assistantName(): string
    {
        if (preg_match('/(?:Tu t\'appelles|Je m\'appelle|Votre nom est)\s+([\p{L}\p{N}\- ]{2,30})/ui', static::activePersonality(), $m)) {
            return trim($m[1]);
        }
        return __('ai.title');
    }

    public function __invoke(Request $request)
    {
        $message = trim((string) $request->input('message', ''));
        if ($message === '') {
            return Response::json(['error' => __('ai.empty_message')], 422);
        }
        // Limite de longueur : un collage geant gonflerait le cache et casserait l'appel API
        $message = mb_substr($message, 0, $this->maxMessageLength);

        // Rate limit : 20 messages / minute par utilisateur
        $key = 'chatbot:' . ($request->user()?->id ?? $request->ip());
        if (RateLimiter::tooManyAttempts($key, 20)) {
            return Response::json(['error' => __('ai.rate_limited')], 429);
        }
        RateLimiter::hit($key, 60);

        // Historique stocke en CACHE (table cache, via CACHE_STORE=database), PAS en session :
        //   - la session fichier est verroulee pendant la requete -> avec l'historique en session,
        //     TOUTE navigation du user gelait pendant l'appel Gemini (25-75 s)
        //   - la session expire a SESSION_LIFETIME (120 min) -> conversation perdue trop tot
        //   - le cache est partage entre onglets/appareils pour le meme utilisateur
        $historyKey = $this->historyKey($request);

        // Verrou atomique court pour serialiser les lectures/écritures concurrentes (2 onglets)
        $lock = Cache::lock('ai_chat_lock:' . $historyKey, 5);
        try {
            $lock->block(3);
            $history = (array) Cache::get($historyKey, []);
        } catch (\Illuminate\Contracts\Cache\LockTimeoutException) {
            $history = (array) Cache::get($historyKey, []);
        }

        $context = array_slice($history, -10);
        $context[] = ['role' => 'user', 'content' => $message];

        // L'assistant repond dans la LANGUE DU PROFIL utilisateur (ar/fr/en)
        $langRule = match ($request->user()?->locale) {
            'ar' => "Réponds toujours en arabe.",
            'en' => "Always respond in English.",
            default => "Réponds en français.",
        };

        // PERSONNALITE : prompt editable depuis /settings (Setting 'ai_personality').
        // Seule la partie CARACTERE est editable ; les regles techniques restent codees en dur
        // (impossibles a casser en editant le prompt dans l'UI).
        $personality = static::activePersonality();

        // Regles IMMUABLES (format + integrite des donnees) — non editables depuis l'UI
        $rules = "RÈGLES ABSOLUES (non négociables) : "
            . "Ne jamais inventer de chiffres ; utilise uniquement les données ci-dessous. "
            . "Les données chiffrées ci-dessous sont la SEULE source de vérité, recalculées À CHAQUE MESSAGE. "
            . "Si un ancien message de cette conversation dit que des données ne sont pas disponibles, "
            . "cette information est OBSOLÈTE : utilise toujours les données fraîches ci-dessous. "
            . "Les données couvrent la période ACTUELLE et les 6 périodes précédentes "
            . "(chaque période va du 21 d'un mois au 20 du mois suivant). "
            . "Si une question porte sur une période plus ancienne, dis-le clairement. "
            . "FORMAT : quand tu listes des montants, catégories ou comparaisons (plus de 2 éléments), "
            . "utilise un TABLEAU Markdown (| col | col | avec |---|---|). Un tableau par sujet. "
            . "Termine par une phrase courte de synthèse.";

        $system = $personality . "\n\n"
            . "Le gérant avec qui tu parles s'appelle " . ($request->user()?->name ?? 'Utilisateur') . ".\n"
            . $langRule . "\n"
            . $rules . "\n\n"
            . "DONNÉES RÉELLES DE LA PLATEFORME :\n"
            . (new ExpenseTools())->buildContext();

        // LIBERE le verrou de session AVANT l'appel lent a Gemini (le driver 'file' garde un
        // flock tant que la session n'est pas sauvegardee ; save() le relâche immédiatement,
        // sinon toute requete suivante du user bloque jusqu'a la fin de la reponse IA).
        $request->session()->save();

        $service = new GeminiService();
        $reply = $service->chat($context, $system);

        // Re-ecriture de l'historique sous verrou (max 15 echanges)
        try {
            $lock->block(3);
            $history = (array) Cache::get($historyKey, []);
        } catch (\Illuminate\Contracts\Cache\LockTimeoutException) {
            // garde l'historique lu avant l'appel
        }
        $history[] = ['role' => 'user', 'content' => $message];
        $history[] = ['role' => 'assistant', 'content' => $reply];
        Cache::put($historyKey, array_slice($history, -$this->maxHistory), $this->ttlSeconds);
        optional($lock)->release();

        return Response::json(['reply' => $reply]);
    }

    public function clear(Request $request)
    {
        Cache::forget($this->historyKey($request));
        // Purge aussi l'ancien stockage session (migration douce des conversations existantes)
        $request->session()->forget('ai_chat_history');
        return Response::json(['ok' => true]);
    }
}
