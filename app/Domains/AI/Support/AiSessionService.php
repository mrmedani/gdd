<?php

namespace App\Domains\AI\Support;

use App\Domains\AI\Models\AiConversation;
use Illuminate\Support\Facades\Cache;

/**
 * Gestion des sessions de conversation IA.
 *
 * ARCHITECTURE :
 *  - "Session ACTIVE" = conversation en cours d'écriture, pointée par le cache
 *    `ai_active_conversation:user:{id}` (id nullable = nouvelle session pas encore créée).
 *  - Chaque session est une ligne `ai_conversations` (messages en JSON, titre auto).
 *  - "Nouvelle session" ARCHIVE la courante et vide le pointeur — l'historique n'est
 *    jamais perdu tant que la session a un contenu.
 *  - Migration douce : au premier accès, l'ancienne conversation cache
 *    (`ai_chat_history:user:{id}`) est archivée comme "Session 1".
 */
class AiSessionService
{
    public static function activeKey(int $userId): string
    {
        return 'ai_active_conversation:user:' . $userId;
    }

    /** Id de la conversation active (ou null si nouvelle session vierge). */
    public static function activeId(int $userId): ?int
    {
        $id = Cache::get(self::activeKey($userId));
        return is_numeric($id) ? (int) $id : null;
    }

    /**
     * Messages de la session active. Migration douce :
     * l'ancienne conversation cache est archivée au premier accès.
     */
    public static function activeMessages(int $userId): array
    {
        $activeId = self::activeId($userId);

        // Migration douce : ancien format cache -> première session archivée
        $legacy = Cache::get('ai_chat_history:user:' . $userId);
        if (!empty($legacy) && is_array($legacy)) {
            if ($activeId === null) {
                $conv = AiConversation::create([
                    'user_id' => $userId,
                    'title' => AiConversation::titleFromMessages($legacy),
                    'messages' => $legacy,
                    'last_activity' => now(),
                ]);
                Cache::put(self::activeKey($userId), $conv->id, 86400 * 7);
                // Purge l'ancien stockage (transposé)
                Cache::forget('ai_chat_history:user:' . $userId);
                return []; // session active = nouvelle vierge, l'ancienne est archivée
            }
        }

        if ($activeId === null) {
            return [];
        }
        $conv = AiConversation::where('user_id', $userId)->find($activeId);
        if (!$conv) {
            Cache::forget(self::activeKey($userId));
            return [];
        }
        return $conv->messages ?? [];
    }

    /**
     * Ajoute une paire user/assistant à la session active (créée si nécessaire).
     */
    public static function append(int $userId, string $userMsg, string $assistantMsg): void
    {
        $conv = self::ensureActive($userId);
        $messages = $conv->messages ?? [];
        // Évite les doublons si deux requêtes concurrentes (2 onglets) ajoutent
        // le même message user — le verrou serveur ne couvre pas ce cas.
        $last = end($messages);
        if ($last && ($last['role'] ?? '') === 'user' && ($last['content'] ?? '') === $userMsg) {
            return;
        }
        $messages[] = ['role' => 'user', 'content' => $userMsg];
        $messages[] = ['role' => 'assistant', 'content' => (string) $assistantMsg];
        $conv->update([
            'messages' => array_slice($messages, -100),
            'title' => $conv->title ?: AiConversation::titleFromMessages($messages),
            'last_activity' => now(),
        ]);
    }

    private static function ensureActive(int $userId): AiConversation
    {
        $id = self::activeId($userId);
        if ($id) {
            $conv = AiConversation::where('user_id', $userId)->find($id);
            if ($conv) return $conv;
            Cache::forget(self::activeKey($userId));
        }
        $conv = AiConversation::create([
            'user_id' => $userId,
            'title' => null,
            'messages' => [],
            'last_activity' => now(),
        ]);
        Cache::put(self::activeKey($userId), $conv->id, 86400 * 7);
        return $conv;
    }

    /** "Nouvelle session" : archive la courante (déjà persistée) et dépointe. */
    public static function startNew(int $userId): void
    {
        Cache::forget(self::activeKey($userId));
    }

    /** Liste des sessions archivées du user (max 30, plus récentes d'abord). */
    public static function listFor(int $userId): array
    {
        return AiConversation::where('user_id', $userId)
            ->orderByDesc('last_activity')
            ->limit(30)
            ->get(['id', 'title', 'last_activity'])
            ->toArray();
    }

    /** Ouvre une session en la définissant comme active. */
    public static function open(int $userId, int $conversationId): ?array
    {
        $conv = AiConversation::where('user_id', $userId)->find($conversationId);
        if (!$conv) return null;
        Cache::put(self::activeKey($userId), $conv->id, 86400 * 7);
        return $conv->messages ?? [];
    }

    /** Suppression définitive d'une session. Retourne true si supprimée. */
    public static function delete(int $userId, int $conversationId): bool
    {
        $activeId = self::activeId($userId);
        $deleted = AiConversation::where('user_id', $userId)
            ->where('id', $conversationId)->first();
        if (!$deleted) return false;
        $wasActive = ($activeId === $conversationId);
        $deleted->delete();
        if ($wasActive) {
            Cache::forget(self::activeKey($userId));
        }
        return true;
    }

    /** Purge les sessions VIDES (0 message) du user — nettoyage anti-ombre (bug « conversation fantôme »). */
    public static function purgeEmpty(int $userId): int
    {
        $count = 0;
        AiConversation::where('user_id', $userId)
            ->get()
            ->each(function (AiConversation $c) use (&$count) {
                if (empty($c->messages) || count($c->messages) === 0) {
                    $c->delete();
                    $count++;
                }
            });
        return $count;
    }

    /** Vide la session active uniquement (comportement de l'ancien bouton poubelle). */
    public static function clearActive(int $userId): void
    {
        $id = self::activeId($userId);
        if ($id) {
            AiConversation::where('user_id', $userId)->where('id', $id)->delete();
            Cache::forget(self::activeKey($userId));
        }
    }
}
