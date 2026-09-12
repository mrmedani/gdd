<?php

namespace App\Services;

use App\Domains\Settings\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service gratuit pour Gemini 2.0 Flash (Google AI Studio).
 * Clé API stockée côté serveur via Setting::set('gemini_api_key', ...) — jamais exposée au frontend.
 * Free tier : généreux, pas de carte bancaire requise.
 */
class GeminiService
{
    // Alias auto-mis-a-jour par Google : pas de deprecation surprise (gemini-2.0-flash est devenu 404 en 2026).
    protected string $model = 'gemini-flash-latest';
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
    /** Temperature (0 = robot factuel, 1 = creatif). Defaut 0.2, overridable via /settings. */
    protected float $temperature = 0.2;

    public function __construct(protected ?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?: Setting::get('gemini_api_key', '');
        // Modele overridable via /settings (Setting gemini_model, UI dans l'onglet IA)
        $this->model = Setting::get('gemini_model', '') ?: 'gemini-flash-latest';
        // Temperature overridable via /settings (controle creativite vs rigueur factuelle)
        $this->temperature = min(1.0, max(0.0, (float) Setting::get('ai_temperature', 0.2)));
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Envoie un tableau de messages et retourne la réponse texte.
     * @param array $messages [['role'=>'user'|'model','content'=>'...'], ...]
     * @param array $systemInstruction texte système optionnel
     * @param array|null $tools déclarations function calling optionnelles — si fourni,
     *        la boucle tool-call est activée (max 2 tours) : Gemini peut demander un tool,
     *        on exécute via $toolHandler(name, args) puis on renvoie les résultats.
     */
    public function chat(array $messages, string $systemInstruction = '', ?array $tools = null, ?\Closure $toolHandler = null): string
    {
        if (!$this->isConfigured()) {
            return __('ai.not_configured');
        }

        $contents = [];
        foreach ($messages as $msg) {
            $contents[] = [
                'role'    => $msg['role'] === 'assistant' ? 'model' : 'user',
                'parts'   => [['text' => $msg['content']]],
            ];
        }

        $payload = ['contents' => $contents];
        if ($systemInstruction !== '') {
            $payload['systemInstruction'] = ['parts' => [['text' => $systemInstruction]]];
        }
        // ANTI-HALLUCINATION : temperature basse par defaut (reponses factuelles ancrees
        // aux donnees). Overridable via /settings (ai_temperature) pour qui veut plus de style.
        $payload['generationConfig'] = [
            'temperature'      => $this->temperature,
            'topP'             => 0.9,
            'maxOutputTokens'  => 2048,
        ];
        // FUNCTION CALLING : declarations de tools. Gemini repondra OU avec du texte final,
        // OU avec functionCall(s) — gérés dans la boucle ci-dessous.
        if ($tools !== null && $tools !== []) {
            $payload['tools'] = [['functionDeclarations' => $tools]];
        }

        // Boucle tool-call : max 2 tours (protection quota — un message normal = 1 appel,
        // avec 1 croisement de données max = 3 appels serveur au total, rare).
        $toolRounds = 0;
        $maxToolRounds = 2;

        $url = $this->baseUrl . '/';

        try {
            // Chaine de fallback : le free tier Gemini renvoie souvent 503 "high demand"
            // ou 404 (modele supprime) sur le modele principal ; on tente les modeles
            // secondaires connus-disponibles en 2026 avant d'echouer.
            // NB: gemini-2.5-flash a ete supprime pour les nouveaux utilisateurs (404 en 2026).
            $models = array_unique([
                $this->model,
                'gemini-3.6-flash',
                'gemini-flash-lite-latest',
            ]);

            $lastStatus = 0;
            $lastBody = '';
            while (true) {
            foreach ($models as $model) {
                // 60s par modele max : laisse plus de marge au free tier Gemini souvent
                // surcharge (503 "high demand") au lieu de timeout trop vite. Pire cas ~3 min.
                $response = Http::timeout(60)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    // Fix cURL error 60 (WAMP sans CA bundle) : pointer Guzzle sur le bundle officiel.
                    // Ne PAS mettre verify=false (trou de sécurité) — voir skill gdd curl-ssl-cacert-fix.
                    ->withOptions(['verify' => base_path('resources/certs/cacert.pem')])
                    ->post($url . $model . ':generateContent?key=' . $this->apiKey, $payload);

                if ($response->successful()) {
                    $json = $response->json();
                    // TOOL CALL demandé ? (uniquement si la boucle tools est active)
                    $calls = $json['candidates'][0]['content']['parts'][0]['functionCall'] ?? null
                        ?? ($json['candidates'][0]['content']['parts'] ?? []);
                    if ($tools !== null && $toolHandler !== null && $toolRounds < $maxToolRounds && !empty($calls)) {
                        $gotToolCall = false;
                        $parts = $json['candidates'][0]['content']['parts'] ?? [];
                        $functionParts = [];
                        foreach ($parts as $part) {
                            if (isset($part['functionCall']['name'])) {
                                $fname = (string) $part['functionCall']['name'];
                                $fargs = $part['functionCall']['args'] ?? [];
                                $result = ($toolHandler)($fname, $fargs);
                                // REJOUER la part model EXACTEMENT telle que reçue, y compris
                                // thought_signature (Gemini 3 exige de renvoyer la signature
                                // du tool call d'origine — sinon 400 INVALID_ARGUMENT).
                                $payload['contents'][] = [
                                    'role' => 'model',
                                    'parts' => [$part],
                                ];
                                $payload['contents'][] = [
                                    'role' => 'user',
                                    'parts' => [['functionResponse' => [
                                        'name' => $fname,
                                        'response' => ['result' => $result],
                                    ]]],
                                ];
                                $gotToolCall = true;
                            }
                        }
                        if ($gotToolCall) {
                            $toolRounds++;
                            // On rejoue avec le MÊME modèle (le fallback réapparaît si rate limité).
                            continue 2; // re-boucle sur $models
                        }
                    }
                    // Réponse texte finale
                    return $this->extractText($response->json());
                }

                $lastStatus = $response->status();
                $lastBody = $response->body();

                // 429 (quota du MODELE epuise) : on CONTINUE la chaine — chaque modele a son
                // propre bucket de quota, le suivant peut etre libre. (Bug constate : le
                // break sur 4xx faisait echouer la chaine alors que flash-lite etait libre.)
                if ($response->status() === 429) {
                    continue;
                }
                // 404 = modele supprime/indisponible (ex: gemini-2.5-flash retire en 2026)
                // -> on essaie le modele de fallback suivant.
                if ($response->status() === 404) {
                    continue;
                }
                // Autres 4xx (cle invalide 401, requete refusee 403) : inutile d'essayer
                // les autres modeles — le probleme est la cle/la requete, pas le modele.
                if ($response->status() < 500) {
                    break;
                }
            }
            // while(true) : si on arrive ici, on a épuisé tous les modèles du fallback
            // sans tool call ni réponse — break la boucle externe (échec propre).
            break;
            }

            Log::warning('Gemini API error', ['status' => $lastStatus, 'body' => $lastBody]);
            // Message informatif selon la cause (timeout frequent sur le free tier)
            if ($lastStatus === 0) {
                return __('ai.timeout');
            }
            // 429 = quota Google depasse (pas notre rate limiter local, celui de Google)
            if ($lastStatus === 429) {
                return __('ai.quota_exceeded');
            }
            return __('ai.api_error');
        } catch (\Throwable $e) {
            Log::error('Gemini request failed', ['error' => $e->getMessage()]);
            // cURL error 28 = timeout ; autre = erreur reseau/SSL
            if (str_contains($e->getMessage(), 'timed out') || str_contains($e->getMessage(), 'cURL error 28')) {
                return __('ai.timeout');
            }
            return __('ai.api_error');
        }
    }

    protected function extractText(array $data): string
    {
        $candidates = $data['candidates'] ?? [];
        if (empty($candidates)) {
            // Pas de candidat : bloque par les filtres de securite (promptFeedback) ou erreur
            $block = $data['promptFeedback']['blockReason'] ?? null;
            if ($block) {
                Log::warning('Gemini response blocked', ['reason' => $block]);
                return __('ai.no_response');
            }
            return __('ai.no_response');
        }
        $parts = $candidates[0]['content']['parts'] ?? [];
        $text = '';
        foreach ($parts as $part) {
            $text .= $part['text'] ?? '';
        }
        // Reponse tronquee (MAX_TOKENS) ou bloquee (SAFETY) : on log pour diagnostic,
        // et on rend le texte partiel s'il existe plutot qu'un echec muet.
        $finish = $candidates[0]['finishReason'] ?? null;
        if ($finish && $finish !== 'STOP') {
            Log::warning('Gemini finishReason anormal', ['finishReason' => $finish, 'text_len' => mb_strlen($text)]);
        }
        return $text ?: __('ai.no_response');
    }
}
