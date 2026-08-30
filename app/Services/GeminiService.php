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
     * Envoie un tableau de messages (role => content) et retourne la réponse texte.
     * @param array $messages [['role'=>'user'|'model','content'=>'...'], ...]
     * @param array $systemInstruction texte système optionnel
     */
    public function chat(array $messages, string $systemInstruction = ''): string
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

        $url = $this->baseUrl . '/';

        try {
            // Chaine de fallback : le free tier Gemini renvoie souvent 503 "high demand"
            // sur le modele principal ; on tente les modeles secondaires avant d'echouer.
            $models = array_unique([
                $this->model,
                'gemini-2.5-flash',
                'gemini-flash-lite-latest',
            ]);

            $lastStatus = 0;
            $lastBody = '';
            foreach ($models as $model) {
                // 25s par modele max : 3 modeles x 25s = ~75s pire cas, acceptable ;
                // 60s par modele faisait attendre jusqu'a 3 minutes avant l'erreur (cURL 28).
                $response = Http::timeout(25)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    // Fix cURL error 60 (WAMP sans CA bundle) : pointer Guzzle sur le bundle officiel.
                    // Ne PAS mettre verify=false (trou de sécurité) — voir skill gdd curl-ssl-cacert-fix.
                    ->withOptions(['verify' => base_path('resources/certs/cacert.pem')])
                    ->post($url . $model . ':generateContent?key=' . $this->apiKey, $payload);

                if ($response->successful()) {
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
                // Autres 4xx (cle invalide, requete refusee) : inutile d'essayer les autres modeles
                if ($response->status() < 500) {
                    break;
                }
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
