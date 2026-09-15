<?php

namespace App\Domains\AI\Support;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Treasury\Models\Income;
use App\Domains\Alerts\Models\Commitment;
use App\Domains\Contracts\Models\Contract;
use Illuminate\Support\Facades\Cache;

/**
 *Invalidateur du contexte et des caches tools de l'assistant IA.
 *
 * RÈGLE : toute mutation d'une donnée financière ou contractuelle DOIT appeler
 * aiFlush() — sinon l'assistant répond avec un contexte de 60 s maximum en retard.
 * Ex : le gérant saisi une dépense « maintenant » → pose une question à l'IA illico
 * → sans invalidation, la réponse donne « 0 DZD » jusqu'à expiration du cache.
 *
 * Les observateurs appèles déjà (ExpenseObserver, IncomeObserver…) : ce helper
 * centralise l'appel pour éviter les oublis dans l'ajout d'un nouveau module.
 */
class AiCacheInvalidator
{
    /**
     * Purge TOUT ce que l'assistant a mis en cache (contexte + tools + sessions
     * ouvertes) pour l'utilisateur courant — ou globalement si $userId est null
     * (certains caches clés n'incluent pas le user).
     */
    public static function invalidateAll(): void
    {
        // 1. contexte statique : clé composite currency+locale+permProfile → on
        //    ne connaît ni tous les combos ni le locale exact — la purge grossière
        //    par préfixe est la seule fiable. Cache::remember() n'offre pas delete
        //    par préfixe ngetClientOriginal : DatabaseCacheStore aussi. On purge par
        //    requête SQL directe sur la table cache (cache.php driver DB utilisé).
        $driver = config('cache.default');
        if (in_array($driver, ['database', 'file'])) {
            if ($driver === 'database') {
                \DB::table('cache')->where('key', 'like', 'ai_expense_context:%')->delete();
                \DB::table('cache')->where('key', 'like', 'ai_tool:%')->delete();
            } else {
                // file store : pas de delete wildcard — utiliser Cache::forget
                // pour chaque workflow. On sauvegarde la liste du ctx doré
                // (experience les combos currency × locale sont bornés, 3 chacun) :
                foreach (['fr', 'ar', 'en'] as $locale) {
                    foreach (['T', '-', 'TI', 'T-E', '-IE'] as $profile) {
                        foreach (['MAD', 'DZD'] as $cur) {
                            Cache::forget("ai_expense_context:{$cur}:{$locale}:{$profile}");
                        }
                    }
                }
                // tools : clef personnelles userId : les lister n'est pas possible sans sql.
                // On choisit de purger TOUTES les clés via cache clear (coût : invalidation full)
                \Artisan::call('cache:clear', ['--quiet' => true]);
            }
        } else {
            \Artisan::call('cache:clear', ['--quiet' => true]);
        }
    }

    /**
     * Purge ciblée suite à une mutation d'EXPENSE — invalide le contexte et les tools
     * qui renvoient des chiffres de dépenses (période courante recalculée).
     */
    public static function invalidateExpenses(): void
    {
        self::invalidateAll();
    }

    /** Purge ciblée INCOME (incomes_summary, executiveSummary, etc.). */
    public static function invalidateIncomes(): void
    {
        self::invalidateAll();
    }

    /** Purge ciblée CONTRAT — rappeler pour invalidateAll (tools + cache). */
    public static function invalidateContracts(): void
    {
        self::invalidateAll();
    }

    /** Purge ciblée ENGAGEMENT. */
    public static function invalidateCommitments(): void
    {
        self::invalidateAll();
    }

    /** Purge ciblée INVESTISSEMENT. */
    public static function invalidateInvestments(): void
    {
        self::invalidateAll();
    }
}
